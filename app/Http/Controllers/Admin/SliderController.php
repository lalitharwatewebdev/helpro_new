<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $url = request()->url();
        $type = collect(explode('/', $url))->last();
      
        return view("content.tables.slider",compact("type"));
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
        $app_type = $request->query("type");
        $request->validate([
            "title" => "required",
            "image" => "required|max:2000|mimes:png,jpg,jpeg,webp"
            
        ]);

        $data = new Slider();
        $data->title = $request->title;
        $data->link = $request->link;
        $data->image = FileUploader::uploadFile($request->file("image"),"images/sliders");
        $data->app_type = $request->app_type;

        $data->save();

        return response([
            'header' => 'Added',
            'message' => 'Added successfully',
            'table' => 'slider-table',
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

    public function status(Request $request)
    {
        // $request->validate([
        //     'id' => 'required|numeric|exists:students,id',
        //     'status' => 'required|in:active,blocked',
        // ]);

        Slider::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'Slider status updated successfully',
            'table' => 'student-table',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Slider::findOrFail($id);
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
        $data = Slider::where("id",$request->id)->first();

        $data->title = $request->title;
        $data->link = $request->link;

        if($request->hasFile("image")){
            $data->image = FileUploader::uploadFile($request->file("image"),"images/slider");
        }

        $data->save();

        return response([
            'header' => 'Success!',
            'message' => 'Slider Updated successfully',
            'table' => 'slider-table',
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
        Slider::find($id)->delete();

        return response([
            'header' => 'Deleted!',
            'message' => 'Slider deleted successfully',
            'table' => 'slider-table',
        ]);
    }
}
