<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBackupHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('backup_histories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->boolean('status')->default(1);
            $table->string('path', 191)->nullable();

            $table->foreignId('created_by')
                ->constrained('users');
            $table->softDeletes();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_histories');
    }
}
