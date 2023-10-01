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
        Schema::create('tb_direccion', function (Blueprint $table) {
            $table->id('dir_id');
            $table->string('dir_calle_av', 255)->nullable();
            $table->unsignedBigInteger('dis_id')->nullable();
            $table->unsignedBigInteger('pro_id')->nullable();
            $table->unsignedBigInteger('dep_id')->nullable();
            $table->unsignedBigInteger('per_id')->nullable();

            $table->foreign('dis_id')
                ->references('dis_id')
                ->on('tb_distrito')
                ->onDelete('cascade');
           
            $table->foreign('pro_id')
                ->references('pro_id')
                ->on('tb_provincia')
                ->onDelete('cascade');
           
            $table->foreign('dep_id')
                ->references('dep_id')
                ->on('tb_departamento')
                ->onDelete('cascade');
           
            $table->foreign('per_id')
                ->references('per_id')
                ->on('tb_personas')
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
        Schema::dropIfExists('tb_direccion');
    }
};
