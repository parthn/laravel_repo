<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/get', function () {
    return \App\Models\User::with('userFranchises', 'franchises')->find(1);
});

Route::get('/franchise', function () {
    return \App\Models\Franchise::with('roles')->get();
});
Route::group([
    'middleware' => ['auth:api',
//        'franchise.check'
    ]
], function () {
    Route::post('logout', 'AuthController@logout');
    Route::get('user', 'AuthController@user');
    Route::get('user2', 'AuthController@user2');

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', 'CategoryController@getAll');
        Route::post('/', 'CategoryController@getAllFiltered');
        Route::get('/{id}', 'CategoryController@get');
        Route::post('/save', 'CategoryController@save');
    });
    Route::group(['prefix' => 'permissions'], function () {
        Route::get('/{id}', 'PermissionController@get');
        Route::post('/', 'PermissionController@getAllFiltered');
        Route::post('/save', 'PermissionController@save');
        Route::get('/category/{category_id}', 'PermissionController@getByCategory');
    });
    Route::group(['middleware' => ['franchise.check']], function () {

        Route::group(['prefix' => 'roles'], function () {
            Route::get('/{id}', 'RoleController@get');
            Route::post('/', 'RoleController@getAllFiltered');
            Route::post('/save', 'RoleController@save');
            Route::post('/permissions', 'RoleController@getAllPermissionsOfRole');
            Route::post('/permissions/save', 'RoleController@saveAllPermissionsOfRole');
//        Route::get('/category/{category_id}', 'RoleController@getByCategory');
        });
//        Route::group(['prefix' => 'franchise','middleware'=>['franchise.wise.permission.check:false,edit_test_user_management,manage_users_user_management']], function () {
        Route::group(['prefix' => 'franchise'], function () {
            Route::post('/save', 'FranchiseController@save');
            Route::get('/', 'FranchiseController@get');
            Route::get('/permissions', 'FranchiseController@permissions');
        });
    });
    Route::get('user2', 'AuthController@user2');
//    Route::group(['middleware' => ['franchise.check']
//    ], function () {
//    });
});
Route::post('forgot_password', 'AuthController@forgotPassword');
Route::post('user_by_token', 'AuthController@getUserByRememberToken');
Route::post('reset_password', 'AuthController@resetPassword');


Route::post('/login', 'AuthController@login');
Route::get('/role', function () {
    $role = \App\Models\Role::whereHas('franchise', function ($q) {
        $q->where('franchises.id', 2);
    })->get();
    $role->load('franchise');
    echo "<pre>";
    print_r($role->toArray());
    die();
});

//Route::post('/forgot_password', 'AuthController@forgotPassword');



