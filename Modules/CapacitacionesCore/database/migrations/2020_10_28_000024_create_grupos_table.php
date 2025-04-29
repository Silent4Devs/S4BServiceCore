<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGruposTable extends Migration
{
    public function up()
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('grupo')->nullable();
            $table->longText('descripcion')->after('grupo')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }
}
