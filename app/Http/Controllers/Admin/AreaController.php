<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AreaExport;
use App\Http\Controllers\Controller;
use App\Models\Areas;
use App\Models\Category;
use Excel;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        return view("content.tables.areas");
    }

    public function addAreas()
    {
        $categories = Category::active()->get();
        return view("content.tables.add-area-service", compact("categories"));
    }

    public function store(Request $request)
    {
        $request->validate([
            "latitude" => "required",
            "longitude" => "required",
            'category' => 'required',
            "radius" => "required",
            "area_name" => "required",
            "price" => "required|numeric",
        ]);

        $data = new Areas();

        $data->price = $request->price;
        $data->radius = $request->radius;
        $data->latitude = $request->latitude;
        $data->longitude = $request->longitude;
        $data->category_id = $request->category;
        $data->area_name = $request->area_name;

        $data->save();

        return redirect()->route("admin.areas.index");
    }

    public function destroy($id)
    {
        Areas::find($id)->delete();

        return response([
            "message" => "Area Deleted Successfully",
            "reload" => true,
        ]);
    }

    public function edit($id)
    {
        $data = Areas::find($id);
        // dd($data);
        return view("content.tables.edit-area-service", compact("data"));
    }

    public function export()
    {
        return Excel::download(new AreaExport, 'areaexport.xlsx');

    }
}
