<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    const PASSWORD_GRANT = 'Calorie Counter Password Grant Client';

    public function register(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed'
        ]);

        // check existing email
        $userEmail = strtolower($request->email);
        $user = User::where('email', $userEmail)->first();
        if ($user) {
            return response()->json([
                'errors' => [
                    'email' => 'User already taken'
                ]
            ], 422);
        }

        // create user and return login token
        $user = User::create([
            'role_id' => Role::USER,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $userEmail,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken(self::PASSWORD_GRANT)->accessToken;
        return response(compact('token'), 200);
    }
}
