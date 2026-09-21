<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\GHNService;
use Illuminate\Http\Request;

class GHNController extends Controller
{
    /**
     * Lấy danh sách Tỉnh/Thành từ GHN
     */
    public function getProvinces(GHNService $ghn)
    {
        return response()->json($ghn->getProvinces());
    }

    /**
     * Lấy danh sách Quận/Huyện từ GHN theo Province ID
     */
    public function getDistricts(int $provinceId, GHNService $ghn)
    {
        return response()->json($ghn->getDistricts($provinceId));
    }

    /**
     * Lấy danh sách Phường/Xã từ GHN theo District ID
     */
    public function getWards(int $districtId, GHNService $ghn)
    {
        return response()->json($ghn->getWards($districtId));
    }

    /**
     * Tính phí giao hàng GHN trực tiếp
     */
    public function getShippingFee(Request $request, GHNService $ghn)
    {
        $request->validate([
            'to_district_id' => 'required|integer',
            'to_ward_code'   => 'required|string',
        ]);

        $cart = session('cart', []);
        $weight = collect($cart)->sum(
            fn ($item) => (int) ($item['weight'] ?? config('services.ghn.default_weight', 200))
            * (int) $item['quantity']
        );

        $fromDistrictId = (int) config('services.ghn.from_district_id', 1450);
        $toDistrictId   = (int) $request->to_district_id;
        $toWardCode     = (string) $request->to_ward_code;

        $feeResult = $ghn->calculateFee(array_merge([
            'from_district_id' => $fromDistrictId,
            'to_district_id'   => $toDistrictId,
            'to_ward_code'     => $toWardCode,
        ], $ghn->packageParameters($weight)));

        return response()->json($feeResult);
    }
}
