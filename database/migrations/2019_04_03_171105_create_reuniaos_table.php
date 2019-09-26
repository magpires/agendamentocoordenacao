<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReuniaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reuniaos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('titulo');
            $table->integer('id_coordenador')->unsigned();
            $table->foreign('id_coordenador')->references('id')->on('users')->onDelete('cascade');
            $table->integer('id_solicitante')->unsigned();
            $table->foreign('id_solicitante')->references('id')->on('users')->onDelete('cascade');
            $table->integer('id_secretario')->unsigned();
            $table->foreign('id_secretario')->references('id')->on('users')->onDelete('cascade');
            $table->enum('status', ['Disponível', 'Em espera', 'Marcada', 'Concluída']);
            $table->string('local');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reuniaos');
    }
}
