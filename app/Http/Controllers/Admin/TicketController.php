<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Ticket;
use App\Models\TicketChat;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::all();
        return view('content.tables.tickets', compact('tickets'));
    }

    public function getTicketChat(Request $request){
        $ticket_id = $request->query("ticket_id");

        $ticket = TicketChat::where("ticket_id",$ticket_id)->get();

        return response([
            "data" => $ticket,
            "message" => "okay"
        ],200);
    }

    public function store(Request $request)
    {
     
        $data = new TicketChat();
        $data->message = $request->message;
        $data->ticket_id = $request->id;
        $data->isAdmin = 1;
        $data->save();
        return response([
            'header' => 'Added',
            'message' => 'Message Added successfully',
            'table' => 'subject-table' ,
            "reload" => true
        ]);
    }
    public function edit($id)
    {
        $name = Subject::findOrFail($id);
        return response($name);
    }

    public function update(Request $request)
    {

    }

    public function status(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:subjects,id',
            'status' => 'required|in:active,blocked',
        ]);

        Subject::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'subject status updated successfully',
            'table' => 'subject-table',
        ]);
    }

    public function destroy($id)
    {
        Subject::findOrFail($id)->delete();
        return response([
            'header' => 'Deleted!',
            'message' => 'subject deleted successfully',
            'table' => 'subject-table',
        ]);
    }

    public function export()
    {
        return Excel::download(new GeneralExport(new Subject), 'subject.xlsx');
    }
}
