<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id()->from(501);
            $table->uuid('uuid')->index();
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->double('tax', 22, 2)->default(0);
            $table->double('percentage', 22, 0)->default(0);
            $table->string('location')->nullable();
            $table->string('phone')->nullable();
            $table->tinyInteger('show_type')->nullable();
            $table->boolean('open')->default(1);
            $table->boolean('visibility')->default(1);
            $table->string('background_img', 191)->nullable();
            $table->string('logo_img', 191)->nullable();
            $table->double('min_amount', 12)->default(0.1);
            $table->enum('status', ['new', 'edited', 'approved', 'rejected', 'inactive'])->default('new');
            $table->text('status_note')->nullable();
            $table->string('mark')->nullable();
            $table->json('delivery_time');
            $table->tinyInteger('type');
            $table->double('price')->default(0);
            $table->double('price_per_km')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('shop_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('title', 191);
            $table->mediumText('description')->nullable();
            $table->string('address')->nullable();
            $table->softDeletes();

            $table->unique(['shop_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_translations');
        Schema::dropIfExists('shops');
    }
};
