<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class CategoryController extends Controller
{
    //
    private $category_repo;

    public function __construct(CategoryRepository $category_repo)
    {
        $this->category_repo = $category_repo;
    }

    public function save(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|unique:categories,name' . addEditValidation()
        ]);
        if ($validator->fails()) {
            return json_response($validator->errors(), 'Error', 500);
        } else {
            $category= Category::firstOrNew(['id'=>$request->id]);
            $category->name = $request->name;
            $category->save();
            return json_response($category, 'Category Saved');
        }

    }

    public function getAllFiltered()
    {
        return json_response(DataTables::of(Category::all())->make());
    }

    public function getAll()
    {
        $categories = $this->category_repo->getAll();
        $categories->makeHidden(['created_at', 'updated_at']);
//        $categories->load('permissions');
        return json_response($categories);

    }

    public function get($category_id)
    {
        $category = $this->category_repo->getById($category_id);
        return json_response($category);
    }
}
