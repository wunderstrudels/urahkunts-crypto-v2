<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller {
    public function login(Request $request) {
        $validated = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (! $token = auth()->attempt($validated)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function refresh(Request $request) {
        return $this->respondWithToken(auth()->refresh());
    }

    public function logout(Request $request) {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token) {
        return response()->json([
            'user' => auth()->user(),
            'auth' => array(
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            )
        ]);
    }
}
