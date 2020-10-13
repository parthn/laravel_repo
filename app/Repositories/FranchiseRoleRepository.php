<?php

namespace App\Repositories;

class FranchiseRoleRepository extends Repository
{
    protected $model;
    protected $model_name='App\Models\FranchiseRole';
    //
    public function __construct()
    {
        parent::__construct();
    }
}
