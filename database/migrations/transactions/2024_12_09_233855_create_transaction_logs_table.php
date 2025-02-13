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
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('payment_method')->nullable();
            $table->string('payment_channel')->nullable();
            $table->integer('total_qty')->default(1);
            $table->bigInteger('total_price')->default(0);

            $table->foreignUuid('transaction_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['waiting payment', 'refund', 'success', 'cancel']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_logs');
    }
};
