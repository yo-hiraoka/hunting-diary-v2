<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'diary_sequences',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('user_id')
                    ->constrained()
                    ->cascadeOnDelete();

                $table->unsignedSmallInteger('fiscal_year');

                $table->unsignedInteger('overall_last_number')
                    ->default(0);

                $table->unsignedInteger('hunting_last_number')
                    ->default(0);

                $table->unsignedInteger('control_last_number')
                    ->default(0);

                $table->timestamps();

                $table->unique([
                    'user_id',
                    'fiscal_year',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('diary_sequences');
    }
};
