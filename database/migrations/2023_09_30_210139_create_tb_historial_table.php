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
            $table->unsignedBigInteger('proc_id')->nullable();
            $table->unsignedBigInteger('exp_id')->nullable();
            $table->foreign('proc_id')
                ->references('proc_id')
                ->on('procesals')
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
