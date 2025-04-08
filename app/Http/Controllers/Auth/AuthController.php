<?php

namespace App\Http\Controllers\Auth;


use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\PasswordReset;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, Password, Validator};
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponses;

    // ✅ Register
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'is_buyer' => 'nullable|boolean',
            'is_seller' => 'nullable|boolean',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $verification_code = verificationCode(6);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'is_buyer' => $data['is_buyer'] ?? false,
            'is_seller' => $data['is_seller'] ?? false,
            'ver_code' => $verification_code,
            'password' => bcrypt($data['password']),
        ]);

        // Send verification email
        notify($user, 'email_verification', [
            'code' => $verification_code,
        ]);
        return $this->ok('Register successfully. Please verify your email address.', [
            'user' => new UserResource($user)
        ], 201);

    }

    // ✅ Login
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::query()->where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

//        if (!$user->hasVerifiedEmail()) {
//            return $this->error('Please verify your email address.', 403);
//        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->ok('Login successfully.', [
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    // ✅ Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->ok('Logout successfully.');
    }

    // ✅ Forgot Password
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::query()->where('email', $request->email)->first();

        if (!$user) {
            return $this->error('Couldn\'t find any account with this email');
        }

        PasswordReset::query()->where('email', $user->email)->delete();
        $code = verificationCode(6);
        $password = new PasswordReset();
        $password->email = $user->email;
        $password->token = $code;
        $password->created_at = now();
        $password->reset_code_expires_at = now()->addMinutes(15);
        $password->save();

//        $userIpInfo = getIpInfo();
//        $userBrowserInfo = osBrowser();
        notify($user, 'password_reset', [
            'code' => $code,
            'operating_system' => 'android',
            'browser' => 'chrome',
            'ip' => '192.88.0.0.1',
            'time' => 454,
        ]);

        $email = $user->email;
        return $this->ok('Verification code sent to mail', [
            'email' => $email,
        ]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'email' => 'required',
        ]);
        $code = $request->code;

        if (PasswordReset::where('token', $code)->where('email', $request->email)->count() != 1) {
            return $this->error('Verification code doesn\'t match');
        }

        return $this->ok('Reset code verified successfully.');
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $resetPassword = PasswordReset::where('token', $request->token)->where('email', $request->email)->delete();

        $user = User::query()->where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        notify($user, 'password_reset_done', [
            'operating_system' => 'android',
            'browser' => 'chrome',
            'ip' => '192.88.0.0.1',
            'time' => 454,
        ]);

        return $this->ok('Password changed successfully', [
            'user' => new UserResource($user),
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 403);
        }

        $user->update(['password' => bcrypt($request->new_password)]);

        return response()->json(['message' => 'Password changed successfully.']);
    }

    // ✅ Get Authenticated User
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'email' => 'required|email|exists:users',
        ]);

        $user = User::query()->where('email', $request->email)->first();

        if ($user->ver_code == $request->code) {
            $user->email_verified_at = now();
            $user->ver_code = null;
            $user->status = UserStatus::ACTIVE->value;
            $user->save();
            return $this->ok('Email verified successfully', new UserResource($user));
        }
        return $this->error('Verification code is incorrect');
    }

    public function resendVerificationCode(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        $user = User::query()->where('email', $request->email)->first();
        if ($user->ver_code == null) {
            $verification_code = verificationCode(6);
            $user->ver_code = $verification_code;
            $user->save();

        }
        $verification_code = $user->ver_code;

        notify($user, 'email_verification', [
            'code' => $verification_code,
        ]);
        return $this->ok('Verification code successfully sent.', []);
    }
}
