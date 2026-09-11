<?php

namespace Tests\Feature\Services;

use App\Enums\DiaryType;
use App\Models\User;
use App\Services\DiaryNumberService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiaryNumberServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_april_first_belongs_to_new_fiscal_year(): void
    {
        $service = app(DiaryNumberService::class);

        $fiscalYear = $service->calculateFiscalYear(
            Carbon::parse('2026-04-01')
        );

        $this->assertSame(2026, $fiscalYear);
    }

    public function test_march_last_belongs_to_previous_fiscal_year(): void
    {
        $service = app(DiaryNumberService::class);

        $fiscalYear = $service->calculateFiscalYear(
            Carbon::parse('2027-03-31')
        );

        $this->assertSame(2026, $fiscalYear);
    }

    public function test_it_issues_overall_and_type_numbers(): void
    {
        $user = User::factory()->create();
        $service = app(DiaryNumberService::class);
        $date = Carbon::parse('2026-06-01');

        $firstHunting = $service->issueForNewDiary(
            $user,
            $date,
            DiaryType::Hunting
        );

        $firstControl = $service->issueForNewDiary(
            $user,
            $date,
            DiaryType::Control
        );

        $secondHunting = $service->issueForNewDiary(
            $user,
            $date,
            DiaryType::Hunting
        );

        $this->assertSame([
            'fiscal_year' => 2026,
            'overall_number' => 1,
            'type_number' => 1,
        ], $firstHunting);

        $this->assertSame([
            'fiscal_year' => 2026,
            'overall_number' => 2,
            'type_number' => 1,
        ], $firstControl);

        $this->assertSame([
            'fiscal_year' => 2026,
            'overall_number' => 3,
            'type_number' => 2,
        ], $secondHunting);
    }

    public function test_type_change_does_not_issue_new_overall_number(): void
    {
        $user = User::factory()->create();
        $service = app(DiaryNumberService::class);

        $numbers = $service->issueForNewDiary(
            $user,
            Carbon::parse('2026-06-01'),
            DiaryType::Hunting
        );

        $controlNumber = $service->issueTypeNumber(
            $user,
            $numbers['fiscal_year'],
            DiaryType::Control
        );

        $this->assertSame(1, $numbers['overall_number']);
        $this->assertSame(1, $controlNumber);
    }
}
