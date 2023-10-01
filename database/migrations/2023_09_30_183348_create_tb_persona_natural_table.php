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
        Schema::create('tb_persona_natural', function (Blueprint $table) {
            $table->id('nat_id');
            $table->string('nat_dni', 8);
            $table->string('nat_apellido_paterno', 55);
            $table->string('nat_apellido_materno', 55);
            $table->string('nat_nombres', 55);
            $table->string('nat_telefono', 55)->nullable();
            $table->string('nat_correo', 55)->unique()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_persona_natural');
    }
};
