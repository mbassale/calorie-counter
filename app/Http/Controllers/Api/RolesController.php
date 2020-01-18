<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Role;

class RolesController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Role::class);
        return Role::orderBy('id')->get();
    }
}
