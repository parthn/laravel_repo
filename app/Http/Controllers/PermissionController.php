<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Permission;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function Sodium\increment;
use Yajra\DataTables\DataTables;

class PermissionController extends Controller
{
    private $permission_repo;

    public function __construct(PermissionRepository $permission_repo)
    {
        $this->permission_repo = $permission_repo;
    }

    public function getAllFiltered()
    {
        $permissions = Permission::with(['category' => function ($q) {
            $q->select('id', 'name');
        }])->get();
        return json_response(DataTables::of($permissions)->make());
    }

    public function get($permission_id)
    {
        return json_response($this->permission_repo->getById($permission_id));
    }

    public function getByCategory($category_id)
    {
        return json_response($this->permission_repo->getOfCategory($category_id));
    }

    public function save(Request $request)
    {
        $category = Category::find($request->category_id);
        $name = '';
        if ($category) {
            $name = $request->input('name') . ' ' . $category->name;
        }
        $general_name = Str::snake(strtolower($name), '_');
        $request->merge(['general_name' => $general_name]);
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions,general_name' . addEditValidation(), 'category_id,' . $request->category_id,
            'general_name' => 'required|unique:permissions,name' . addEditValidation(),
            'category_id' => 'required|exists:categories,id'
        ]);
        if ($validator->fails()) {
            return json_response($validator->errors(), 'Error', 500);
        } else {
            $permission = Permission::firstOrNew(['id' => $request->id]);
            if (!$permission->exists) {
                $permission->name = $request->general_name;
            }
            $permission->general_name = $request->name;
            $permission->guard_name = 'web';
            $permission->category_id = $request->category_id;
            $permission->save();
            return json_response($permission, 'Permission Saved');
        }

    }

}
