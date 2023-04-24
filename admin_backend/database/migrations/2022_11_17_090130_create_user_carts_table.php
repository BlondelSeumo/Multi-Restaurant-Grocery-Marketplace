<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_carts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('user_id')->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->boolean('status')->default(true);
            $table->string('name')->nullable();
            $table->uuid('uuid')->nullable();

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
        Schema::dropIfExists('user_carts');
    }
}
