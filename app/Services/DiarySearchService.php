<?php

namespace App\Services;

use App\Models\Diary;
use Illuminate\Database\Eloquent\Builder;

class DiarySearchService
{
    /**
     * @param  Builder<Diary>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<Diary>
     */
    public function apply(
        Builder $query,
        array $filters
    ): Builder {
        $query
            ->when(
                $filters['fiscal_year'] ?? null,
                fn (Builder $query, mixed $year) => $query->where('fiscal_year', $year)
            )
            ->when(
                $filters['date_from'] ?? null,
                fn (Builder $query, mixed $date) => $query->whereDate(
                    'activity_date',
                    '>=',
                    $date
                )
            )
            ->when(
                $filters['date_to'] ?? null,
                fn (Builder $query, mixed $date) => $query->whereDate(
                    'activity_date',
                    '<=',
                    $date
                )
            )
            ->when(
                $filters['diary_type'] ?? null,
                fn (Builder $query, mixed $type) => $query->where('diary_type', $type)
            )
            ->when(
                $filters['hunting_method'] ?? null,
                fn (Builder $query, mixed $method) => $query->whereJsonContains(
                    'hunting_methods',
                    $method
                )
            )
            ->when(
                $filters['activity'] ?? null,
                fn (Builder $query, mixed $activity) => $query->whereJsonContains(
                    'activities',
                    $activity
                )
            )
            ->when(
                $filters['transportation'] ?? null,
                fn (Builder $query, mixed $transportation) => $query->whereJsonContains(
                    'transportations',
                    $transportation
                )
            )
            ->when(
                array_key_exists('has_capture', $filters),
                fn (Builder $query) => $query->where(
                    'has_capture',
                    (bool) $filters['has_capture']
                )
            )
            ->when(
                array_key_exists('has_sighting', $filters),
                fn (Builder $query) => $query->where(
                    'has_sighting',
                    (bool) $filters['has_sighting']
                )
            )
            ->when(
                array_key_exists('has_gun', $filters),
                fn (Builder $query) => $query->where(
                    'has_gun',
                    (bool) $filters['has_gun']
                )
            )
            ->when(
                array_key_exists(
                    'has_used_ammunition',
                    $filters
                ),
                fn (Builder $query) => $query->where(
                    'has_used_ammunition',
                    (bool) $filters[
                        'has_used_ammunition'
                    ]
                )
            )
            ->when(
                $filters['location'] ?? null,
                fn (Builder $query, mixed $location) => $query->where(
                    'location',
                    'like',
                    '%'.$location.'%'
                )
            );

        return $this->applySort(
            $query,
            $filters['sort'] ?? 'date_desc'
        );
    }

    /**
     * @param  Builder<Diary>  $query
     * @return Builder<Diary>
     */
    private function applySort(
        Builder $query,
        string $sort
    ): Builder {
        return match ($sort) {
            'date_asc' => $query
                ->orderBy('activity_date')
                ->orderBy('id'),

            'overall_asc' => $query
                ->orderBy('fiscal_year')
                ->orderBy('overall_number'),

            'overall_desc' => $query
                ->orderByDesc('fiscal_year')
                ->orderByDesc('overall_number'),

            'type_asc' => $query
                ->orderBy('fiscal_year')
                ->orderBy('diary_type')
                ->orderBy('type_number'),

            'type_desc' => $query
                ->orderByDesc('fiscal_year')
                ->orderBy('diary_type')
                ->orderByDesc('type_number'),

            default => $query
                ->orderByDesc('activity_date')
                ->orderByDesc('id'),
        };
    }
}
