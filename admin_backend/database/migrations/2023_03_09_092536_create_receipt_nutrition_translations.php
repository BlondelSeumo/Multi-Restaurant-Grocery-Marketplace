<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptNutritionTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('receipt_nutrition_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('nutrition_id')
                ->constrained('receipt_nutritions')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('locale')->index();
            $table->string('title', 191);
            $table->softDeletes();

            $table->unique(['nutrition_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_nutrition_translations');
    }
}
