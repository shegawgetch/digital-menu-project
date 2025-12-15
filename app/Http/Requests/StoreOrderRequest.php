<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id', // service provider
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
        ];
    }
}
