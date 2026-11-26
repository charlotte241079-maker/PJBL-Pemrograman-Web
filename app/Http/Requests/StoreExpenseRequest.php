<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null
            && $this->user()->isUmkm()
            && $this->user()->umkm !== null
            && $this->user()->umkm->status === 'approved';
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|gt:0',
            'expense_date' => 'required|date',
            'description' => 'nullable|string',
            'purchase_id' => 'prohibited',
            'umkm_id' => 'prohibited',
        ];
    }
}
