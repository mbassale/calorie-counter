<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

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
}
