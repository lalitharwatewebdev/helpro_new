<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        return view("content.tables.subscription");
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
            "amount" => "required|numeric",
            "days" => "required|numeric"
        ]);

        $data = new Subscription();

        $data->title = $request->title;
        $data->amount = $request->amount;
        $data->days  = $request->days;
        
     

        $data->save();

        return response([
            'header' => 'Added',
            'message' => 'Added successfully',
            'table' => 'subscription-table',
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

    public function update(Request $request){
        $request->validate([
            "title" => "required",
            "amount" => "required|numeric",
            "days" => "required|numeric"
        ]);

       

        $data = Subscription::where("id",$request->id)->first();
        $data->title = $request->title;
        $data->amount = $request->amount;
        $data->days = $request->days;

        $data->save();
        return response([
            "message" => "Subscription Updated",
            'table' => 'subscription-table',
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
        $name = new Subscription();
        $data = $name::where('id', $id)->first();
        return response($data);
    }

    public function status(Request $request){
      

        Subscription::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'Subscription Status Updated Successfully',
            'table' => 'student-table',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Subscription::findOrFail($id)->delete();

        return response([
            'header' => 'Deleted!',
            'message' => 'Category deleted successfully',
            'table' => 'category-table',
        ]);
    }
}
