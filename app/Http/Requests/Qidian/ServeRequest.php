<?php

namespace App\Http\Requests\Qidian;

use Illuminate\Http\Request;

class ServeRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'signature' => 'required',
            'echostr' => 'string',
            'timestamp' => 'required',
            'nonce' => 'required',
            'encrypt_type' => 'string',
            'msg_signature' => 'string',
        ];
    }
}
