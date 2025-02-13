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
        Schema::create('config_apps', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->integer('tax_fee')->comment("In percentage");
            $table->bigInteger('service_fee')->comment("In nominal");
            $table->string('call_center')->nullable()->comment("example 62xxxxxxxxx");
            $table->string('email_help_center')->nullable()->comment("example email@domain.com");
            $table->longText('terms_and_conditions')->nullable();
            $table->longText('privacy_policy')->nullable();
            $table->string('success_redirect_url')->nullable();
            $table->string('failure_redirect_url')->nullable();

            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_apps');
    }
};
