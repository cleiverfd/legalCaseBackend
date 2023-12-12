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
        Schema::create('procesal_naturals', function (Blueprint $table) {
            $table->id('proc_id');
            $table->string('nat_dni', 8);
            $table->string('nat_apellido_paterno', 55);
            $table->string('nat_apellido_materno', 55);
            $table->string('nat_nombres', 55);
            $table->string('nat_telefono', 55)->nullable();
            $table->string('nat_correo', 55)->unique()->nullable();
            $table->string('tipo_procesal', 55)->nullable();
            $table->string('condicion_procesal', 55)->nullable();
            $table->unsignedBigInteger('exp_id')->nullable();
            $table->unsignedBigInteger('dir_id')->nullable();
            $table->foreign('exp_id')
                ->references('exp_id')
                ->on('proceedings')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('dir_id')
                ->references('dir_id')
                ->on('addresses')
                ->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procesal_naturals');
    }
};
