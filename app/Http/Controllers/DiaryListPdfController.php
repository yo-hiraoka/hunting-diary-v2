<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiaryFilterRequest;
use App\Models\Diary;
use App\Services\DiarySearchService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class DiaryListPdfController extends Controller
{
    public function __invoke(
        DiaryFilterRequest $request,
        DiarySearchService $searchService
    ): Response {
        $filters = $request->validated();

        $query = Diary::query()
            ->whereBelongsTo($request->user());

        $diaries = $searchService
            ->apply($query, $filters)
            ->get();

        $summary = [
            'total_count' => $diaries->count(),

            'capture_count' => $diaries
                ->where('has_capture', true)
                ->count(),

            'sighting_count' => $diaries
                ->where('has_sighting', true)
                ->count(),

            'sabot_count' => (int) $diaries->sum('sabot_count'),

            'slug_count' => (int) $diaries->sum('slug_count'),

            'bs_count' => (int) $diaries->sum('bs_count'),

            'shot_count' => (int) $diaries->sum('shot_count'),
        ];

        $summary['ammunition_total'] =
            $summary['sabot_count']
            + $summary['slug_count']
            + $summary['bs_count']
            + $summary['shot_count'];

        $pdf = Pdf::loadView('diaries.pdf.index', [
            'diaries' => $diaries,
            'filters' => $filters,
            'summary' => $summary,
            'user' => $request->user(),
        ])->setPaper('a4', 'landscape');

        if ($request->boolean('print')) {
            return $pdf->stream('diary-list-'.now()->format('Ymd-His').'.pdf');
        }

        return $pdf->download(
            'diary-list-'.now()->format('Ymd-His').'.pdf'
        );
    }
}
