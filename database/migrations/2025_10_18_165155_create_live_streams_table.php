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
        Schema::create('live_streams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketer_id')->nullable();
            $table->string('agora_live_id'); // معرف البث في Agora
            $table->boolean('is_active')->nullable();
            $table->string('youtube_live_chat_id')->nullable();
            $table->text('youtube_access_token')->nullable();
            $table->string('facebook_live_video_id')->nullable();
            $table->text('facebook_access_token')->nullable();
            $table->string('instagram_live_video_id')->nullable();
            $table->text('instagram_access_token')->nullable();
            $table->string('tiktok_live_video_id')->nullable();
            $table->text('tiktok_access_token')->nullable();
            $table->string('jaco_live_video_id')->nullable();
            $table->text('jaco_access_token')->nullable();
         
            $table->timestamps();
            $table->index('agora_live_id');
            $table->index('marketer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_streams');
    }
};
