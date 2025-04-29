<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizacionsTable extends Migration
{
    public function up()
    {
        Schema::create('organizacions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('empresa');
            $table->longText('direccion');
            $table->string('telefono')->nullable();
            $table->string('correo')->nullable();
            $table->string('pagina_web')->nullable();
            $table->string('logotipo')->nullable();
            $table->string('razon_social')->nullable();
            $table->string('giro')->nullable();
            $table->string('rfc')->nullable();
            $table->string('representante_legal')->nullable();
            $table->integer('num_empleados')->nullable();
            $table->string('tamano')->nullable();
            $table->string('linkedln')->nullable();
            $table->string('youtube')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->tinyInteger('certificado')->default(1);

            $table->string('external_id_organizacion');

            $table->timestamps();
            $table->softDeletes();
        });
    }
}
