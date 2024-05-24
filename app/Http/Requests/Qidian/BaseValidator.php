<?php
/**
 * Author david you
 * Date 2024/5/20
 * Time 14:19
 */

namespace App\Http\Requests\Qidian;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class BaseValidator
{
    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return $validator->errors();
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
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
