<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannerTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('banner_id')
                ->constrained('banners')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('locale')->index();
            $table->string('title', 191);
            $table->text('description')->nullable();
            $table->string('button_text')->nullable();
            $table->softDeletes();
            $table->unique(['banner_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banner_translations');
    }
}
