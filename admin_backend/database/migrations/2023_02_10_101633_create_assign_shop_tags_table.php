<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignShopTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('assign_shop_tags', function (Blueprint $table) {
            $table->foreignId('shop_tag_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('assign_shop_tags');
    }
}
