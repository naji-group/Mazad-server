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
        Schema::create('livevars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketer_id')->nullable();
$table->string('live_video_id')->nullable();
$table->text('first_value')->nullable();
$table->text('second_value')->nullable();
$table->text('notes')->nullable();
$table->integer('is_active')->nullable();
$table->string('social')->nullable();
$table->index('live_video_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livevars');
    }
};
