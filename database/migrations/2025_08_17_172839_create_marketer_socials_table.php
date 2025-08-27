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
        Schema::create('marketer_socials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketer_id')->nullable();
$table->foreignId('social_id')->nullable();
$table->string('link')->nullable();
$table->integer('is_active')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketer_socials');
    }
};
