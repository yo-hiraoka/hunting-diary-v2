<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiarySequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fiscal_year',
        'overall_last_number',
        'hunting_last_number',
        'control_last_number',
    ];

    protected function casts(): array
    {
        return [
            'fiscal_year' => 'integer',
            'overall_last_number' => 'integer',
            'hunting_last_number' => 'integer',
            'control_last_number' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
