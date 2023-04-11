<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\IndexRequest;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Product;
use Ramsey\Uuid\Uuid;

class ProductController extends Controller
{
    public function index(IndexRequest $request) : CollectionResource
    {
        $data = Product::with('category:title,slug')
            ->filter($request->validated())->get();

        return new CollectionResource($data);
    }

    public function show(Product $product) : CollectionResource
    {
        return new CollectionResource($product);
    }

    public function store(StoreRequest $request) : CollectionResource
    {
        $product = Product::create(array_merge($request->validated(), [
            'uuid' => Uuid::uuid4(),
            'metadata' => json_encode($request->validated()['metadata'])
        ]));

        return new CollectionResource($product);
    }

    public function update(UpdateRequest $request, Product $product) : CollectionResource
    {
        $product->update($request->validated());

        return new CollectionResource($product);
    }

    public function destroy(Product $product) : CollectionResource
    {
        $product->delete();

        return new CollectionResource($product);
    }
}
