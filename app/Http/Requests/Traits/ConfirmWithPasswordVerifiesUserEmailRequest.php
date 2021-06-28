<?php

namespace App\Http\Requests\Traits;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmWithPasswordVerifiesUserEmailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
'password' => [
'required',
'min:6',
],
];
    }
}
