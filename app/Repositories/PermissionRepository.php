<?php

namespace App\Repositories;

class PermissionRepository extends Repository
{
    protected $model;
    protected $model_name='App\Models\Permission';
    //
    public function __construct()
    {
        parent::__construct();
    }

    public function getOfCategory($category_id)
    {
        return $this->model->whereCategoryId($category_id)->get();
    }
}
