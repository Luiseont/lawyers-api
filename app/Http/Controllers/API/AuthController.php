<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\API\LoginRequest;


class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {

        if(Auth::attempt(['email'=> $request->input('email'), 'password' => $request->input('password')]))
        {
            $user = Auth::user();
            $data['token'] = $user->createToken('User Access Token', [$user->role])->accessToken;

            return response()->Json(['status' => 'ok', 'message' => $data], 200);

        }else{
            return response()->Json(['status' => 'ko', 'message' => 'Invalid credentials'], 401);
        }

    }
}
