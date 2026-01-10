<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\UploadFileTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
    use UploadFileTrait;

    public function index()
    {
        $vediosPosts = Post::with('videos')->where('type', 'video')->get();
        return response()->json([
            'message' => 'Video Posts Retrieved Successfully',
            'posts' => $vediosPosts,
        ], 200);
    }
    public function store(StorePostRequest $request)
    {
        try
        {
            $request->validated();
            $post = Post::create([
                'user_id' => Auth::id(),
                'type'    => 'video',
            ]);

            foreach ($request->file('video_url') as $video_url)
            {
                $path = $this->uploadFile($video_url, 'posts/videos', 'public');

                $post->videos()->create([
                    'user_id' => Auth::id(),
                    'post_id' => $post->id,
                    'video_url'     => $path,
                    'description' => $request->description,
                ]);
            }

            return response()->json([
                'message' => 'Video post created successfully',
                'post' => $post,
                'videos' => $path,
                'description' => $request->description,
            ], 201);
        }
        catch(Exception $e)
        {
            return response()->json([
                'message' => 'Failed To Create Video Post',
                'error' => $e->getMessage()
            ]);
        }
    }
}
