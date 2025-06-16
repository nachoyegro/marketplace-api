<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVariationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create', Variation::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'benefit_id' => 'required|exists:benefits,id',
            'title' => 'required|string|max:255',
            'cost' => 'required|decimal:0,2',
            'price' => 'required|decimal:0,2',
            'price_credits' => 'required|integer|min:0',
        ];
    }
}

