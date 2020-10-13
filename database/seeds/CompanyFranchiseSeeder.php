<?php

use Illuminate\Database\Seeder;
//Models
use App\Models\Company;
use App\Models\Franchise;
use App\Models\User;
use App\Models\Role;
use App\Models\FranchiseRole;
use App\Models\UserFranchise;
use App\Models\Permission;
use App\Models\Category;
//Models end
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

class CompanyFranchiseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = ['optamark', 'esb'];
        $franchises = ['franchise 1', 'franchise 2'];
        $permissions = ['Edit User', 'Create User'];
        $categories = ['User management'];
        foreach ($categories as $category) {
            $category_attr = [];
            $category_attr['name'] = $category;
           $cat= Category::create($category_attr);
        }
        foreach ($permissions as $permission) {
            $permission_attr = [];
            $permission_attr['guard_name'] = 'web';
            $permission_attr['general_name'] = $permission;
            $permission_attr['name'] = Str::slug(strtolower($permission), '_');
            $permission_attr['category_id'] = 1;
            Permission::create($permission_attr);
        }

        Artisan::call('passport:client --personal');


        $roles = [
            'Admin',
            'Estimator HOD', 'Estimator', 'Sales Representative'];

        foreach ($companies as $company_name) {
            $data = [];
            $data['name'] = $company_name;
            $company = Company::create($data);

            foreach ($franchises as $franchise_name) {
                $f_data = [];
                $f_data['name'] = $company_name . ' ' . $franchise_name;
                $f_data['company_id'] = $company->id;
                $f_data['logo_url'] = 'logo.png';
                $f_data['database'] = Str::slug($franchise_name, '_');
                $f_data['db_user'] = Str::slug($franchise_name, '_');
                $f_data['db_password'] = '123456';
                $f_data['from_email'] = Str::slug($franchise_name, '_') . '@' . Str::slug($company_name, '_') . '.com';
                $franchise = Franchise::create($f_data);


                foreach ($roles as $role_name) {
//                    $name = Str::slug($role_name, '_') . '_' . Str::orderedUuid();
                    $franchise_role = [];
                    $franchise_role['name'] = $role_name;
//                    $franchise_role['role_name'] = $name;
                    $franchise_role['franchise_id'] = $franchise->id;
                    $FranchiseRole = FranchiseRole::create($franchise_role);

                    $role=$FranchiseRole->role();
//                    $role_data = [];
//                    $role_data['name'] = $FranchiseRole->role_name;
//                    $role_data['guard_name'] = 'web';
//                    $role = Role::create($role_data);


                    for ($i = 1; $i <= 2; $i++) {
//                        Str::re
                        $user_data = [];
                        $user_data['name'] = $franchise->name . ' ' . $FranchiseRole->name . ' ' . $i;
                        $user_data['username'] = Str::slug($franchise->name, '_') . '_' . $i . '_' . sprintf("%06d", mt_rand(1, 999999));
//                        $user_data['last_name'] =;
                        $user_data['email'] = Str::slug($franchise->name, '_') . '_' . $i . '@' . strtolower(trim(str_replace(" ", "_", $FranchiseRole->name))) . '.com';
                        $user_data['password'] = Hash::make('123456');
                        $user = User::create($user_data);

                        $user_franchise = [];
                        $user_franchise['user_id'] = $user->id;
                        $user_franchise['franchise_id'] = $franchise->id;
                        UserFranchise::create($user_franchise);


                        $user->assignRole($role->id);
                    }
                }
            }
        }
    }
}
