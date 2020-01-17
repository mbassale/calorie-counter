<?php

namespace App\Http\Controllers\Api;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController
{
    const PASSWORD_GRANT = 'Calorie Counter Password Grant Client';

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken(self::PASSWORD_GRANT)->accessToken;
                return response(compact('token'), 200);
            } else {
                return response('Credentials mismatch', 422);
            }
        } else {
            return response('User does not exist', 422);
        }
    }
}
