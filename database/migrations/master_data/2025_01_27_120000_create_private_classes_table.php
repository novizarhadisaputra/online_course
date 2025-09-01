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
        Schema::create('private_classes', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();

            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('duration')->default(0);
            $table->string('duration_units')->default('minutes');
            $table->integer('max_participants')->default(1);
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('is_paid')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_classes');
    }
};