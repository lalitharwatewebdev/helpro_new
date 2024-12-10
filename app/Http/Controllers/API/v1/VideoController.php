<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function get(){
        $video = Video::get();

        return response([
            "data" => $video,
            "status" => true
        ],200);
    }
}
