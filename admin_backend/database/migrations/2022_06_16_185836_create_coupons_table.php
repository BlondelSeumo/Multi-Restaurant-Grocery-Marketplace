<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shop_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('name')->index();
            $table->enum('type', ['fix', 'percent'])->default('fix');
            $table->integer('qty')->default(0);
            $table->double('price')->default(0);
            $table->dateTime('expired_at');
            $table->string('img')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['shop_id', 'name']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}
