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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuidMorphs('model'); // purchases, sale, sale_return, purchase_return, stock_transfer_in, stock_transfer_out, adjustment, initial_stock
            $table->enum('type', ['in', 'out']);
            $table->bigInteger('qty')->default(0);
            $table->text('notes')->nullable();

            $table->foreignUuid('branch_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_id')->unique()->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
