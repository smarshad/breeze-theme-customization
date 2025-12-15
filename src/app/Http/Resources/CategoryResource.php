<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Auth::user();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'color_code' => $this->color_code,
            'created_by' => $this->created_by,
            'is_active' => (bool) $this->is_active ? TRUE : FALSE, // Enhanced field
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            // --- PERMISSIONS ---
            // Add a 'can' object that holds the permissions for this specific category.
            // The when($user, ...) ensures this only runs if a user is authenticated.
            'can' => $this->when($user, [
                'update' => $user->can('update', $this->resource),
                'delete' => $user->can('delete', $this->resource),
            ]),
        ];
    }
}