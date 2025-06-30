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
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();

            $table->uuidMorphs('model');
            $table->char('label')->nullable();
            $table->char('first_name')->nullable();
            $table->char('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('street_line1')->nullable();
            $table->string('street_line2')->nullable();
            $table->string('country')->nullable();
            $table->foreignUuid('province_id')->constrained()->nullable();
            $table->foreignUuid('regency_id')->constrained()->nullable();
            $table->foreignUuid('district_id')->constrained()->nullable();
            $table->foreignUuid('village_id')->constrained()->nullable();
            $table->string('postal_code')->nullable();
            $table->boolean('status')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
