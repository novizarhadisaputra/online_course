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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->comment('sales, returns')->nullableUuidMorphs('model');
            $table->enum('transaction_type', ['top_up', 'payment', 'refund', 'adjustment']);
            $table->bigInteger('amount');
            $table->text('notes')->nullable();

            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
