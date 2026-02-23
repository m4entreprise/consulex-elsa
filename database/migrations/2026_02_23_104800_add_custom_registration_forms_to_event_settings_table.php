<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_settings', function (Blueprint $table) {
            $table->boolean('spectator_custom_form_enabled')->default(false)->after('spectator_registrations_end_at');
            $table->string('spectator_custom_form_url', 2000)->nullable()->after('spectator_custom_form_enabled');

            $table->boolean('candidate_custom_form_enabled')->default(false)->after('candidate_registrations_end_at');
            $table->string('candidate_custom_form_url', 2000)->nullable()->after('candidate_custom_form_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('event_settings', function (Blueprint $table) {
            $table->dropColumn('spectator_custom_form_enabled');
            $table->dropColumn('spectator_custom_form_url');
            $table->dropColumn('candidate_custom_form_enabled');
            $table->dropColumn('candidate_custom_form_url');
        });
    }
};
