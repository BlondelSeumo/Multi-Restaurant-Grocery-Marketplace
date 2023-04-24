<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('sms_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('title', 191);
            $table->string('from')->nullable();
            $table->string('type');
            $table->string('api_key')->nullable();
            $table->string('secret_key')->nullable();
            $table->string('service_id')->nullable();
            $table->string('text', 191)->nullable();
            $table->boolean('active')->default(0);
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
        Schema::dropIfExists('sms_gateways');
    }
}
