<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopSectionTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('shop_section_translations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('locale');
            $table->foreignId('shop_section_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->softDeletes();
            $table->unique(['shop_section_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_section_translations');
    }
}
