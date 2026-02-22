<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_settings', function (Blueprint $table) {
            $table->unsignedInteger('event_edition_year')->nullable()->after('key');
            $table->text('event_description')->nullable()->after('event_title');

            $table->string('venue_room_name')->nullable()->after('event_location');
            $table->string('venue_room_title')->nullable()->after('venue_room_name');

            $table->string('map_place_label')->nullable()->after('venue_room_title');
            $table->text('map_address')->nullable()->after('map_place_label');
            $table->text('map_open_url')->nullable()->after('map_address');
            $table->text('map_embed_url')->nullable()->after('map_open_url');

            $table->text('access_text')->nullable()->after('map_embed_url');
            $table->string('network_text')->nullable()->after('access_text');

            $table->json('timeline')->nullable()->after('network_text');
        });
    }

    public function down(): void
    {
        Schema::table('event_settings', function (Blueprint $table) {
            $table->dropColumn([
                'event_edition_year',
                'event_description',
                'venue_room_name',
                'venue_room_title',
                'map_place_label',
                'map_address',
                'map_open_url',
                'map_embed_url',
                'access_text',
                'network_text',
                'timeline',
            ]);
        });
    }
};
