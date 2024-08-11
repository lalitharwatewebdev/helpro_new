<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use App\Models\State;
use App\Models\LabourImage;
use App\Models\City;
use App\Models\Labour;
use Illuminate\Http\Request;

class LabourController extends Controller
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



        return view('content.tables.labour', compact("type"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $states = State::india()->orderBy("name")->get();

        $category_data = Category::active()->get();

        $data = compact("states", "category_data");


        return view("content.tables.add-labour", $data);
    }

    public function details(Request $request)
    {
        $data = User::with("states", "cities")->where("id", $request->id)->first();
        // return $data;
        return view("content.tables.details-labours", compact("data"));
    }



    public function getCity(Request $request)
    {
        $state_id = $request->query("state_id");

        $data = City::where("state_id", $state_id)->get();

        return response($data);
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
            //     "name" => "required",
            //     "email" => "required|email",
            //     "phone" => "required|max:10",
            //     "aadhaar_number" => "required|max:12",
            //     "aadhaar_card_front" => "required|mimes:png,jpeg,webp,jpg",
            //     "aadhaar_card_back" => "required|mimes:png,jpeg,webp,jpg",
            //     "pan_number" => "required",
            //     "bank_name" => "required",
            //     "IFSC_code" => "required",
            //     "bank_address" => "required",
            'profile_pic' => 'required|mimes:png,jpg,jpeg,webp,svg'
        ]);
        $data = new User();


        if ($request->profile_pic) {
            $data->profile_pic = FileUploader::uploadFile($request->profile_pic, "images/profile_pic");
        }

        if ($request->aadhaar_card_front) {
            $data->aadhaar_card_front = FileUploader::uploadFile($request->aadhaar_card_front, "images/aadhaar_card");
        }

        if ($request->aadhaar_card_back) {
            $data->aadhaar_card_back = FileUploader::uploadFile($request->aadhaar_card_back, "images/aadhaar_card");
        }


        $data->phone = $request->phone;
        $data->email = $request->email;
        $data->pan_card_number = $request->pan_number;
        $data->bank_name = $request->bank_name;
        $data->IFSC_code = $request->IFSC_code;
        $data->address = $request->address;
        $data->name = $request->name;
        $data->state = $request->state;
        $data->start_time = $request->start_time;
        $data->end_time = $request->end_time;
        $data->city = $request->city;
        $data->aadhaar_number = $request->aadhaar_number;
        $data->branch_address = $request->bank_address;
        $data->gender = $request->gender;


        $data->rate_per_day = $request->rate_per_day;
        $data->type = "labour";



        $data->save();

        if ($data) {

            foreach ($request->labour_images as $images) {
                $labour_image = new LabourImage();
                $labour_image->user_id = $data->id;
                $labour_image->image = FileUploader::uploadFile($images, 'images/labour_images');

                $labour_image->save();

                $user_data = User::find($data->id);

                $user_data->category()->attach($request->category);
            }



        }
        return redirect("admin/labours/pending");
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
        $data = User::findOrFail($id);
        return response($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();

        return response([
            'header' => 'Deleted!',
            'message' => 'Labour deleted successfully',
            'table' => 'slider-table',
        ]);
    }

    public function status(Request $request)
    {
        // $request->validate([
        //     'id' => 'required|numeric|exists:students,id',
        //     'status' => 'required|in:active,blocked',
        // ]);

        User::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'Labour status updated successfully',
            'table' => 'labour-table',
        ]);
    }
}
