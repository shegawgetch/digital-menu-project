<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
{
    public function toArray($request)
    {
        $price = (float) ($this->price ?? 0);
        $tax = (float) ($this->tax_percentage ?? 0);
        $discount = (float) ($this->discount ?? 0);

        $finalPrice = ($price + ($price * $tax / 100)) - $discount;

        return [
            'id' => $this->id,
            'item_name' => $this->item_name,
            'price' => $price,
            'tax_percentage' => $tax,
            'discount' => $discount,
            'final_price' => $finalPrice,
            'photo' => $this->photo ? url('storage/'.$this->photo) : null,
        ];
    }
}
