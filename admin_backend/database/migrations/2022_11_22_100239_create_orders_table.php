<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id()->from(1000);
            $table->foreignId('user_id');
            $table->double('total_price', 20)->comment('Сумма с учётом всех налогов и скидок');

            $table->foreignId('currency_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->float('rate')->default(1);
            $table->string('note', 191)->nullable();

            $table->foreignId('shop_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->double('tax')->default(1);
            $table->double('commission_fee')->nullable();
            $table->string('status')->default('new');
            $table->string('location')->nullable();
            $table->string('address')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('username')->nullable();

            $table->double('delivery_fee', 20)->default(0);

            $table->foreignId('deliveryman')->nullable()->index();
            $table->date('delivery_date')->nullable();
            $table->string('delivery_time')->nullable();
            $table->integer('total_discount')->nullable();
            $table->string('delivery_type')->default(Order::PICKUP);
            $table->boolean('current')->default(false);

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
        Schema::dropIfExists('orders');
    }
}
