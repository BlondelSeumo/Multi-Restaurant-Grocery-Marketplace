<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('type')->comment('click,read,listen and etc')->nullable();
            $table->string('value')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->string('device')->nullable();
            $table->jsonb('agent')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
}
