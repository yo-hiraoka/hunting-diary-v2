<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class DiaryPdfController extends Controller
{
    public function __invoke(Request $request, Diary $diary): Response
    {
        Gate::authorize('view', $diary);

        $pdf = Pdf::loadView('diaries.pdf.show', [
            'diary' => $diary,
        ])->setPaper('a4', 'portrait');

        $filename = sprintf(
            'diary-%d-%s-%03d.pdf',
            $diary->fiscal_year,
            $diary->diary_type->value,
            $diary->type_number
        );
        if ($request->boolean('print')) {
            return $pdf->stream(sprintf(
                'diary-%d-%s-%03d.pdf',
                $diary->fiscal_year,
                $diary->diary_type->value,
                $diary->type_number
            ));
        }

        return $pdf->download($filename);
    }
}
