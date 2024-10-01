<?php

namespace App\Http\Controllers\API\v1\Labour;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;

class VideoController extends Controller
{
    public function get(){
        $video = Video::where("type","labour")->get();

        return response([
            "data" => $video
        ],200);
    }
}
