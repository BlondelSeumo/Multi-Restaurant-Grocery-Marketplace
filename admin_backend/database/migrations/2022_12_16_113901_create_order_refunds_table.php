<?php

use App\Models\OrderRefund;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_refunds', function (Blueprint $table) {
            $table->id();
            $table->enum('status', OrderRefund::STATUSES)
                ->default(OrderRefund::STATUS_PENDING)
                ->index();
            $table->text('cause')->nullable();
            $table->text('answer')->nullable();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->softDeletes();
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
        Schema::dropIfExists('order_refunds');
    }
}
