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
        Schema::create('histories', function (Blueprint $table) {
            $table->id('his_id');
            $table->string('his_fecha_hora', 255)->nullable();
            $table->string('his_medio_comuniacion', 255)->nullable();
            $table->text('his_detalle')->nullable();
            $table->unsignedBigInteger('procesal_natural_id')->nullable();
            $table->unsignedBigInteger('procesal_juridic_id')->nullable();
            $table->unsignedBigInteger('exp_id')->nullable();
            $table->foreign('procesal_natural_id')
                ->references('proc_id')
                ->on('procesal_naturals')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('procesal_juridic_id')
                ->references('jur_id')
                ->on('people_juridics')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('exp_id')
                ->references('exp_id')
                ->on('proceedings')
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
        Schema::dropIfExists('histories');
    }
};
