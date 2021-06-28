<?php

namespace App\Http\Requests\Support\Messages;

use Illuminate\Foundation\Http\FormRequest;

class SendingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return ['message' => ['required']];
    }
}
