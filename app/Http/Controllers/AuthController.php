<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    private $user_repo;

    public function __construct(UserRepository $user_repo)
    {
        $this->user_repo = $user_repo;
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'remember_token' => 'required|exists:users,remember_token',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return json_response($validator->errors(), $validator->errors()->first(), 500);
        } else {
            $user = User::whereRememberToken($request->input('remember_token'))->first();
            $user->password = bcrypt($request->input('password'));
            $user->save();
            return json_response($user, 'Password reset successfully');
        }
    }

    public function getUserByRememberToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'remember_token' => 'required|exists:users,remember_token',
        ]);
        if ($validator->fails()) {
            return json_response($validator->errors(), 'Invalid url', 500);
        } else {
            $user = User::whereRememberToken($request->input('remember_token'))->first();
            return json_response($user, '');
        }
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users,email',
        ]);
        if ($validator->fails()) {
            return json_response($validator->errors(), 'Errors', 500);
        } else {
            return $this->user_repo->forgotPassword($request);
        }
    }

    public function user2(Request $request)
    {
        return response()->json(Auth::user()->myHasPermissionTo('edit'));
    }

    public function logout(Request $request)
    {
        $request->user()->token()->delete();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function user(Request $request)
    {
        $user = auth()->user();
        $user->load('roles', 'franchises');
        if (count($user->roles) > 0) {
            foreach ($user->roles as $key => $role) {
                $user->roles[$key]->append('original_name');
            }
        }
        return json_response($user);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
//            'email' => 'required|string|email',
            'email_or_username' => 'required|string',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 500);
        } else {
            return $this->user_repo->attempt($request);
        }


    }
}
