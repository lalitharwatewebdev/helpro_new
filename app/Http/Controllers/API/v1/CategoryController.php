<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function get(){
        $data = Category::active()->get();

        return response([
            "data" => $data,
            "status" => true  
        ],200);
    }
}
