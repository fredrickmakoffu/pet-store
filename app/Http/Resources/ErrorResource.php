<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->error($request);
    }

    /**
     * Return an error response.
     *
     * @param Request $request
     * @return array<string>
     */
    public function error(Request $request): array
    {
        return [
            'message' => 'An error occurred',
            'error' => $this->resource->getMessage()
        ];
    }
}
