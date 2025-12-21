<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Infra\Requests;

use Hyperf\Validation\Request\FormRequest;

class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payer' => 'required|integer|exists:users,id',
            'payee' => 'required|integer|exists:users,id|different:payer',
            'value' => 'required|numeric|min:0.01|max:999999.99',
            'idempotency_key' => 'required|uuid',
        ];
    }
}
