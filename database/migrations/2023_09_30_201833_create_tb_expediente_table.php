<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_expediente', function (Blueprint $table) {
            $table->id('exp_id');
            $table->string('exp_numero', 255);
            $table->string('exp_fecha_inicio', 255)->nullable();
            $table->string('exp_pretencion', 255)->nullable();
            $table->string('exp_materia', 255)->nullable();
            $table->string('exp_juzgado', 255)->nullable();
            $table->float('exp_monto_pretencion')->nullable();
            $table->float('exp_monto_ejecucion')->nullable();
            $table->string('exp_estado_proceso', 255)->nullable();
            $table->unsignedBigInteger('exp_demandante')->nullable()->comment('parte_procesal_demandante');
            $table->unsignedBigInteger('exp_demandado')->nullable()->comment('parte_procesal_demandado');
            $table->unsignedBigInteger('abo_id')->nullable();

            $table->foreign('exp_demandante')
                ->references('per_id')
                ->on('tb_personas')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('exp_demandado')
                ->references('per_id')
                ->on('tb_personas')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('abo_id')
                ->references('abo_id')
                ->on('tb_abogado')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_expediente');
    }
};
