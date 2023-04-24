<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();

            $table->foreignId('shop_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('brand_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnUpdate();

            $table->foreignId('unit_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('keywords', 191)->nullable();
            $table->string('img')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('bar_code')->nullable();
            $table->double('tax')->nullable();
            $table->boolean('active')->default(0);
            $table->enum('status', Product::STATUSES)->default(Product::PENDING);
            $table->integer('min_qty')->default(1);
            $table->integer('max_qty')->default(1000000);
            $table->boolean('addon')->default(false);
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
        Schema::dropIfExists('products');
    }
}
