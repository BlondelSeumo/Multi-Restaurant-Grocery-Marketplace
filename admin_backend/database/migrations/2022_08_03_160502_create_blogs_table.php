<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();
            $table->foreignId('user_id')->constrained();
            $table->tinyInteger('type')->index()->default(1);
            $table->date('published_at')->nullable();
            $table->boolean('active')->default(1);
            $table->string('img', 191)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blog_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('blog_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('locale')->index();
            $table->string('title', 191);
            $table->string('short_desc', 191)->nullable();
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->unique(['blog_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_translations');
        Schema::dropIfExists('blogs');
    }
}
