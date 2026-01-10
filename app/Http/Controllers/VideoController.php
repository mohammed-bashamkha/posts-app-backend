<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\UploadFileTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
    use UploadFileTrait;
    public function store(Request $request)
    {
        try
        {
            $request->validate([
            'description' => 'nullable|string|max:500',
            'video_url'   => 'required|array|min:1',
            'video_url.*' => 'mimetypes:video/mp4,video/avi,video/mov|max:40480',
        ]);
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
