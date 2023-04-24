<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnShopsFromBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasColumn('banners', 'shops')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->dropColumn('shops');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            //
        });
    }
}
