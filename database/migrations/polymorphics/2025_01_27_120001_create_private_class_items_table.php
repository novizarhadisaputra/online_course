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
        Schema::create('private_class_items', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuidMorphs('model');
            $table->foreignUuid('private_class_id')->constrained()->cascadeOnDelete();
            $table->integer('order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_class_items');
    }
};