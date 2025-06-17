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
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->enum('meeting_type', ['online', 'offline']);
            $table->boolean('is_paid')->default(false);
            $table->boolean('status')->default(false);
            $table->string('url')->nullable();
            $table->json('meta')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');

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
        Schema::dropIfExists('events');
    }
};
