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
    public function up()
    {
        Schema::create('user_evaluations', function (Blueprint $table) {
            $table->id();
            $table->boolean('completed')->default(false);
            $table->float('score')->default(0.0);
            $table->tinyInteger('quiz_size')->default(0);

            $table->integer('number_of_attempts')->default(3);
            $table->timestamp('last_attempt')->nullable();
            $table->boolean('approved')->default(false);

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluation_id')->constrained()->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_evaluations');
    }
};
