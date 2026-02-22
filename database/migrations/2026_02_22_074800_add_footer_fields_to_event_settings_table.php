<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_settings', function (Blueprint $table) {
            $table->string('footer_brand')->nullable()->after('rules_url');
            $table->string('footer_copyright')->nullable()->after('footer_brand');
        });
    }

    public function down(): void
    {
        Schema::table('event_settings', function (Blueprint $table) {
            $table->dropColumn([
                'footer_brand',
                'footer_copyright',
            ]);
        });
    }
};
