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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id('emp_id');
            $table->string('emp_profesion_ocupacion', 255)->nullable();
            $table->string('emp_centro_trabajo', 255)->nullable();
            $table->unsignedBigInteger('proc_id')->nullable();
            $table->foreign('proc_id')
                ->references('proc_id')
                ->on('procesal_naturals')
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
        Schema::dropIfExists('jobs');
    }
};
