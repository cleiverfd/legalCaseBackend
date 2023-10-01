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
        Schema::create('tb_calendario', function (Blueprint $table) {
            $table->id('cal_id');
            $table->string('cal_tipo_evento', 255)->nullable();
            $table->string('cal_fecha_evento', 255)->nullable();
            $table->text('cal_descripcion')->nullable();
            $table->unsignedBigInteger('exp_id');
            $table->unsignedBigInteger('abo_id');

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
        Schema::dropIfExists('tb_calendario');
    }
};
