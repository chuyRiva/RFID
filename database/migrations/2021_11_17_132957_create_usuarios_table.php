<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->rememberToken();
	    $table->string('usuario')->unique();
	    $table->string('nombre');
 	    $table->string('email');
	    $table->string('tipo_usuario');
	    $table->boolean('activo');
   	    $table->integer('empresa_id')->unsigned();

	    $table->foreign('empresa_id')
	    ->references('id')
            ->on('empresas')
     	    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
}
