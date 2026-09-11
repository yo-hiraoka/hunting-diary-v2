<?php

namespace App\Http\Requests;

use App\Enums\DiaryType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiaryFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fiscal_year' => [
                'nullable',
                'integer',
                'between:2000,2100',
            ],
            'date_from' => ['nullable', 'date'],
            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],
            'diary_type' => [
                'nullable',
                Rule::enum(DiaryType::class),
            ],
            'hunting_method' => [
                'nullable',
                Rule::in(['gun', 'trap', 'net']),
            ],
            'activity' => [
                'nullable',
                Rule::in([
                    'feeding',
                    'patrol',
                    'capture',
                    'miss',
                ]),
            ],
            'transportation' => [
                'nullable',
                Rule::in(['car', 'motorcycle']),
            ],
            'has_capture' => [
                'nullable',
                Rule::in(['0', '1']),
            ],
            'has_sighting' => [
                'nullable',
                Rule::in(['0', '1']),
            ],
            'has_gun' => [
                'nullable',
                Rule::in(['0', '1']),
            ],
            'has_used_ammunition' => [
                'nullable',
                Rule::in(['0', '1']),
            ],
            'location' => [
                'nullable',
                'string',
                'max:255',
            ],
            'sort' => [
                'nullable',
                Rule::in([
                    'date_desc',
                    'date_asc',
                    'overall_asc',
                    'overall_desc',
                    'type_asc',
                    'type_desc',
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'date_to.after_or_equal' => '終了日は開始日以降の日付にしてください。',
        ];
    }
}
