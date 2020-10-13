<?php

if (!function_exists('test')) {
    function test($name)
    {
        return $name;
    }
}

if (!function_exists('json_response')) {
    function json_response($data = [], $message = '', $status = 200)
    {
        $final = [];
        $final['message'] = $message;
        $final['data'] = $data;
        return response()->json($final, $status);
    }
}

if (!function_exists('addEditValidation')) {

    function addEditValidation($index = 'id')
    {
        return ',' . request()->$index ?? null;

    }
}
if (!function_exists('sendEmail')) {
    function sendEmail($to, $subject, $view, $data = [], $attachments = [], $ccs = [], $bccs =['vaibhav@esparkifo.com'])
    {
//        print_r($data[]);
//        die();
//        return app('franchise')->name ?? config('mail.from.address');
        try {
            Illuminate\Support\Facades\Mail::send($view, $data
                , function ($message) use ($to, $subject, $attachments, $ccs, $bccs) {

//                    if (request()->has('custom_franchise_email')) {
//
//                    }
                    $from_email=app()->bound('franchise')?(app('franchise')->from_email ?? config('mail.from.address')):config('mail.from.address');
                    $from_name=app()->bound('franchise')?(app('franchise')->name ?? config('mail.from.name')):config('mail.from.name');
                    $message->from(
                        $from_email,
                        $from_name
                    );
                    $message->to($to, '')
                        ->subject($subject);

                    if ($ccs) {
                        foreach ($ccs as $cc) {
                            $message->cc($cc);
                        }
                    }
                    if ($bccs) {
                        foreach ($bccs as $bcc) {
                            $message->bcc($bcc);
                        }
                    }
                    if ($attachments) {
                        foreach ($attachments as $attachment) {
                            $message->attach($attachment);
                        }
                    }
                });
            return true;
        } catch (Exception $exception) {
            Log::info('Mail Error: ' . $exception->getMessage());
            return false;
        }


    }
}

