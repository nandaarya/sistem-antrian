<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function login(Request $request) 
    {
        $admin = Admin::where('email', $request->email)->first();
        if ($admin && Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'Login berhasil', 'admin' => $admin]);
        }
        return response()->json(['message' => 'Email atau password salah'], 401);
    }
}
