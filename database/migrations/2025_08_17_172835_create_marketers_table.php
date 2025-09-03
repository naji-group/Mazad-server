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
        Schema::create('marketers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
$table->string('last_name')->nullable();
$table->string('username')->nullable();
$table->string('password')->nullable();
$table->integer('is_active')->nullable();
$table->string('email')->nullable();
$table->string('image')->nullable();
$table->string('provider')->nullable();
$table->string('provider_user_id')->nullable();
$table->string('provider_token')->nullable();
$table->string('provider_refresh_token')->nullable();
 
$table->foreignId('social_id')->nullable();
$table->timestamp('email_verified_at')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketers');
    }
};
