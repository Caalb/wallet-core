<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infra\Requests;

use Hyperf\Validation\Request\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'cpf' => 'required_without:cnpj|string|size:11',
            'cnpj' => 'required_without:cpf|string|size:14',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|max:255',
            'type' => 'nullable|string|in:COMMON,MERCHANT',
        ];
    }
}
