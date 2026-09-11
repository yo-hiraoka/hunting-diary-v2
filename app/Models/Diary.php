<?php

namespace App\Models;

use App\Enums\DiaryType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Diary extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPE_HUNTING = 'hunting';

    public const TYPE_CONTROL = 'control';

    protected $fillable = [
        'user_id',
        'fiscal_year',
        'overall_number',
        'diary_type',
        'type_number',
        'activity_date',
        'departure_time',
        'return_time',
        'weather',
        'sunrise_time',
        'sunset_time',
        'weather_prefecture',
        'weather_city',
        'weather_latitude',
        'weather_longitude',
        'weather_fetched_at',
        'hunting_methods',
        'activities',
        'transportations',
        'location',
        'has_capture',
        'capture_details',
        'has_sighting',
        'sighting_details',
        'has_gun',
        'has_used_ammunition',
        'sabot_count',
        'slug_count',
        'bs_count',
        'shot_count',
        'notes',
    ];

    protected function overallDisplayNumber(): Attribute
    {
        return Attribute::get(
            fn (): string => sprintf(
                '%d-全-%03d',
                $this->fiscal_year,
                $this->overall_number
            )
        );
    }

    protected function typeDisplayNumber(): Attribute
    {
        return Attribute::get(
            fn (): string => sprintf(
                '%d-%s-%03d',
                $this->fiscal_year,
                $this->diary_type->abbreviation(),
                $this->type_number
            )
        );
    }

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'weather_latitude' => 'decimal:7',
            'weather_longitude' => 'decimal:7',
            'weather_fetched_at' => 'datetime',
            'hunting_methods' => 'array',
            'activities' => 'array',
            'transportations' => 'array',
            'has_capture' => 'boolean',
            'has_sighting' => 'boolean',
            'has_gun' => 'boolean',
            'has_used_ammunition' => 'boolean',
            'sabot_count' => 'integer',
            'slug_count' => 'integer',
            'bs_count' => 'integer',
            'shot_count' => 'integer',
            'diary_type' => DiaryType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
