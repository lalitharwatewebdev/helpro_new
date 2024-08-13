<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    public function index()
    {
        return view("content.tables.promo_code");
    }

    public function store(Request $request)
    {
        $request->validate([
            "type" => "required",
            "promo_code_title" => "required",
            "amount" => "required|numeric"
        ]);

        $data = new PromoCode();

        $data->type = $request->type;
        $data->number = $request->amount;
        $data->title = $request->promo_code_title;

        $data->save();

        return response([
            "message" => "Promo Code Added Successfully",
            "reload" => true
        ], 200);
    }

    public function edit($id)
    {
        $data = PromoCode::find($id);
        return response($data);
    }

    public function update(Request $request)
    {
        $request->validate([
            "type" => "required",
            "amount" => "required|numeric",
            "promo_code_title" => "required"
        ]);


        $data  = PromoCode::find($request->id);

        $data->type = $request->type;
        $data->number = $request->amount;
        $data->title = $request->promo_code_title;
        $data->save();

        return response([
            "message" => "Promo Code Updated Successfully",
            "reload" => true
        ]);
    }

    public function destroy($id){
        PromoCode::find($id)->delete();

        return response([
            "message" => "PromoCode Deleted Successfully",
            "reload" => true
        ]);
    }

    public function status(Request $request){
        PromoCode::find($request->id)->update([
            "status" => $request->status
        ]);

        return response([
            "message" => "Status Updated Successfully",
            
        ]);
    }
}
