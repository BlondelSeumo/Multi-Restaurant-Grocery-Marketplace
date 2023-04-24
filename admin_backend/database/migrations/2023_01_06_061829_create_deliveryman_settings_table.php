<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliverymanSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveryman_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('type_of_technique')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('number')->nullable();
            $table->string('color')->nullable();
            $table->boolean('online')->default(false);
            $table->jsonb('location')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deliveryman_settings');
    }
}
