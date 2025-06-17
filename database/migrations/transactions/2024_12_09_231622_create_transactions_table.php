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
            $table->bigInteger('total_discount')->default(0);
            $table->bigInteger('service_fee')->default(0);
            $table->bigInteger('tax_fee')->default(0);
            $table->integer('tax_percentage')->default(11);
            $table->enum('status', ['waiting payment', 'refund', 'success', 'cancel', 'expire', 'pending', 'fail'])->default('waiting payment');
            $table->enum('category', ['debit', 'credit'])->default('debit');
            $table->json('data')->nullable();

            $table->foreignUuid('cashier_id')->nullable()->references('id')->on('users');
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('payment_method_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignUuid('coupon_id')->nullable()->constrained()->cascadeOnDelete();

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
