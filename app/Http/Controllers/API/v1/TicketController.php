<?php

namespace App\Http\Controllers\API\v1;

use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function create(Request $request){

        $ticket = new Ticket();

        $ticket->user_id = auth()->user()->id;
        $ticket->ticket_number = "#HELPRO".rand(0000,9999);
        $ticket->ticket_name = $request->ticket_name;

        if($request->image){
            $ticket->image = FileUploader::uploadFile($request->image,"images/tickets");
        }

        $ticket->save();

        return response([
            "message" => "Ticket Created Successfully",
            "status" => true
        ],200);
    }


    public function get(){
        $user_id = auth()->user()->id;

        $ticket = Ticket::where("user_id",$user_id)->get();

        return response([
            "data" => $ticket,
            "status" => true
        ],200);
    }
}
