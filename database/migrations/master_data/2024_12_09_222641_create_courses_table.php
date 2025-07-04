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
        Schema::create('courses', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->text('requirement')->nullable();
            $table->bigInteger('duration')->default(0);
            $table->char('duration_units')->default('minutes');
            $table->enum('level', ['beginner', 'middle', 'advance']);
            $table->string('language')->default('Bahasa Indonesia');
            $table->boolean('is_paid')->default(false);
            $table->boolean('status')->default(false);
            $table->boolean('is_get_certificate')->default(false);
            $table->json('meta')->nullable();

            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('category_id')->constrained()->cascadeOnDelete();

            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
