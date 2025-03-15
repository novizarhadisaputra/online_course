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
        Schema::create('coupons', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('name');
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['general', 'specific course', 'specific category', 'specific user']);
            $table->enum('discount_type', ['percent', 'fixed']);
            $table->bigInteger('discount_value');
            $table->string('code');
            $table->bigInteger('max_amount')->nullable();
            $table->bigInteger('minimum_order')->nullable();
            $table->bigInteger('max_usable_times')->default(1);
            $table->boolean('status')->default(false);
            $table->dateTime('expired_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
