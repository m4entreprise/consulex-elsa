<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spectator_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->index();
            $table->string('phone');
            $table->unsignedTinyInteger('accompanying_count')->default(0);
            $table->foreignId('food_option_id')->nullable()->constrained('food_options')->nullOnDelete();
            $table->string('food_option_label')->nullable();
            $table->boolean('accepted_rgpd');
            $table->boolean('accepted_rules');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spectator_registrations');
    }
};
