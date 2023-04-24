<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnDescriptionFromTermConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasColumn('term_conditions', 'description')) {
            Schema::table('term_conditions', function (Blueprint $table) {
                $table->dropColumn('description');
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
        Schema::table('term_conditions', function (Blueprint $table) {
            //
        });
    }
}
