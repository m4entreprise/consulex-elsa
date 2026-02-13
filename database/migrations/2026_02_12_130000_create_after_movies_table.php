<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('after_movies', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('location');
            $table->string('theme');
            $table->string('winner');
            $table->string('aftermovie_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('after_movies');
    }
};
