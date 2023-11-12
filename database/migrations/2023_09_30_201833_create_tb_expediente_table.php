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
        Schema::create('proceedings', function (Blueprint $table) {
            $table->id('exp_id');
            $table->string('exp_numero', 255);
            $table->string('exp_fecha_inicio', 255)->nullable();
            $table->string('exp_pretencion', 255)->nullable();
            $table->string('exp_materia', 255)->nullable();
            $table->unsignedBigInteger('exp_especialidad')->nullable()->comment('parte_procesal_demandante');
            $table->decimal('exp_monto_pretencion')->nullable();
            $table->string('exp_estado_proceso', 255)->nullable();
            $table->unsignedBigInteger('exp_demandante')->nullable()->comment('parte_procesal_demandante');
            $table->unsignedBigInteger('exp_demandado')->nullable()->comment('parte_procesal_demandado');
            $table->string('exp_juzgado')->nullable()->comment('juzgado_donde_se_lleva_el_proceso');
            $table->unsignedBigInteger('abo_id')->nullable();
            $table->foreign('exp_especialidad')
                ->references('esp_id')
                ->on('specialties')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('exp_demandante')
                ->references('per_id')
                ->on('persons')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('exp_demandado')
                ->references('per_id')
                ->on('persons')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('abo_id')
                ->references('abo_id')
                ->on('lawyers')
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
        Schema::dropIfExists('proceedings');
    }
};
