<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleLoginController extends Controller
{
    // Form login untuk Admin
    public function showAdminLoginForm()
    {
        return view('auth.login', ['role' => 'admin']);
    }
}
