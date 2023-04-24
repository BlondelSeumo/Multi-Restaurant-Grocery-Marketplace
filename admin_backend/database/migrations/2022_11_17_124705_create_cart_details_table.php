<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('cart_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('user_cart_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->integer('quantity')->default(0);
            $table->double('price')->nullable();
            $table->boolean('bonus')->default(false);
            $table->double('discount')->default(0);
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('cart_details')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_details');
    }
}
