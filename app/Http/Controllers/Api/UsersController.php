<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);
        return User::with('role')->orderBy('last_name')->get();
    }

    public function show(Request $request, User $user = null)
    {
        if (!$user) $user = $request->user();
        $this->authorize('view', $user);
        return $user;
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $this->validate($request, [
            'role_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required'
        ]);
        $user->update($request->only(['first_name', 'last_name', 'email']));
        if ($user->role_id != $request->role_id) {
            $this->authorize('updateRole', User::class);
            $user->role_id = $request->input('role_id');
            $user->save();
        }
        if ($request->has('password') && $request->has('password_confirmed')) {
            $this->validate($request, [
                'password' => 'required|confirmed'
            ]);
            $user->password = Hash::make($request->password);
            $user->save();
        }
        return $user->load('role');
    }
}
