<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\State;
use App\Models\City;
use Illuminate\Http\Request;

class LabourController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('content.tables.labour');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $states = State::india()->orderBy("name")->get();

        $data = compact("states");


        return view("content.tables.add-labour",$data);
    }

  

    public function getCity(Request $request){
        $state_id = $request->query("state_id");

        $data = City::where("state_id",$state_id)->get();

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
        // $request->validate([
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
        
        // ]);

        $data = new User();


        

        // if ($request->hasFile("aadhaar_card_front")) {
        //     $data->aadhaar_card_front = FileUploader::uploadFile($request->file("aadhaar_card_front"), "images/aadhar");
        // }

        // if ($request->hasFile("aadhaar_card_back")) {
        //     $data->aadhaar_card_back = FileUploader::uploadFile($request->file("aadhaar_card_back"), "images/aadhar");
        // }

        if($request->hasFile("profile_pic")) {
            // dd($request->profile_pic);
           $data->profile_pic = FileUploader::uploadFile($request->file("profile_pic"),"/images/profile_pic");
        }

        $data->phone = $request->phone;
        $data->email = $request->email;
        $data->pan_card_number = $request->pan_number;
        $data->bank_name = $request->bank_name;
        $data->IFSC_code = $request->IFSC_code;
        $data->name = $request->name;
        $data->aadhaar_number = $request->aadhaar_number;
        $data->branch_address = $request->bank_address;
     

        $data->rate_per_day = $request->rate_per_day;
        $data->type = "labour"; 

        $data->save();

        return redirect("admin/labours?labour_status=pending");
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
