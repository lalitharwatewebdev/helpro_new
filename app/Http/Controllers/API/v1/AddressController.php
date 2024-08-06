<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $id = auth()->user()->id;

        if ($request->update == 'yes') {
            $address_id  = $request->id;

            if ($request->isPrimary == "yes") {
                Address::where("user_id", auth()->user()->id)->update(['is_primary' => "no"]);
            }
            $data = Address::where("id", $address_id)->first();

            $data->address = $request->address;
            $data->pincode = $request->pincode;
            $data->state_id = $request->state_id;
            $data->city_id = $request->city_id;
            $data->is_primary = $request->isPrimary;

            $data->save();

            return response([
                "message" => "Address Updated Successfully",
                "status" => true
            ], 200);
        } else {



            if ($request->isPrimary == "yes") {
                Address::where("user_id", auth()->user()->id)->update(['is_primary' => "no"]);
            }
            $data = new Address();
            $data->user_id = $id;
            $data->address = $request->address;
            $data->pincode = $request->pincode;
            $data->state_id = $request->state_id;
            $data->city_id = $request->city_id;
            $data->is_primary = $request->isPrimary;

            $data->save();

            return response([
                "message" => "Address Added Successfully",
                "status" => true
            ], 200);
        }
    }

    public function setAddressPrimary(Request $request)
    {
        $user_address = Address::where("user_id", auth()->user()->id)->update([
            "is_primary" => "no"
        ]);




        $set_primary = Address::where("user_id", auth()->user()->id)
            ->where("id", $request->id)->first();

        $set_primary->update([
            "is_primary" => "yes"
        ]);

        $set_primary->save();

        return response([
            "message" => "Address set as primary",
            "status" => true
        ], 200);
    }


    public function edit(Request $request)
    {
        $id = $request->query("id");

        $data = Address::where("id", $id)->first();

        return response([
            "data" => $data,
            "status" => true,
        ], 200);
    }

    public function delete(Request $request)
    {
        $id = $request->id;

        Address::where("id", $id)->first()->delete();

        return response([
            "message" => "Address Deleted Successfully",
            "status" => true
        ], 200);
    }

    public function get(Request $request)
    {
        $data = Address::with(['states:id,name', "cities:id,name"])->where("user_id", auth()->user()->id)->latest()->get();



        return response([
            "data" => $data,
            "status" => true
        ], 200);
    }

    public function update(Request $request)
    {
        // $id = auth()->user()->id;
        $address_id = $request->address_id;

        $user_address = Address::where("id", $address_id)->first();

        return response([
            "data" => $user_address,
            "status" => true
        ], 200);
    }
}
