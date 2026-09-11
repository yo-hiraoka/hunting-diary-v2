<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('weather_prefecture', 20)
                ->nullable()
                ->after('email');

            $table->string('weather_city', 100)
                ->nullable()
                ->after('weather_prefecture');

            $table->decimal('weather_latitude', 10, 7)
                ->nullable()
                ->after('weather_city');

            $table->decimal('weather_longitude', 10, 7)
                ->nullable()
                ->after('weather_latitude');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'weather_prefecture',
                'weather_city',
                'weather_latitude',
                'weather_longitude',
            ]);
        });
    }
};
