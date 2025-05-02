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
        Schema::create('specialists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('specialization');
            $table->text('bio')->nullable();
            $table->text('education')->nullable();
            $table->text('experience')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('languages')->nullable();
            $table->integer('years_of_experience')->default(0);
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->decimal('session_rate', 10, 2)->default(0);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('available_days')->nullable();
            $table->json('available_hours')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialists');
    }
};
