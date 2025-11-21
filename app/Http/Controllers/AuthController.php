<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginPage()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            return redirect()->route('hr.dashboard');
        }

        return back()->with('error', 'Invalid username or password');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('hr.dashboard');
    }
}

