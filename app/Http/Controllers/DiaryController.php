<?php

namespace App\Http\Controllers;

use App\Enums\DiaryType;
use App\Http\Requests\DiaryFilterRequest;
use App\Http\Requests\StoreDiaryRequest;
use App\Http\Requests\UpdateDiaryRequest;
use App\Models\Diary;
use App\Services\DiaryNumberService;
use App\Services\DiarySearchService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DiaryController extends Controller
{
    public function create(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user === null) {
            return to_route('login');
        }

        if (
            $user->weather_latitude === null ||
            $user->weather_longitude === null
        ) {
            return to_route('settings.location.edit')
                ->with(
                    'status',
                    '日誌を作成する前に固定地域を設定してください。'
                );
        }

        return view('diaries.create');
    }

    public function store(
        StoreDiaryRequest $request,
        DiaryNumberService $numberService
    ): RedirectResponse {
        $validated = $request->validated();
        $user = $request->user();
        $diaryType = DiaryType::from(
            $validated['diary_type']
        );

        $diary = DB::transaction(function () use ($request, $validated, $user, $diaryType, $numberService): Diary {
            $numbers = $numberService->issueForNewDiary(
                $user,
                Carbon::parse($validated['activity_date']),
                $diaryType
            );

            $hasGun = $request->boolean('has_gun');
            $hasUsedAmmunition = $hasGun
                && $request->boolean('has_used_ammunition');

            return $user->diaries()->create([
                'fiscal_year' => $numbers['fiscal_year'],
                'overall_number' => $numbers['overall_number'],
                'diary_type' => $diaryType,
                'type_number' => $numbers['type_number'],

                'activity_date' => $validated['activity_date'],
                'departure_time' => $validated['departure_time'],
                'return_time' => $validated['return_time'],

                'weather' => $validated['weather'] ?? null,
                'sunrise_time' => $validated['sunrise_time'] ?? null,
                'sunset_time' => $validated['sunset_time'] ?? null,
                'weather_fetched_at' => $request->boolean('weather_was_fetched')
                    ? now()
                    : null,

                'weather_prefecture' => $user->weather_prefecture,
                'weather_city' => $user->weather_city,
                'weather_latitude' => $user->weather_latitude,
                'weather_longitude' => $user->weather_longitude,

                'hunting_methods' => $validated['hunting_methods'] ?? [],
                'activities' => $validated['activities'] ?? [],
                'transportations' => $validated['transportations'] ?? [],

                'location' => $validated['location'],

                'has_capture' => $request->boolean('has_capture'),
                'capture_details' => $request->boolean('has_capture')
                    ? ($validated['capture_details'] ?? null)
                    : null,

                'has_sighting' => $request->boolean('has_sighting'),
                'sighting_details' => $request->boolean('has_sighting')
                    ? ($validated['sighting_details'] ?? null)
                    : null,

                'has_gun' => $hasGun,
                'has_used_ammunition' => $hasUsedAmmunition,

                'sabot_count' => $hasUsedAmmunition
                    ? $validated['sabot_count']
                    : 0,
                'slug_count' => $hasUsedAmmunition
                    ? $validated['slug_count']
                    : 0,
                'bs_count' => $hasUsedAmmunition
                    ? $validated['bs_count']
                    : 0,
                'shot_count' => $hasUsedAmmunition
                    ? $validated['shot_count']
                    : 0,

                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return to_route('diaries.index')
            ->with(
                'status',
                "{$diary->type_display_number}を登録しました。"
            );
    }

    public function index(
        DiaryFilterRequest $request,
        DiarySearchService $searchService
    ): View {
        $filters = $request->validated();

        $query = Diary::query()
            ->whereBelongsTo($request->user());

        $diaries = $searchService
            ->apply($query, $filters)
            ->paginate(10)
            ->withQueryString();

        $fiscalYears = Diary::query()
            ->whereBelongsTo($request->user())
            ->select('fiscal_year')
            ->distinct()
            ->orderByDesc('fiscal_year')
            ->pluck('fiscal_year');

        return view('diaries.index', [
            'diaries' => $diaries,
            'fiscalYears' => $fiscalYears,
            'filters' => $filters,
        ]);
    }

    public function show(Diary $diary): View
    {
        Gate::authorize('view', $diary);

        return view('diaries.show', [
            'diary' => $diary,
        ]);
    }

    public function edit(Diary $diary): View
    {
        Gate::authorize('update', $diary);

        return view('diaries.edit', [
            'diary' => $diary,
        ]);
    }

    public function update(
        UpdateDiaryRequest $request,
        Diary $diary,
        DiaryNumberService $numberService
    ): RedirectResponse {
        Gate::authorize('update', $diary);

        $validated = $request->validated();
        $user = $request->user();

        $newDiaryType = DiaryType::from(
            $validated['diary_type']
        );

        $newActivityDate = Carbon::parse(
            $validated['activity_date']
        );

        $newFiscalYear = $numberService->calculateFiscalYear(
            $newActivityDate
        );

        DB::transaction(function () use ($request, $validated, $user, $diary, $newDiaryType, $newFiscalYear, $numberService): void {
            $oldDiaryType = $diary->diary_type;
            $oldFiscalYear = $diary->fiscal_year;

            if ($newFiscalYear !== $oldFiscalYear) {
                $numbers = $numberService->issueForNewDiary(
                    $user,
                    Carbon::parse($validated['activity_date']),
                    $newDiaryType
                );

                $diary->fiscal_year =
                    $numbers['fiscal_year'];

                $diary->overall_number =
                    $numbers['overall_number'];

                $diary->type_number =
                    $numbers['type_number'];
            } elseif ($newDiaryType !== $oldDiaryType) {
                $diary->type_number =
                    $numberService->issueTypeNumber(
                        $user,
                        $oldFiscalYear,
                        $newDiaryType
                    );
            }

            $hasGun = $request->boolean('has_gun');

            $hasUsedAmmunition = $hasGun
                && $request->boolean('has_used_ammunition');

            $attributes = [
                'diary_type' => $newDiaryType,
                'activity_date' => $validated['activity_date'],
                'departure_time' => $validated['departure_time'],
                'return_time' => $validated['return_time'],

                'weather' => $validated['weather'] ?? null,
                'sunrise_time' => $validated['sunrise_time'] ?? null,
                'sunset_time' => $validated['sunset_time'] ?? null,

                'hunting_methods' => $validated['hunting_methods'] ?? [],
                'activities' => $validated['activities'] ?? [],
                'transportations' => $validated['transportations'] ?? [],

                'location' => $validated['location'],

                'has_capture' => $request->boolean('has_capture'),
                'capture_details' => $request->boolean('has_capture')
                    ? ($validated['capture_details'] ?? null)
                    : null,

                'has_sighting' => $request->boolean('has_sighting'),
                'sighting_details' => $request->boolean('has_sighting')
                    ? ($validated['sighting_details'] ?? null)
                    : null,

                'has_gun' => $hasGun,
                'has_used_ammunition' => $hasUsedAmmunition,

                'sabot_count' => $hasUsedAmmunition
                    ? $validated['sabot_count']
                    : 0,
                'slug_count' => $hasUsedAmmunition
                    ? $validated['slug_count']
                    : 0,
                'bs_count' => $hasUsedAmmunition
                    ? $validated['bs_count']
                    : 0,
                'shot_count' => $hasUsedAmmunition
                    ? $validated['shot_count']
                    : 0,

                'notes' => $validated['notes'] ?? null,
            ];

            /*
             * 編集画面で天気を再取得した場合だけ、
             * 現在の固定地域と取得日時を更新します。
             */
            if ($request->boolean('weather_was_fetched')) {
                $attributes += [
                    'weather_prefecture' => $user->weather_prefecture,
                    'weather_city' => $user->weather_city,
                    'weather_latitude' => $user->weather_latitude,
                    'weather_longitude' => $user->weather_longitude,
                    'weather_fetched_at' => now(),
                ];
            }

            $diary->fill($attributes);
            $diary->save();
        });

        return to_route('diaries.show', $diary)
            ->with('status', '日誌を更新しました。');
    }

    public function trash(): View
    {
        $diaries = auth()
            ->user()
            ->diaries()
            ->onlyTrashed()
            ->latest('deleted_at')
            ->paginate(10);

        return view('diaries.trash', [
            'diaries' => $diaries,
        ]);
    }

    public function destroy(
        Diary $diary
    ): RedirectResponse {
        Gate::authorize('delete', $diary);

        $displayNumber = $diary->type_display_number;

        $diary->delete();

        return to_route('diaries.index')
            ->with(
                'status',
                "{$displayNumber}をゴミ箱へ移動しました。"
            );
    }

    public function restore(
        Diary $diary
    ): RedirectResponse {
        Gate::authorize('restore', $diary);

        if (! $diary->trashed()) {
            return to_route('diaries.index')
                ->with('status', 'この日誌は削除されていません。');
        }

        $diary->restore();

        return to_route('diaries.trash')
            ->with(
                'status',
                "{$diary->type_display_number}を元に戻しました。"
            );
    }

    public function forceDelete(
        Diary $diary
    ): RedirectResponse {
        Gate::authorize('forceDelete', $diary);

        if (! $diary->trashed()) {
            return to_route('diaries.index')
                ->with(
                    'status',
                    '完全削除するには、先にゴミ箱へ移動してください。'
                );
        }

        $displayNumber = $diary->type_display_number;

        $diary->forceDelete();

        return to_route('diaries.trash')
            ->with(
                'status',
                "{$displayNumber}を完全に削除しました。"
            );
    }
}
