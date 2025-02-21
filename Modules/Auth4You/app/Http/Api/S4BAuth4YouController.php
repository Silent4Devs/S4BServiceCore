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
use Modules\Auth4You\Entities\S4BUserEntities;

class S4BAuth4YouController extends S4BBaseController
{
    // Registro de usuario
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $user = S4BUserEntities::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Generar token de acceso usando Passport
            $token = $user->createToken('Personal Access Token')->accessToken;

            $response = ['token' => $token, 'user' => $user];
            return $this->S4BSendResponse($response, 'Login exitoso');
        } catch (\Exception $e) {
            return $this->S4BSendError($e, ['error' => $e]);
        }
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
                return response()->json(['error' => 'Las credenciales no son correctas.'], 401);
            }

            $user = Auth::user();

            // Generar token de acceso con Passport
            $token = $user->createToken('Personal Access Token')->accessToken;

            $response = ['token' => $token, 'user' => $user];
            return $this->S4BSendResponse($response, 'Login exitoso');
        } catch (\Exception $e) {
            return $this->S4BSendError($e, ['error' => $e]);
        }
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    // Recuperar Contraseña
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Correo enviado.'])
            : response()->json(['message' => 'Error al enviar correo.'], 400);
    }
}
