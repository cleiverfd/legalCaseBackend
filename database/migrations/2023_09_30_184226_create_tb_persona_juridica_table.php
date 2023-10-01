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
        Schema::create('tb_persona_juridica', function (Blueprint $table) {
            $table->id('jur_id');
            $table->string('jur_ruc', 255);
            $table->string('jur_razon_social', 255);
            $table->string('jur_telefono', 255)->nullable();
            $table->string('jur_correo', 255)->nullable();
            $table->string('jur_rep_legal', 255)->nullable()->comment('representante legal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_persona_juridica');
    }
};
