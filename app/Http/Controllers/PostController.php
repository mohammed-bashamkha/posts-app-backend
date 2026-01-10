<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\UploadFileTrait;

class PostController extends Controller
{
    use UploadFileTrait;
    public function index()
    {
        try
        {
            $user = Auth::id();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $posts = Post::where('user_id', $user)->with(['videos', 'images', 'comments'])->get();
            return response()->json($posts, 200);
        }
        catch(Exception $e)
        {
            return response()->json([
                'message' => 'Failed To Display Posts',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',

            'description' => 'nullable|string|max:500',

            'video_url'   => 'nullable|array|min:1',
            'video_url.*' => 'mimetypes:video/mp4,video/avi,video/mov|max:40480',

            'image_url'   => 'nullable|array|min:1',
            'image_url.*' => 'image|mimes:jpg,png,jpeg|max:2048',
        ]);
            $post = Post::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'content' => $request->content,
            ]);

            if ($request->hasFile('image_url')) {
            foreach ($request->file('image_url') as $image) {

                $imagePath = $this->uploadFile($image, 'posts/images', 'public');

                $images[] = $post->images()->create([
                    'user_id'     => Auth::id(),
                    'image_url'         => $imagePath,
                    'description' => $request->description,
                ]);
            }
        }


            if ($request->hasFile('video_url')) {
            foreach ($request->file('video_url') as $video) {

                $videoPath = $this->uploadFile($video, 'posts/videos', 'public');

                $videos[] = $post->videos()->create([
                    'user_id'     => Auth::id(),
                    'video_url'         => $videoPath,
                    'description' => $request->description,
                ]);
            }
        }

            return response()->json([
                'message' => 'Post created successfully',
                'post' => $post->load(['images', 'videos']),
                'description' => $request->description,
            ],201);
        }
        catch(Exception $e)
        {
            return response()->json([
                'message' => 'Failed To Create Post',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try
        {
            $user = Auth::id();
            $post = Post::findOrFail($id);
            if($post->user_id !== $user) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 403);
            }

            $data = $request->validate([
                'title' => 'sometimes|required|string',
                'content' => 'sometimes|required|string'
            ]);
            $post->update($data);
            return response()->json($post,200);
        }
        catch(Exception $e)
        {
            return response()->json([
                'message' => 'Failed To Create Post',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try
        {
            $user = Auth::id();
            $post = Post::findOrFail($id);
            if($post->user_id !== $user) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 403);
            }
            $post->delete();
            return response()->json(['message' => 'Post Deleted Successfully','post title' => $post->title],200);
        }
        catch(Exception $e)
        {
            return response()->json([
                'message' => 'Failed To Delete Post',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        try
        {
            $user = Auth::id();
            $post = Post::findOrFail($id);
            if($post->user_id !== $user) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 403);
            }
            // return response()->json($post, 200);
            return view('posts.show',compact('post'));
        }
        catch(Exception $e)
        {
            return response()->json([
                'message' => 'Failed To Display Post',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function showTrashed()
    {
        try
        {
            $user = Auth::id();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $trashedPosts = Post::where('user_id', $user)->onlyTrashed()->get();
            // return response()->json($posts, 200);
            return view('posts.show-trashed',compact('trashedPosts'));
        }
        catch(Exception $e)
        {
            return response()->json([
                'message' => 'Failed To Display Trashed Posts',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function restorePost($id)
    {
        try
        {
            $user = Auth::id();
            $post = Post::onlyTrashed()->findOrFail($id);
            if($post->user_id !== $user) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 403);
            }
            $post->restore();
            return response()->json(['message' => 'Post Restored Successfully','title' => $post->title],200);
        }
        catch(Exception $e)
        {
            return response()->json([
                'message' => 'Failed To Restore Post',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function forceDeletePost($id)
    {
        try
        {
            $user = Auth::id();
            $post = Post::onlyTrashed()->findOrFail($id);
            if($post->user_id !== $user) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 403);
            }
            $post->forceDelete();
            return response()->json(['message' => 'Post Force Deleted Successfully','title' => $post->title],200);
        }
        catch(Exception $e)
        {
            return response()->json([
                'message' => 'Failed To Restore Post',
                'error' => $e->getMessage()
            ]);
        }
    }

}
