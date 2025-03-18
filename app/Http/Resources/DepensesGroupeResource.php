<?php

namespace App\Http\Resources;

use App\Models\Devise;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class depensesGroupeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // 'id' => $this->id,
            'name' => $this->name,
            'devise'=> $this->devise,
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
        ];
    }
}
