<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\IndexRequest;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Post;
use Ramsey\Uuid\Uuid;

class PostsController extends Controller
{
    public function index(IndexRequest $request) : CollectionResource
    {
        $data = Post::filter($request->validated())->get();

        return new CollectionResource($data);
    }

    public function show(Post $post) : CollectionResource
    {
        return new CollectionResource($post);
    }

    public function store(StoreRequest $request) : CollectionResource
    {
        $post = Post::create(array_merge($request->validated(), [
            'uuid' => Uuid::uuid4()
        ]));

        return new CollectionResource($post);
    }

    public function update(StoreRequest $request, Post $post) : CollectionResource
    {
        $post->update($request->validated());

        return new CollectionResource($post);
    }

    public function destroy(Post $post) : CollectionResource
    {
        $post->delete();

        return new CollectionResource($post);
    }
}
