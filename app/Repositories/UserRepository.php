<?php

namespace App\Repositories;

use App\Events\ForgotPassword;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;

class UserRepository extends Repository
{
    protected $model_name = 'App\Models\User';
    protected $model;

    public function __construct()
    {
        parent::__construct();
    }

    public function forgotPassword(Request $request)
    {
        $email = $request->input('email');
        $user = $this->model->whereEmail($email)->first();

        $user->remember_token = Str::random(60);
        $user->save();

        $actionUrl = config('app.front_url') . '/reset/' . $user->remember_token;

        $email_data = [];
        $email_data['greeting'] = 'Hello,';
        $email_data['level'] = 'success';
        $email_data['actionUrl'] = $actionUrl;
        $email_data['outroLines'] = [];
        $email_data['introLines'] = [
            0 => 'Click the button to Reset your Password.'
        ];
        $email_data['actionText'] = 'Reset Password';
        $email_data['user'] = $user;
        $email_data['to'] = $user->email;
        event(new ForgotPassword($email_data));

        return json_response([], 'Email Send Successfully');

//        $myViewData = View::make('emails.forget_password',
//            ['email' => $request->only('email'),
//                'level' => 'success',
//                'outroLines' => [0 => ''],
//                'actionText' => 'Reset Password',
//                'actionUrl' => $url,
//                'introLines' => [0 => 'Click the button to Reset your Password.']])->render();
//
//        if (app('App\Http\Controllers\EmailController')->sendMail($credentials['email'], 'Password Reminder', $myViewData)) {
//
//        }

    }

    public function attempt(Request $request)
    {
        $credentials = request(['email_or_username', 'password']);

        $field = filter_var($credentials['email_or_username'], FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $credentials[$field] = $credentials['email_or_username'];
        unset($credentials['email_or_username']);
//        $this->model->find(1)->update(['password'=>bcrypt('123')]);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid cred.'
            ], 401);
        }
        $user = $request->user();
        $tokenResult = $user->createToken($user->name);
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();
        $user->load('roles', 'franchises');
        if (count($user->roles) > 0) {
            foreach ($user->roles as $key => $role) {
                $user->roles[$key]->append('original_name');
            }
        }
        $resultData = [
            'user' => $user,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ];
        return json_response($resultData, 'Logged in successfully');


    }
}
