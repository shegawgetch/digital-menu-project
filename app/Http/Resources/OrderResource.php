<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'service_provider' => [
                'id' => $this->user_id,
                'name' => $this->serviceProvider->business_name,
            ],
            'customer_name' => $this->customer_name,
            'phone' => $this->phone,
            'items' => $this->items,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
