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
        Schema::create('question_and_answers', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('question')->unique();
            $table->text('answer');
            $table->boolean('status')->default(true);

            $table->foreignUuid('question_and_answer_category_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_and_answers');
    }
};
