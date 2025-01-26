<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\LoginRequest; // カスタムLoginRequestを使用
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController as BaseController;

class CustomAuthenticatedSessionController extends BaseController
{
    
}

