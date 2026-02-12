<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->default('default');
            $table->string('event_title')->nullable();
            $table->string('event_theme')->nullable();
            $table->string('event_date')->nullable();
            $table->string('event_location')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('aftermovie_url')->nullable();
            $table->string('privacy_policy_url')->nullable();
            $table->string('rules_url')->nullable();
            $table->unsignedInteger('spectator_capacity')->default(200);
            $table->unsignedInteger('candidate_capacity')->default(40);
            $table->boolean('spectator_registrations_enabled')->default(true);
            $table->boolean('candidate_registrations_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_settings');
    }
};
