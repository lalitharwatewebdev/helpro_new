<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LabourBusinessSettings as ModelsLabourBusinessSettings;
use Illuminate\Http\Request;

class LabourBusinessSettings extends Controller
{
    public function index(){
        $data = ModelsLabourBusinessSettings::pluck("value","title");
        return view("content.pages.labour-business-settings",compact("data"));
    }

    public function store(Request $request){
        foreach ($request->all() as $key => $value) {
            ModelsLabourBusinessSettings::updateOrCreate(
                [
                    'title' => $key,
                ],
                [
                    'value' => $value,
                ]
            );
        }
        return response([
            'header' => 'Updated!',
            'message' => 'Settings Updated Successfully'
        ]);
    }
}
