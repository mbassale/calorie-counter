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

    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        $this->validate($request, [
            'role_id' => 'required|exists:roles,id',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
            'calories_per_day' => 'nullable|numeric|min:1'
        ]);

        // create user and return login token
        $user = User::create([
            'role_id' => $request->role_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'calories_per_day' => $request->calories_per_day
        ]);
        return $user->load('role');
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $this->validate($request, [
            'role_id' => 'nullable|exists:roles,id',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'calories_per_day' => 'nullable|numeric|min:1'
        ]);
        $user->update($request->only(['first_name', 'last_name', 'email', 'calories_per_day']));
        if ($request->has('role_id') && $user->role_id != $request->role_id) {
            $this->authorize('updateRole', User::class);
            $user->role_id = $request->input('role_id');
            $user->save();
        }
        if ($request->has('password') && $request->has('password_confirmation')) {
            $this->validate($request, [
                'password' => 'required|confirmed'
            ]);
            $user->password = Hash::make($request->password);
            $user->save();
        }
        return $user->load('role');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        return $user;
    }
}
