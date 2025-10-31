<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PostResource;
use Illuminate\Http\Request;
use App\Http\Requests\V1\PostStoreRequest;
use App\Http\Requests\V1\PostUpdateRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Events\PostChanged;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with('user')->get();

        return response()->json([
            'success' => true,
            'message' => 'All post retrived successfully',
            'data' => PostResource::collection($posts),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request)
    {
        $this->authorize('create', Post::class);

        $validated = $request->validated();

        $post = DB::transaction(function () use ($request, $validated) {
            $post = $request->user()->posts()->create($validated);

            broadcast(new PostChanged('created', $post));

            return $post;
        });

        return response()->json([
            'success' => true,
            'message' => 'Post stored successfully',
            'data' => new PostResource($post),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $this->authorize('view', $post);

        return response()->json([
            'success' => true,
            'message' => 'Post show successfully',
            'data' => new PostResource($post),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validated();

        $post = DB::transaction(function () use ($post, $validated) {
            $post->update($validated);

            broadcast(new PostChanged('updated', $post));
            return $post;
        });

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => new PostResource($post),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post = DB::transaction(function () use ($post) {
            $post->delete();

            broadcast(new PostChanged('deleted', $post));
        });

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully'
        ], 200);
    }
}
