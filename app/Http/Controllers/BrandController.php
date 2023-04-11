<?php

namespace App\Http\Controllers;

use App\Http\Requests\Brand\StoreRequest;
use App\Http\Requests\Brand\IndexRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Brand;
use Ramsey\Uuid\Uuid;

class BrandController extends Controller
{
    public function index(IndexRequest $request) : CollectionResource
    {
        $data = Brand::filter($request->validated())->get();
        return new CollectionResource($data);
    }

    public function store(StoreRequest $request) : CollectionResource
    {
        $data = Brand::create(array_merge($request->validated(), [
            'uuid' => Uuid::uuid4()
        ]));

        return new CollectionResource($data);
    }

    public function show(Brand $brand) : CollectionResource
    {
        return new CollectionResource($brand);
    }

    public function update(StoreRequest $request, Brand $brand) : CollectionResource
    {
        $brand->update($request->validated());
        return new CollectionResource($brand);
    }

    public function destroy(Brand $brand) : CollectionResource
    {
        $brand->delete();
        return new CollectionResource($brand);
    }
}
