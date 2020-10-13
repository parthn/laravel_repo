<?php

namespace App\Repositories;

class RoleRepository extends Repository
{
    protected $model;
    protected $model_name='App\Models\Role';
    //
    public function __construct()
    {
        parent::__construct();
    }
}
