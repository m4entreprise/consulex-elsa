<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_settings', function (Blueprint $table) {
            $table->dateTime('spectator_registrations_end_at')->nullable()->after('spectator_registrations_enabled');
            $table->dateTime('candidate_registrations_end_at')->nullable()->after('candidate_registrations_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('event_settings', function (Blueprint $table) {
            $table->dropColumn('spectator_registrations_end_at');
            $table->dropColumn('candidate_registrations_end_at');
        });
    }
};
