<?php

use App\Models\Escuela\Course;
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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('subtitle');
            $table->text('description')->nullable();
            $table->enum('status', [Course::BORRADOR, Course::REVISION, Course::PUBLICADO])->default(Course::BORRADOR);
            $table->string('slug');
            $table->string('order_section')->nullable();
            $table->tinyInteger('certificado')->default(0);
            $table->longText('firma_instructor')->nullable();
            $table->boolean('firma_habilitar')->default(true);

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->unsignedBigInteger('level_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('price_id')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('level_id')->references('id')->on('levels')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('price_id')->references('id')->on('prices')->onDelete('set null');

            $table->boolean('publico')->default(false);
            $table->string('external_id_organizacion');

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
        Schema::dropIfExists('courses');
    }
};
