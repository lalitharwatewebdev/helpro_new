<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\TicketChat;
use Illuminate\Http\Request;

class TicketChatController extends Controller
{
    public function create(Request $request){
            TicketChat::create([
                "ticket_id" => $request->ticket_id,
                "message" => $request->message,
            ]);

            return response([
                "message" => "Chat Added",
                "status" => true
            ],200);
    }

    public function get(Request $request){
        $ticket_id = $request->query("ticket_id");

        $data= TicketChat::where("ticket_id",$ticket_id)->get();

        return response([
            "data" => $data,
            "status" => true
        ],200);
    }
}
