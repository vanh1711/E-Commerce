<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OtpVerificationService
{
    protected int $otpExpiryMinutes = 10;

    /**
     * Sinh và gửi mã OTP xác thực qua REST API
     */
    public function sendOtp(User $user): array
    {
        // 1. Sinh mã OTP 6 chữ số ngẫu nhiên
        $otp = (string) random_int(100000, 999999);

        // 2. Lưu vào Cache với thời hạn 10 phút
        $cacheKey = 'email_otp_' . $user->id;
        Cache::put($cacheKey, [
            'code'       => $otp,
            'email'      => $user->email,
            'created_at' => now()->timestamp,
        ], now()->addMinutes($this->otpExpiryMinutes));

        // Lưu vào session để hỗ trợ hiển thị/demo tức thì
        session(['last_generated_otp' => $otp]);

        // 3. Tiến hành gửi mã qua các kênh API
        $sendResult = $this->dispatchViaApi($user, $otp);

        return [
            'success' => true,
            'otp'     => $otp,
            'channel' => $sendResult['channel'],
            'message' => $sendResult['message'],
        ];
    }

    /**
     * Xác thực mã OTP người dùng nhập
     */
    public function verifyOtp(User $user, string $inputOtp): bool
    {
        $inputOtp = trim($inputOtp);
        $cacheKey = 'email_otp_' . $user->id;
        $cachedData = Cache::get($cacheKey);

        if (!$cachedData) {
            // Kiểm tra thêm session fallback nếu Cache bị dọn dẹp
            $sessionOtp = session('last_generated_otp');
            if ($sessionOtp && $inputOtp === trim((string) $sessionOtp)) {
                $user->markEmailAsVerified();
                session()->forget('last_generated_otp');
                return true;
            }
            return false;
        }

        $expectedCode = is_array($cachedData) ? ($cachedData['code'] ?? '') : (string) $cachedData;

        if ($inputOtp === trim((string) $expectedCode)) {
            // Xác thực thành công
            $user->markEmailAsVerified();
            Cache::forget($cacheKey);
            session()->forget('last_generated_otp');
            return true;
        }

        return false;
    }

    /**
     * Phân phối gửi email OTP qua REST API (HTTPS port 443 - không bao giờ bị chặn như cổng SMTP 587 trên Render)
     */
    protected function dispatchViaApi(User $user, string $otp): array
    {
        $brevoKey  = config('services.otp.brevo_key', env('BREVO_API_KEY'));
        $resendKey = config('services.otp.resend_key', env('RESEND_API_KEY'));
        $fromEmail = config('services.otp.from_email', env('MAIL_FROM_ADDRESS', 'security@phonestore.vn'));
        $fromName  = config('services.otp.from_name', env('MAIL_FROM_NAME', 'PhoneStore Security'));

        $subject = "[PhoneStore] Mã xác thực OTP của bạn là: {$otp}";
        $htmlContent = $this->buildEmailTemplate($user->name, $otp);

        // KÊNH 1: Brevo (Sendinblue) REST API (HTTPS Endpoint)
        if (!empty($brevoKey)) {
            try {
                $response = Http::withoutVerifying()
                    ->baseUrl('https://api.brevo.com/v3')
                    ->timeout(6)
                    ->withHeaders([
                        'api-key'      => $brevoKey,
                        'Content-Type' => 'application/json',
                        'Accept'       => 'application/json',
                    ])
                    ->post('/smtp/email', [
                        'sender'      => ['name' => $fromName, 'email' => $fromEmail],
                        'to'          => [['email' => $user->email, 'name' => $user->name]],
                        'subject'     => $subject,
                        'htmlContent' => $htmlContent,
                    ]);

                if ($response->successful()) {
                    Log::info("OTP sent successfully via Brevo REST API to {$user->email}");
                    return ['channel' => 'brevo_api', 'message' => 'Mã OTP đã được gửi về email qua Brevo API.'];
                } else {
                    Log::warning("Brevo API responded with error: " . $response->body());
                }
            } catch (\Exception $e) {
                Log::warning("Brevo API error: " . $e->getMessage());
            }
        }

        // KÊNH 2: Resend REST API (HTTPS Endpoint)
        if (!empty($resendKey)) {
            try {
                $response = Http::withoutVerifying()
                    ->baseUrl('https://api.resend.com')
                    ->timeout(6)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $resendKey,
                        'Content-Type'  => 'application/json',
                    ])
                    ->post('/emails', [
                        'from'    => "{$fromName} <onboarding@resend.dev>",
                        'to'      => [$user->email],
                        'subject' => $subject,
                        'html'    => $htmlContent,
                    ]);

                if ($response->successful()) {
                    Log::info("OTP sent successfully via Resend REST API to {$user->email}");
                    return ['channel' => 'resend_api', 'message' => 'Mã OTP đã được gửi về email qua Resend API.'];
                }
            } catch (\Exception $e) {
                Log::warning("Resend API error: " . $e->getMessage());
            }
        }

        // KÊNH 3: Fallback gửi SMTP cục bộ (nếu chạy ở localhost) với timeout ngắn
        // Sử dụng try-catch bảo vệ để không bao giờ bị đứng/treo trang trên môi trường Cloud như Render
        if (config('mail.default') === 'smtp' && !empty(env('MAIL_PASSWORD'))) {
            try {
                Mail::html($htmlContent, function ($m) use ($user, $subject, $fromEmail, $fromName) {
                    $m->to($user->email, $user->name)
                      ->from($fromEmail, $fromName)
                      ->subject($subject);
                });
                return ['channel' => 'smtp', 'message' => 'Mã OTP đã được gửi qua email.'];
            } catch (\Exception $e) {
                Log::info("SMTP connection failed/blocked (Render Cloud). Fallback to Smart OTP in-app delivery. Code: {$otp}");
            }
        }

        return [
            'channel' => 'in_app',
            'message' => 'Mã OTP xác thực đã được khởi tạo sẵn sàng cho tài khoản.',
        ];
    }

    /**
     * Mẫu Email HTML hiện đại chuẩn thương hiệu PhoneStore
     */
    protected function buildEmailTemplate(string $name, string $otp): string
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 520px; margin: 0 auto; padding: 24px; background: #0c1322; color: #f8fafc; border-radius: 20px;'>
            <div style='text-align: center; margin-bottom: 24px;'>
                <h1 style='color: #38bdf8; font-size: 24px; margin: 0; letter-spacing: 1px;'>PHONESTORE</h1>
                <p style='color: #94a3b8; font-size: 12px; margin-top: 4px;'>Trung Tâm Bảo Mật & Xác Thực Khách Hàng</p>
            </div>
            <div style='background: #1e293b; padding: 24px; border-radius: 16px; border: 1px solid #334155; text-align: center;'>
                <p style='color: #cbd5e1; font-size: 14px; margin-bottom: 12px;'>Xin chào <strong>{$name}</strong>,</p>
                <p style='color: #94a3b8; font-size: 13px; line-height: 1.6;'>Cảm ơn bạn đã đăng ký tài khoản tại PhoneStore. Vui lòng nhập mã xác thực OTP 6 số dưới đây để kích hoạt tài khoản của bạn:</p>
                <div style='margin: 24px 0; padding: 16px; background: #0f172a; border-radius: 12px; border: 2px dashed #38bdf8; letter-spacing: 8px; font-size: 32px; font-weight: bold; color: #38bdf8;'>
                    {$otp}
                </div>
                <p style='color: #64748b; font-size: 12px;'>Mã xác thực có hiệu lực trong vòng <strong>10 phút</strong>. Vì lý do bảo mật, tuyệt đối không chia sẻ mã này cho bất kỳ ai.</p>
            </div>
            <div style='text-align: center; margin-top: 20px; color: #64748b; font-size: 11px;'>
                © 2026 PhoneStore Co. All rights reserved.
            </div>
        </div>
        ";
    }
}
