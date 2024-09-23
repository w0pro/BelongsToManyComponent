<?php

namespace Wopro\BelongsToManyComponent\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BelongsToManySortRequest extends FormRequest
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
            'POST' => [
                'objectName' => 'required|string',
                'subjectModel' => 'required|string',
                'sortData' => 'required|array',
            ],
        };


    }
}
