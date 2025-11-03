<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('live_streams', function (Blueprint $table) {
            $table->boolean('facebook_is_active')->nullable();
            $table->boolean('instagram_is_active')->nullable();
            $table->boolean('youtube_is_active')->nullable();
            $table->boolean('tiktok_is_active')->nullable();  
            $table->boolean('jaco_is_active')->nullable();
             

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_streams', function (Blueprint $table) {
            //
        });
    }
};
