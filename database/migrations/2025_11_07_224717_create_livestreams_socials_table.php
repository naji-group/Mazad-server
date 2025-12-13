<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**  
       
     * Run the migrations.add_dislike_count_to_livestreams_socials_table
     */
    public function up(): void
    {
        Schema::create('livestreams_socials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('live_stream_id')->nullable();
$table->foreignId('social_id')->nullable();
$table->integer('real_comments_count')->nullable();
$table->integer('views_count')->nullable();
$table->integer('likes_count')->nullable();
$table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livestreams_socials');
    }
};
