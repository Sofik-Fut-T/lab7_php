<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'sku'        => $this->sku,
            'quantity'   => (int) $this->quantity,
            'owner_id'   => $this->user_id,
            'category'   => $this->whenLoaded('category', fn () => [
                'id'    => $this->category->id,
                'title' => $this->category->title,
            ]),
            'suppliers'  => $this->whenLoaded('suppliers', fn () =>
                $this->suppliers->map(fn ($supplier) => [
                    'id'   => $supplier->id,
                    'name' => $supplier->name,
                ])
            ),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
