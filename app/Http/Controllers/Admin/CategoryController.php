<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CategoryExport;
use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Excel;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("content.tables.category");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "title" => "required",
            "image" => "required|mimes:png,jpg,jpeg,webp|max:2000",
        ]);

        $data = new Category();

        $data->title = $request->title;
        $data->percentage_for_less_than = $request->percentage_for_less_than;
        $data->percentage_for_more_than = $request->percentage_for_more_than;

        if ($request->hasFile("image")) {
            $data->image = FileUploader::uploadFile($request->file("image"), "images/category_images");
        }

        $data->save();

        return response([
            'header' => 'Added',
            'message' => 'Added successfully',
            'table' => 'category-table',
            "reload" => true,
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $name = new Category();
        $data = $name::where('id', $id)->first();
        return response($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // $request->validate([
        //     "title" => "required",
        //     "image" => "required|file|mimes:png,jpg,jpeg,webp|max:2048",
        // ]);

        $data = Category::where("id", $request->id)->first();

        $data->title = $request->title;
        $data->percentage_for_less_than = $request->percentage_for_less_than;
        $data->percentage_for_more_than = $request->percentage_for_more_than;

        if ($request->hasFile("image")) {
            $data->image = FileUploader::uploadFile($request->file("image"), "images/category_images");
        }

        $data->save();

        return response([
            'header' => 'Success!',
            'message' => 'Category Updated successfully',
            'table' => 'category-table',
            "reload" => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Category::findOrFail($id)->delete();

        return response([
            'header' => 'Deleted!',
            'message' => 'Category deleted successfully',
            'table' => 'category-table',
        ]);
    }

    public function status(Request $request)
    {

        Category::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'Subscription Status Updated Successfully',
            'table' => 'student-table',
        ]);
    }

    public function export()
    {
        return Excel::download(new CategoryExport, 'categorys.xlsx');
    }
}
