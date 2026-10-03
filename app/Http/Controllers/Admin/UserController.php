<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách người dùng & Tìm kiếm phân trang
     */
    public function index(Request $request)
    {
        $query = User::withCount('orders');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('role') && in_array($request->role, ['admin', 'user'])) {
            $query->where('role', $request->role);
        }

        // Lọc theo trạng thái Khóa / Hoạt động
        if ($request->filled('status')) {
            if ($request->status === 'locked') {
                $query->where('is_locked', true);
            } elseif ($request->status === 'active') {
                $query->where(function ($q) {
                    $q->where('is_locked', false)->orWhereNull('is_locked');
                });
            }
        }

        // Thống kê nhanh người dùng
        $stats = [
            'total'     => User::count(),
            'customers' => User::where(function($q) { $q->where('role', 'user')->orWhere('is_admin', 0); })->count(),
            'admins'    => User::where(function($q) { $q->where('role', 'admin')->orWhere('is_admin', 1); })->count(),
            'locked'    => User::where('is_locked', true)->count(),
        ];

        $users = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Form tạo tài khoản người dùng mới
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Lưu người dùng mới vào CSDL
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,user',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
        ], [
            'name.required'     => 'Vui lòng nhập họ và tên.',
            'email.required'    => 'Vui lòng nhập địa chỉ email.',
            'email.email'       => 'Email không đúng định dạng.',
            'email.unique'      => 'Email này đã được sử dụng bởi người dùng khác.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'role.required'     => 'Vui lòng chọn vai trò.',
            'role.in'           => 'Vai trò chỉ có thể là Quản trị viên (admin) hoặc Khách hàng (user).',
        ]);

        $role = $request->role;

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $role,
            'is_admin'  => ($role === 'admin' ? 1 : 0),
            'is_locked' => $request->boolean('is_locked'),
            'phone'     => $request->phone,
            'address'   => $request->address,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Thêm người dùng mới thành công.');
    }

    /**
     * Hiển thị chi tiết hồ sơ & Lịch sử đơn hàng của người dùng
     */
    public function show(User $user)
    {
        $user->load(['orders' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Form chỉnh sửa thông tin người dùng
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Cập nhật thông tin người dùng
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:admin,user',
            'password' => 'nullable|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
        ], [
            'name.required'  => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email'    => 'Email không đúng định dạng.',
            'email.unique'   => 'Email này đã được sử dụng bởi người dùng khác.',
            'password.min'   => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'role.required'  => 'Vui lòng chọn vai trò.',
            'role.in'        => 'Vai trò không hợp lệ.',
        ]);

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'role'      => $request->role,
            'is_admin'  => ($request->role === 'admin' ? 1 : 0),
            'phone'     => $request->phone,
            'address'   => $request->address,
        ];

        if ($request->has('is_locked')) {
            $data['is_locked'] = $request->boolean('is_locked');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', "Đã cập nhật thông tin người dùng [{$user->name}] thành công.");
    }

    /**
     * Khóa hoặc Mở khóa tài khoản người dùng
     */
    public function toggleStatus(User $user)
    {
        // 1. Không cho phép tự khóa chính tài khoản đang đăng nhập
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Bạn không thể tự khóa tài khoản của chính mình.');
        }

        // 2. Không cho phép khóa tài khoản Super Admin chính
        if (in_array($user->email, ['admin@gmail.com', 'admin@example.com'])) {
            return back()->with('error', 'Không thể khóa tài khoản Quản trị viên tối cao (Super Admin).');
        }

        // Chuyển đổi trạng thái khóa
        $user->is_locked = !$user->is_locked;
        $user->save();

        $actionText = $user->is_locked ? 'khóa' : 'mở khóa';
        $statusText = $user->is_locked ? 'bị tạm khóa (không thể đăng nhập)' : 'hoạt động bình thường';

        return back()->with('success', "Đã {$actionText} tài khoản [{$user->name}] ({$user->email}) thành công. Tài khoản hiện {$statusText}.");
    }

    /**
     * Xóa tài khoản người dùng
     */
    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'Bạn không thể tự xóa tài khoản của chính mình.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "Đã xóa người dùng [{$userName}] thành công.");
    }
}
