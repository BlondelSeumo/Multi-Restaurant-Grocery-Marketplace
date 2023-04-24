<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtColumnForFaqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (!Schema::hasColumn('faqs', 'deleted_at')) {
            Schema::table('faqs', function (Blueprint $table) {
                $table->softDeletes();
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
        if (Schema::hasColumn('faqs', 'deleted_at')) {
            Schema::table('faqs', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
    }
}
