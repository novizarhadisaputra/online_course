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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('code')->nullable();
            $table->string('payment_link')->nullable();
            $table->integer('total_qty')->default(1);
            $table->bigInteger('total_price')->default(0);
            $table->bigInteger('service_fee')->default(0);
            $table->bigInteger('tax_fee')->default(0);
            $table->enum('status', ['waiting payment', 'refund', 'success', 'cancel'])->default('waiting payment');
            $table->enum('category', ['debit', 'credit'])->default('debit');

            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('payment_channel_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
