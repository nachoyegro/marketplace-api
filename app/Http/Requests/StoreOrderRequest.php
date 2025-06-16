<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Order;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create', Order::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        
        return [
            'employee_id' => 'required|exists:employees,id',
            'company_id' => 'required|exists:companies,id',
            'variation_id' => 'required|exists:variations,id',
            'gift_card_id' => 'required|exists:gift_cards,id',
            'cost' => 'required|decimal:0,2|min:0',
            'sale_price' => 'required|decimal:0,2|min:0',
            'sale_price_credits' => 'required|integer|min:0',
        ];
    }
}
