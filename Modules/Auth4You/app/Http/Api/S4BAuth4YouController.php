<?php

namespace Modules\Auth4You\App\Http\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Auth4You\Entities\S4BUserEntities;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class S4BAuth4YouController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('auth4you::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('auth4you::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('auth4you::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('auth4you::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $user = S4BUserEntities::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['user' => $user, 'message' => 'Usuario registrado'], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = S4BUserEntities::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user], 200);
    }

    public function prueba(Request $request)
    {
        return response()->json(['token' => "ok", 'user' => "ok"], 200);
    }
}
