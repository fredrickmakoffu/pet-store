<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\IndexRequest;
use App\Http\Requests\Category\StoreRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Category;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(IndexRequest $request) : CollectionResource
    {
        $data = Category::filter($request->validated())->get();

        return new CollectionResource($data);
    }

    public function show(Category $category) : CollectionResource
    {
        return new CollectionResource($category);
    }

    public function store(StoreRequest $request) : CollectionResource
    {
        $category = Category::create(array_merge($request->validated(), [
            'uuid' => Uuid::uuid4()
        ]));

        return new CollectionResource($category);
    }

    public function update(StoreRequest $request, Category $category) : CollectionResource
    {
        $category->update($request->validated());

        return new CollectionResource($category);
    }

    public function destroy(Category $category) : CollectionResource
    {   
        DB::beginTransaction();

        $category->delete();

        // delete all related products
        $category->products()->delete();

        DB::commit();

        return new CollectionResource($category);
    }
}
