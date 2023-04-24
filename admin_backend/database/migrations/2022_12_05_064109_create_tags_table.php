<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(true);

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tag_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tag_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('locale')->index();
            $table->string('title', 191);
            $table->mediumText('description')->nullable();
            $table->softDeletes();
            $table->unique(['tag_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tag_translations');
        Schema::dropIfExists('tags');
    }
}
