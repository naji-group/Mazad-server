<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketer_id')->nullable();
            $table->string('live_video_id')->nullable();
            $table->integer('is_active')->nullable();
            $table->decimal('price')->nullable();
            $table->foreignId('social_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->text('customer_link')->nullable();
            $table->index('live_video_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auctios');
    }
};
