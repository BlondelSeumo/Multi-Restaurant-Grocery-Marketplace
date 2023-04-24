<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('translations', function(Blueprint $table) {

            $table->collation = 'utf8mb4_bin';

            $table->id();
            $table->integer('status')->default(1);
            $table->string('locale');
            $table->string('group');
            $table->string('key');
            $table->text('value')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['group', 'key', 'locale']);

        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::dropIfExists('translations');
	}

}
