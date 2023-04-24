<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();
            $table->foreignId('created_by')->constrained('users');
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('order_id')->nullable();
            $table->bigInteger('parent_id')->default(0);
            $table->string('type')->default('question');
            $table->string('subject', 191);
            $table->text('content');
            $table->enum('status', ['open', 'answered', 'progress', 'closed', 'rejected'])->default('open');
            $table->boolean('read')->default(false);
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
        Schema::dropIfExists('tickets');
    }
}
