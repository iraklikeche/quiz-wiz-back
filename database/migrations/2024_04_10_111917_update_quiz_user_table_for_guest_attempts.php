<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('quiz_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();

            $table->integer('time_spent')->nullable();
            $table->integer('score')->nullable();
            $table->boolean('is_guest')->default(false);
        });
    }

    public function down()
    {
        Schema::table('quiz_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();

            $table->dropColumn(['time_spent', 'score', 'is_guest']);
        });
    }

};
