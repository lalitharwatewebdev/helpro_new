<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Video;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::all();
        return view('content.tables.video', compact('videos'));
    }

    public function edit(Request $request,$id){
        $name = new Video();
        $data = $name::where('id', $id)->first();
        return response($data);
    }

    public function update(Request $request)
    {
        $request->validate([
            "video_type" => "required",
            "video" => "required",
            "title" => "required"
        ]);

        $video = Video::where('id',$request->id)->first();
        if(!empty($request->image))
        {
            $video->image = FileUploader::uploadFile($request->image,"images/youtube-video");
        }

        $video->video_type = $request->video_type;
        $video->video = $request->video;
        $video->title = $request->title;
        $video->save();

        return response([
            'header' => 'Added',
            'message' => 'Added successfully',
            'table' => 'video-table',
            "reload" => true
        ]);
    }

    public function store(Request $request){
        $request->validate([
            "image" => "required",
            "video_type" => "required",
            "video" => "required",
            "title" => "required"
        ]);

        $video = new Video();

        $video->image = FileUploader::uploadFile($request->image,"images/youtube-video");
        $video->video_type = $request->video_type;
        $video->video = $request->video;
        $video->title = $request->title;
        $video->save();

        return response([
            'header' => 'Added',
            'message' => 'Added successfully',
            'table' => 'video-table',
            "reload" => true
        ]);
    }

    public function destroy($id)
    {
        Video::findOrFail($id)->delete();

        return response([
            'header' => 'Deleted!',
            'message' => 'Video deleted successfully',
            'table' => 'video-table',
        ]);
    }
}
