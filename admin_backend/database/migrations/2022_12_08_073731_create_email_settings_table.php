<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('smtp_auth')->default(true);
            $table->boolean('smtp_debug')->default(false);
            $table->string('host', 92);
            $table->integer('port')->default(465);
            $table->string('password')->nullable();
            $table->string('from_to')->nullable();
            $table->string('from_site')->nullable();
            $table->json('ssl')->nullable();
            $table->boolean('active')->default(false);
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
        Schema::dropIfExists('email_settings');
    }
}
