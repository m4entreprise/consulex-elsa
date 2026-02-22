<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate_registrations', function (Blueprint $table) {
            $table->string('study_year', 50)->nullable()->after('faculty');
        });
    }

    public function down(): void
    {
        Schema::table('candidate_registrations', function (Blueprint $table) {
            $table->dropColumn('study_year');
        });
    }
};
