<?php

namespace App\Http\Controllers;

use App\Models\FranchiseRole;
use App\Models\Role;
use App\Repositories\FranchiseRoleRepository;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
    private $role_repo;
    private $franchise_role_repo;

    public function __construct(RoleRepository $role_repo, FranchiseRoleRepository $franchise_role_repo)
    {
        $this->role_repo = $role_repo;
        $this->franchise_role_repo = $franchise_role_repo;
    }


    public function saveAllPermissionsOfRole(Request $request)
    {
//        $role = $request->all();
        $permissions = [];

        if ($request->has('id') && $request->has('role.category_wise_permissions')) {
            foreach ($request->role['category_wise_permissions'] as $category) {
                foreach ($category['permissions'] as $permission) {
                    if ($permission['has_permission']) {
                        $permissions[] = $permission['id'];
                    }
                }
            }
            $franchiseRole = FranchiseRole::find($request->id);
            if ($franchiseRole) {
                $franchiseRole->name = $request->name;
                $franchiseRole->save();
            }
            $role_id = $request->role['id'];
            $role = Role::findById($role_id);
            if ($role) {
                $role->syncPermissions($permissions);
            }
            return json_response([], 'Permission Saved');
        } else {
            return json_response(['role' => ['Invalid Data']], 'Error', 500);
        }
    }

    public function get($role_id)
    {
        $franchiseRole = FranchiseRole::whereHas('franchise', function ($q) {
            $q->whereId(app('franchise')->id);
        })
            ->where('id', $role_id)
            ->first();

//        $franchiseRole ? $franchiseRole->makeHidden(['role_name', 'franchise_id']) : null;
        return json_response($franchiseRole);
    }

    public function getAllFiltered(Request $request)
    {
        $franchiseRoles = FranchiseRole::whereHas('franchise', function ($q) {
            $q->whereId(app('franchise')->id);
        })->get();
        return json_response(DataTables::of($franchiseRoles)->make());
    }

    public function getAllPermissionsOfRole(Request $request)
    {
//        return $request->all();
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:franchise_roles,id'
        ]);
        if ($validator->fails()) {
            return json_response($validator->errors(), 'Error', 500);
        } else {
            $franchiseRoles = FranchiseRole::whereHas('franchise', function ($q) {
                $q->whereId(app('franchise')->id);
            })->whereId($request->role_id)
                ->first();
            if ($franchiseRoles) {
//            $franchiseRoles
//                ->role->revokePermissionTo('edit_test_user_management');
//            $franchiseRoles
//                ->role->givePermissionTo('edit_test_user_management');
                $franchiseRoles
                    ->role
//                ->makeHidden(['id', 'created_at', 'updated_at', 'permissions'])
                    ->append('category_wise_permissions');
            }
            return json_response($franchiseRoles);

        }

    }

    public function save(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return json_response($validator->errors(), 'Error', 500);
        } else {
            $franchiseRole = new FranchiseRole;
            $franchiseRole->name = $request->name;
//            $franchiseRole->role_name = Str::slug(strtolower($request->name), '_') . '_' . Str::orderedUuid();
            $franchiseRole->save();

            return json_response($franchiseRole, 'Role Saved');
        }

    }


}
