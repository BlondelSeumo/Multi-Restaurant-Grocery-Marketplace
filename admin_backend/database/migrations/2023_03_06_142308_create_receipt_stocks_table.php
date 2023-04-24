<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('receipt_stocks', function (Blueprint $table) {
            $table->foreignId('receipt_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('stock_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('min_quantity')->default(1);
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
        Schema::dropIfExists('receipt_stocks');
    }
}
