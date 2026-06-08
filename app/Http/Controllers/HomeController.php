<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(): RedirectResponse
    {
        return match (Auth::user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'puskesmas' => redirect()->route('puskesmas.dashboard'),
            'kader' => redirect()->route('kader.dashboard'),
            'user' => redirect()->route('user.dashboard'),
            default => redirect('/'),
        };
    }
}