<?php

namespace App\Repositories;

class CategoryRepository extends Repository
{
    protected $model;
    protected $model_name = 'App\Models\Category';
    //

    public function __construct()
    {
        parent::__construct();
    }


}
