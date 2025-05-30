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
        Schema::create('purchases', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->integer('total_qty')->default(1);
            $table->bigInteger('total_price')->default(0);
            $table->bigInteger('total_discount')->default(0);
            $table->bigInteger('service_fee')->default(0);
            $table->bigInteger('tax_fee')->default(0);
            $table->integer('tax_percentage')->default(11);

            $table->foreignUuid('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->comment('cashier')->constrained()->cascadeOnDelete();
            $table->foreignUuid('payment_method_id')->nullable()->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
