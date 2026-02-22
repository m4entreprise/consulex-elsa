<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spectator_registrations', function (Blueprint $table) {
            $table->json('accompanying_people')->nullable()->after('accompanying_count');
            $table->boolean('food_wanted')->default(false)->after('food_option_label');
            $table->json('food_quantities')->nullable()->after('food_wanted');
        });
    }

    public function down(): void
    {
        Schema::table('spectator_registrations', function (Blueprint $table) {
            $table->dropColumn(['accompanying_people', 'food_wanted', 'food_quantities']);
        });
    }
};
