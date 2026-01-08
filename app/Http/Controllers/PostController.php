<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Exception;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        try
        {
            $user = Auth::id();
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $posts = Post::where('user_id', $user)->get();
            $posts = Post::paginate(10);
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
            $data = $request->validate([
            'user_id' => 'exists:user,id',
            'title' => 'required|string',
            'content' => 'required|string'
        ]);
            $data['user_id'] = Auth::id();
            $post = Post::create($data);
            return response()->json($post,201);
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

    public function displayPage()
    {
        return view('posts');
    }

}
