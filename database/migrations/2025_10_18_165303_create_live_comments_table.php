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
        Schema::create('live_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketer_id')->nullable();
            $table->foreignId('live_streams')->nullable();            
            $table->string('agora_live_id')->nullable();
            $table->string('platform')->comment('facebook|youtube|instagram|tiktok|jaco');
            $table->string('comment_id')->comment('id from platform');
            $table->string('author_name')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('comment_time')->nullable();
            $table->timestamps();
            $table->unique(['platform','comment_id']);           
            $table->index('agora_live_id');
            $table->index(['platform','comment_time']);
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_comments');
    }
};
