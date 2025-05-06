<?php

namespace Modules\Auth4You\App\Http\Api;

use App\Http\Controllers\S4BBaseController;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Modules\Auth4You\Entities\S4BUserEntities;
use Modules\Auth4You\Mail\TwoFACodeMail;

class S4BAuth4YouController extends S4BBaseController
{
    // Registro de usuario
    public function register(Request $request)
    {
        try {
            $stripeKey = $request->header('STRIPE_SECRET');

            if (!$stripeKey) {
                return response()->json(['error' => 'Stripe API Key es requerida'], 400);
            }

            \Stripe\Stripe::setApiKey($stripeKey);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role_id' => 'required|exists:roles,id',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $user = S4BUserEntities::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id,
            ]);

            $stripeCustomer = \Stripe\Customer::create([
                'name'  => $user->name,
                'email' => $user->email,
            ]);

            $user->stripe_customer_id = $stripeCustomer->id;
            $user->save();

            $user->assignRole($request->role_id);

            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->accessToken;
            $expiration = $tokenResult->token->expires_at;

            $response = [
                'token' => $token,
                'expires_at' => $expiration,
                'user' => $user
            ];

            return $this->S4BSendResponse($response, 'Usuario registrado correctamente.');
        } catch (\Exception $e) {
            return $this->S4BSendError($e, ['error' => $e->getMessage()]);
        }
    }

    // Enable 2FA
    public function enable2FA(Request $request)
    {
        $user = $request->user();
        $user->otp_enabled = true;
        $user->save();

        return $this->S4BSendResponse([], '2FA activado correctamente.');
    }

    // Disable 2FA
    public function disable2FA(Request $request)
    {
        $user = $request->user();
        $user->otp_enabled = false;
        $user->save();

        return $this->S4BSendResponse([], '2FA desactivado correctamente.');
    }

    // Login
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (!Auth::attempt($credentials)) {
                return $this->S4BSendError($credentials, ['error' => 'Credenciales inválidas.'], 401);
            }

            $user = Auth::user();

            // Generar token de acceso con Passport
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->accessToken;
            $expiration = $tokenResult->token->expires_at;

            if ($user->otp_enabled) {
                $twoFACode = rand(100000, 999999);
                $user->otp_code = $twoFACode;
                $user->otp_expires_at = now()->addMinutes(10);
                $user->save();

                Mail::to($user->email)->send(new TwoFACodeMail($twoFACode));

                $response = [
                    'token' => $token,
                    'expires_at' => $expiration,
                    'user' => $user
                ];

                return $this->S4BSendResponse($response, 'Código 2FA enviado a su correo electrónico.');
            }

            $response = [
                'token' => $token,
                'expires_at' => $expiration,
                'user' => $user
            ];
            return $this->S4BSendResponse($response, 'Login exitoso');
        } catch (\Exception $e) {
            return $this->S4BSendError($e, ['error' => $e]);
        }
    }

    // Verify 2FA Code
    public function verify2FA(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);

        $user = Auth::user();

        if (!$user->otp_enabled) {
            return $this->S4BSendError([], ['error' => '2FA no está habilitado.'], 400);
        }

        if ($user->otp_code !== $request->code || now()->greaterThan($user->otp_expires_at)) {
            return $this->S4BSendError([], ['error' => 'Código 2FA inválido o expirado.'], 401);
        }

        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->accessToken;
        $expiration = $tokenResult->token->expires_at;

        $response = [
            'token' => $token,
            'expires_at' => $expiration,
            'user' => $user
        ];
        return $this->S4BSendResponse($response, '2FA verificado exitosamente.');
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->S4BSendResponse([], 'Sesión cerrada correctamente');
    }

    // Recuperar Contraseña
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? $this->S4BSendResponse(['message' => 'Correo enviado.'], 'Sesión cerrada correctamente')
            : $this->S4BSendError(['message' => 'Error al enviar correo.'], 400);
    }
}
