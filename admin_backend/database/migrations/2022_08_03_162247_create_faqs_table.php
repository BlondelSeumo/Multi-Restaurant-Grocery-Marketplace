<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->index();
            $table->string('type')->nullable();
            $table->boolean('active')->default(1);
            $table->timestamps();
        });

        Schema::create('faq_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('faq_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('locale')->index();
            $table->text('question');
            $table->text('answer')->nullable();
            $table->softDeletes();

            $table->unique(['faq_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('faq_translations');
        Schema::dropIfExists('faqs');
    }
}
