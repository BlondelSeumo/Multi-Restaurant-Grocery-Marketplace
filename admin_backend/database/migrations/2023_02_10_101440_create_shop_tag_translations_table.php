<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopTagTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('shop_tag_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_tag_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title');
            $table->string('locale')->index();
            $table->softDeletes();
            $table->unique(['shop_tag_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_tag_translations');
    }
}
