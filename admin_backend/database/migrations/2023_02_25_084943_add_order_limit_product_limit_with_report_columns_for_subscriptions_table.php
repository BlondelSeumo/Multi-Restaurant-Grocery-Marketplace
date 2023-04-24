<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddOrderLimitProductLimitWithReportColumnsForSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('title');
            $table->integer('product_limit');
            $table->integer('order_limit');
            $table->boolean('with_report');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('product_limit');
            $table->dropColumn('order_limit');
            $table->dropColumn('with_report');
        });
    }
}
