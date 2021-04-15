<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (JWTAuth::attempt($credentials)) {
                $data['user'] = $request->user();
                $data['token'] = JWTAuth::fromUser($data['user']);
                return sendResponse($data, 'User logged in successfully');
            }else{
                return sendError(null, 'User login failed',400);
            }
        } catch (JWTException $e) {
            return sendError($e->getMessage(), 'Some Error Occurred',500);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return sendError($validator->errors(),'Some Error Occurred',400);
        }

        $data['user'] = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $data['token'] = JWTAuth::fromUser($data['user']);

        return sendResponse($data,'User Created Successfully',201);
    }

    public function getUser(Request $request)
    {
        try {
            if (!JWTAuth::parseToken()->authenticate()) {
                return sendError(null,'User Not Found',404);
            }else{
                $data['user'] = $request->user();
                $data['token'] = JWTAuth::fromUser($data['user']);
                return sendResponse($data, 'User token is valid');
            }
        }  catch (JWTException $e) {
            return sendError($e->getMessage(), 'Some Error Occurred',500);
        }
    }
}
