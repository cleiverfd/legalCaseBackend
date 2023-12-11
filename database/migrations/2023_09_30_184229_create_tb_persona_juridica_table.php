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
        Schema::create('people_juridics', function (Blueprint $table) {
            $table->id('jur_id');
            $table->string('jur_ruc', 255);
            $table->string('jur_razon_social', 255);
            $table->string('jur_telefono', 255)->nullable();
            $table->string('jur_correo', 255)->nullable();
            $table->string('jur_rep_legal', 255)->nullable()->comment('representante legal');
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
        Schema::dropIfExists('people_juridics');
    }
};
