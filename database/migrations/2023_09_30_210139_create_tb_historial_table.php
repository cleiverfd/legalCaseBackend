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
        Schema::create('tb_historial', function (Blueprint $table) {
            $table->id('his_id');
            $table->string('his_fecha_hora', 255)->nullable();
            $table->string('his_medio_comuniacion', 255)->nullable();
            $table->text('his_detalle')->nullable();
            $table->unsignedBigInteger('per_id')->nullable();
            $table->unsignedBigInteger('exp_id')->nullable();
            $table->unsignedBigInteger('abo_id')->nullable();

            $table->foreign('per_id')
                ->references('per_id')
                ->on('tb_personas')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('exp_id')
                ->references('exp_id')
                ->on('tb_expediente')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('abo_id')
                ->references('abo_id')
                ->on('tb_abogado')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_historial');
    }
};
