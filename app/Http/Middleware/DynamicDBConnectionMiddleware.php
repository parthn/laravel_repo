<?php

namespace App\Http\Middleware;

use App\Models\Franchise;
use App\Models\UserFranchise;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DynamicDBConnectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'franchise_id' => [
                'required',
                Rule::exists('franchises', 'id')
            ]
        ]);
        if ($validator->fails()) {
            return json_response($validator->errors(), 'Error', 403);
        } else {
            if (Auth::user()->hasAccessToFranchise($data['franchise_id'])) {
                $franchise = Franchise::find($data['franchise_id']);

                App::singleton('franchise', function () use ($franchise) {
                    return $franchise;
                });

//                $request->merge(['custom_franchise' => $franchise->toArray()]);
                try {
//                    config(['default' => 'mysql']);
//                    config(['database.connections.mysql.database' => decrypt($franchise->database)]);
//                    config(['database.connections.mysql.username' => decrypt($franchise->db_user)]);
//                    config(['database.connections.mysql.password' => decrypt($franchise->db_password)]);
                } catch (\Exception $exception) {
                    return json_response([], $exception->getMessage(), 401);
                }
            } else {
                return json_response(['franchise_id' => ['You Don\'n have access to this franchise.']], 'You Don\'n have access to this franchise.', 403);
            }
        }
        return $next($request);
    }
}
