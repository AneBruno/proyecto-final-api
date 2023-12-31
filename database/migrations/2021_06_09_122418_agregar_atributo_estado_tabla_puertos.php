<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AgregarAtributoEstadoTablaPuertos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('puertos', function (Blueprint $table) {
            $table->enum('estado', ['HABILITADO', 'DESHABILITADO'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('puertos', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
}
