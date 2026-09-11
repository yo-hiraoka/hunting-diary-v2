<?php

namespace App\Services;

use App\Enums\DiaryType;
use App\Models\DiarySequence;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class DiaryNumberService
{
    /**
     * 新しい日誌の全体番号と区分番号を発行します。
     *
     * @return array{
     *     fiscal_year: int,
     *     overall_number: int,
     *     type_number: int
     * }
     */
    public function issueForNewDiary(
        User $user,
        CarbonInterface $activityDate,
        DiaryType $diaryType
    ): array {
        $fiscalYear = $this->calculateFiscalYear($activityDate);

        return DB::transaction(function () use ($user, $fiscalYear, $diaryType): array {
            $sequence = $this->lockSequence(
                $user,
                $fiscalYear
            );

            $sequence->overall_last_number++;

            $typeNumber = $this->incrementTypeNumber(
                $sequence,
                $diaryType
            );

            $sequence->save();

            return [
                'fiscal_year' => $fiscalYear,
                'overall_number' => $sequence->overall_last_number,
                'type_number' => $typeNumber,
            ];
        });
    }

    /**
     * 区分変更時の新しい区分番号だけを発行します。
     */
    public function issueTypeNumber(
        User $user,
        int $fiscalYear,
        DiaryType $diaryType
    ): int {
        return DB::transaction(function () use ($user, $fiscalYear, $diaryType): int {
            $sequence = $this->lockSequence(
                $user,
                $fiscalYear
            );

            $typeNumber = $this->incrementTypeNumber(
                $sequence,
                $diaryType
            );

            $sequence->save();

            return $typeNumber;
        });
    }

    public function calculateFiscalYear(
        CarbonInterface $activityDate
    ): int {
        if ($activityDate->month >= 4) {
            return $activityDate->year;
        }

        return $activityDate->year - 1;
    }

    private function lockSequence(
        User $user,
        int $fiscalYear
    ): DiarySequence {
        $now = now();

        DiarySequence::query()->insertOrIgnore([
            'user_id' => $user->id,
            'fiscal_year' => $fiscalYear,
            'overall_last_number' => 0,
            'hunting_last_number' => 0,
            'control_last_number' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return DiarySequence::query()
            ->where('user_id', $user->id)
            ->where('fiscal_year', $fiscalYear)
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function incrementTypeNumber(
        DiarySequence $sequence,
        DiaryType $diaryType
    ): int {
        return match ($diaryType) {
            DiaryType::Hunting => ++$sequence->hunting_last_number,

            DiaryType::Control => ++$sequence->control_last_number,
        };
    }
}
