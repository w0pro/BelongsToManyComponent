<?php

namespace Wopro\BelongsToManyComponent\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BelongsToManyFullRelationsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->method()) {
            'GET' => [
                'objectName' => 'required|string',
            ],

        };


    }
}
