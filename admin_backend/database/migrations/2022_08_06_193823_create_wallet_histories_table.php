<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_histories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignUuid('wallet_uuid');
            $table->foreignId('transaction_id')->nullable()->constrained();
            $table->string('type')->default('topup')->index();
            $table->double('price')->default(0);
            $table->string('note')->nullable();
            $table->enum('status', ['processed', 'paid', 'rejected', 'canceled'])->default('processed');
            $table->foreignId('created_by')->constrained('users');
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
        Schema::dropIfExists('wallet_histories');
    }
}
