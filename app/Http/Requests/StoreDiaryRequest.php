<?php

namespace App\Http\Requests;

use App\Enums\DiaryType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreDiaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sabot_count' => $this->input('sabot_count', 0),
            'slug_count' => $this->input('slug_count', 0),
            'bs_count' => $this->input('bs_count', 0),
            'shot_count' => $this->input('shot_count', 0),
        ]);
    }

    public function rules(): array
    {
        return [
            'diary_type' => [
                'required',
                Rule::enum(DiaryType::class),
            ],
            'activity_date' => ['required', 'date'],
            'departure_time' => ['required', 'date_format:H:i'],
            'return_time' => [
                'required',
                'date_format:H:i',
                'after:departure_time',
            ],

            'weather' => ['nullable', 'string', 'max:50'],
            'sunrise_time' => ['nullable', 'date_format:H:i'],
            'sunset_time' => ['nullable', 'date_format:H:i'],
            'weather_was_fetched' => ['nullable', 'boolean'],

            'hunting_methods' => ['nullable', 'array'],
            'hunting_methods.*' => [
                Rule::in(['gun', 'trap', 'net']),
            ],

            'activities' => ['nullable', 'array'],
            'activities.*' => [
                Rule::in([
                    'feeding',
                    'patrol',
                    'capture',
                    'miss',
                ]),
            ],

            'transportations' => ['nullable', 'array'],
            'transportations.*' => [
                Rule::in(['car', 'motorcycle']),
            ],

            'location' => ['required', 'string', 'max:255'],

            'has_capture' => ['required', 'boolean'],
            'capture_details' => [
                'nullable',
                'required_if:has_capture,1',
                'string',
                'max:5000',
            ],

            'has_sighting' => ['required', 'boolean'],
            'sighting_details' => [
                'nullable',
                'required_if:has_sighting,1',
                'string',
                'max:5000',
            ],

            'has_gun' => ['required', 'boolean'],
            'has_used_ammunition' => ['required', 'boolean'],

            'sabot_count' => [
                'required',
                'integer',
                'between:0,99',
            ],
            'slug_count' => [
                'required',
                'integer',
                'between:0,99',
            ],
            'bs_count' => [
                'required',
                'integer',
                'between:0,99',
            ],
            'shot_count' => [
                'required',
                'integer',
                'between:0,99',
            ],

            'notes' => ['nullable', 'string', 'max:10000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $hasGun = $this->boolean('has_gun');

                $hasUsedAmmunition = $this->boolean(
                    'has_used_ammunition'
                );

                $total = (int) $this->input('sabot_count')
                    + (int) $this->input('slug_count')
                    + (int) $this->input('bs_count')
                    + (int) $this->input('shot_count');

                if (! $hasGun && $hasUsedAmmunition) {
                    $validator->errors()->add(
                        'has_used_ammunition',
                        '銃を持ち出していない場合、'
                        .'弾は使用済みにできません。'
                    );

                    return;
                }

                if ($hasUsedAmmunition && $total === 0) {
                    $validator->errors()->add(
                        'ammunition_counts',
                        '使用した弾を1発以上入力してください。'
                    );

                    return;
                }

                if (! $hasUsedAmmunition && $total > 0) {
                    $validator->errors()->add(
                        'ammunition_counts',
                        '弾の使用が「無」の場合、'
                        .'使用数はすべて0にしてください。'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'diary_type.required' => '日誌区分を選択してください。',
            'activity_date.required' => '日付を入力してください。',
            'departure_time.required' => '出発時刻を入力してください。',
            'return_time.required' => '帰宅時刻を入力してください。',
            'return_time.after' => '帰宅時刻は出発時刻より後にしてください。',
            'location.required' => '場所を入力してください。',
            'capture_details.required_if' => '捕獲内容を入力してください。',
            'sighting_details.required_if' => '目撃内容を入力してください。',
            'sabot_count.between' => 'サボットは0〜99発で入力してください。',
            'slug_count.between' => 'スラグは0〜99発で入力してください。',
            'bs_count.between' => 'BSは0〜99発で入力してください。',
            'shot_count.between' => '散弾は0〜99発で入力してください。',
        ];
    }
}
