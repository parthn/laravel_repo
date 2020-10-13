<?php

namespace App\Listeners;

use App\Events\ForgotPassword;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class SendForgotPasswordEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ForgotPassword $event
     * @return void
     */
    public function handle(ForgotPassword $event)
    {
       return sendEmail('vaibhav@esparkinfo.com','Reset Password','emails.forget_password',$event->data);
//       return sendEmail($event->data['to'],'Reset Password','emails.forget_password',$event->data);
    }

    public function failed(ForgotPassword $event, $exception)
    {
        Log::info('Failed Email Error for ' . $event->data->user->email . ': ' . $exception->getMessage());
    }
}
