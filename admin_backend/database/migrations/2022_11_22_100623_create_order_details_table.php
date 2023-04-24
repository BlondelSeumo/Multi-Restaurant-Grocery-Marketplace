<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id()->from(500);

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('stock_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->double('origin_price')->default(0);
            $table->double('total_price')->default(0);
            $table->double('tax', 20)->default(0);
            $table->double('discount', 20)->default(0);
            $table->integer('quantity')->default(0);
            $table->boolean('bonus')->default(false);
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('order_details')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
}
