<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_has_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();

            //If Selected Record On the model
            $table->uuidMorphs('model');

            $table->string('provider')->default('pusher')->nullable();
            $table->string('provider_token')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_has_notifications');
    }
};
