<?php

namespace App\Http\Controllers;

use App\Http\Requests\Promotion\IndexRequest;
use App\Http\Requests\Promotion\StoreRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Promotion;
use Ramsey\Uuid\Uuid;

class PromotionController extends Controller
{
    public function index(IndexRequest $request) : CollectionResource {
        $promotions = Promotion::filter($request->validated())->get();
        
        return new CollectionResource($promotions);
    }

    public function show(Promotion $promotion) : CollectionResource {
        return new CollectionResource($promotion);
    }

    public function store(StoreRequest $request) : CollectionResource {
        $promotion = Promotion::create(array_merge($request->validated(), [
            'uuid' => Uuid::uuid4(),
            'metadata' => json_encode($request->validated())
        ]));

        return new CollectionResource($promotion);
    }

    public function update(StoreRequest $request, Promotion $promotion) : CollectionResource {
        $promotion->update(array_merge($request->validated(), [
            'metadata' => json_encode($request->validated())
        ]));

        return new CollectionResource($promotion);
    }

    public function destroy(Promotion $promotion) : CollectionResource {
        $promotion->delete();

        return new CollectionResource($promotion);
    }
}
