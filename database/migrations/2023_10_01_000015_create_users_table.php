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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();

            $table->string('usu_rol', 255)->comment('admin, abogado, cliente, asistente, secretaria');
            $table->unsignedBigInteger('per_id')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();

            // $table->foreign('name')
            //     ->references('nat_nombres')
            //     ->on('tb_persona_natural')
            //     ->onDelete('cascade')
            //     ->onUpdate('cascade');
            $table->foreign('email')
                ->references('nat_correo')
                ->on('people_naturals')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('per_id')
                ->references('nat_id')
                ->on('people_naturals')
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
        Schema::dropIfExists('users');
    }
};
