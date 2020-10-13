<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class FranchiseController extends Controller
{
    //
    public function __construct()
    {

    }

    public function get()
    {
        $franchise = app('franchise');
        $data = [];
        $data['id'] = $franchise->id;
        $data['name'] = $franchise->name;
        $data['logo_url'] = $franchise->logo_url;
        $data['from_email'] = $franchise->from_email;
        return json_response($data);
    }

    public function permissions()
    {
        $role = Auth::user()->singleRoleOfFranchise();
        $permissions = [];
        if ($role) {
            $permissions = $role->permissions->pluck('name');
        }
        return json_response($permissions);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo_url' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            'name' => 'required|string',
            'from_email' => 'required|email'
        ]);
        if ($validator->fails()) {
            return json_response($validator->errors(), 'Error', 500);
        } else {
            $update = [];
            if ($request->hasFile('logo_url')) {
                $file = $request->file('logo_url');
//            $size = File::size($file);
//            $destinationPath = public_path() . '/' . app('franchise')->id . '/images/';
                $destinationPath = public_path() . '/' . request('franchise_id') . '/images/';
                @mkdir($destinationPath, 0777);

                $extension = $file->getClientOriginalExtension();
                $filename = str_random(25) . '.' . $extension;
                $upload_success = $request->file('logo_url')->move($destinationPath, $filename);
                if ($upload_success) {
                    $update['logo_url'] = $filename;
                }
            }

            $update['name'] = $request->input('name', '');
            $update['from_email'] = $request->input('from_email', '');
            app('franchise')
                ->update($update);
            return json_response(app('franchise'), 'Franchise Successfully');

        }

    }
}
