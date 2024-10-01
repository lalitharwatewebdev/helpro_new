<?php

namespace App\Http\Controllers\API\v1\Labour;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function create(Request $request){
        Ticket::create([
            "user_id" => auth()->user()->id,
            "ticket_number" => sha1("TCK".rand(000,999)),
            "ticket_name" => $request->ticket_name
        ]);

        return response([
            "message" => "Ticket Created Successfully",
            "status" => true
        ],200);
    }
}
