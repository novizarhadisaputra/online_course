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
        Schema::create('returns', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->enum('transaction_type', ['return_in', 'return_out']);
            $table->text('notes');

            $table->foreignUuid('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->comment('cashier')->constrained()->cascadeOnDelete();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
