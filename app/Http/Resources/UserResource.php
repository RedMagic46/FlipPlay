<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'membership_type' => $this->membership_type,
            'collections' => MediaResource::collection($this->whenLoaded('media')),
            'collections_count' => $this->when(isset($this->media_count), $this->media_count),
            'collection_meta' => $this->whenPivotLoaded('media_user', function () {
                return [
                    'notes' => $this->pivot->notes,
                    'added_at' => $this->pivot->created_at?->toIso8601String(),
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
