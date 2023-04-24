<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean('discount_type')->default(false);
            $table->float('discount_price')->default(0);
            $table->string('img')->nullable();
            $table->string('active_time', 10)->nullable();
            $table->string('total_time', 10)->nullable();
            $table->integer('calories')->nullable();
            $table->integer('servings')->default(1)->nullable();
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
        Schema::dropIfExists('receipts');
    }
}
