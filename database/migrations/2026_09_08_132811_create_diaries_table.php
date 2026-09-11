<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diaries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // 年度・通し番号
            $table->unsignedSmallInteger('fiscal_year');
            $table->unsignedInteger('overall_number');
            $table->string('diary_type', 20);
            $table->unsignedInteger('type_number');

            // 日時
            $table->date('activity_date');
            $table->time('departure_time');
            $table->time('return_time');

            // 天気
            $table->string('weather', 50)->nullable();
            $table->time('sunrise_time')->nullable();
            $table->time('sunset_time')->nullable();

            $table->string('weather_prefecture', 20);
            $table->string('weather_city', 100);

            $table->decimal('weather_latitude', 10, 7);
            $table->decimal('weather_longitude', 10, 7);

            $table->timestamp('weather_fetched_at')
                ->nullable();

            // 複数選択項目
            $table->json('hunting_methods');
            $table->json('activities');
            $table->json('transportations');

            // 活動場所
            $table->string('location');

            // 捕獲
            $table->boolean('has_capture')->default(false);
            $table->text('capture_details')->nullable();

            // 目撃
            $table->boolean('has_sighting')->default(false);
            $table->text('sighting_details')->nullable();

            // 銃と弾
            $table->boolean('has_gun')->default(false);

            $table->boolean('has_used_ammunition')
                ->default(false);

            $table->unsignedInteger('sabot_count')
                ->default(0);

            $table->unsignedInteger('slug_count')
                ->default(0);

            $table->unsignedInteger('bs_count')
                ->default(0);

            $table->unsignedInteger('shot_count')
                ->default(0);

            // 注釈
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // ユーザー・年度ごとに全体番号を一意にする
            $table->unique([
                'user_id',
                'fiscal_year',
                'overall_number',
            ]);

            // ユーザー・年度・区分ごとに番号を一意にする
            $table->unique([
                'user_id',
                'fiscal_year',
                'diary_type',
                'type_number',
            ]);

            // 一覧検索用
            $table->index([
                'user_id',
                'activity_date',
            ]);

            $table->index([
                'user_id',
                'fiscal_year',
                'diary_type',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diaries');
    }
};
