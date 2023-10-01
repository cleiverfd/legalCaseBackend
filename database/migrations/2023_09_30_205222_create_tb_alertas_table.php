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
        Schema::create('tb_alertas', function (Blueprint $table) {
            $table->id('ale_id');
            $table->string('ale_fecha_vencimiento', 255)->nullable();
            $table->text('ale_descripcion')->nullable();
            $table->unsignedBigInteger('cal_id');
            $table->foreign('cal_id')
                ->references('cal_id')
                ->on('tb_calendario')
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
        Schema::dropIfExists('tb_alertas');
    }
};
