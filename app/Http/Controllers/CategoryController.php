<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if($request->input('limit')){
            $data = Category::limit($request->input('limit'))->get();
        }
        $data = Category::get();
        return response()->json($data, 200);
    }
}
