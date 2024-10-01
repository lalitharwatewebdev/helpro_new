<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// events
use App\Events\sendAcceptMessage;

class TestController extends Controller
{
    public function index(Request $request){
        event(new sendAcceptMessage('hello world'));

        return response()->json(['status' => 'Message sent!']);
    }
}
