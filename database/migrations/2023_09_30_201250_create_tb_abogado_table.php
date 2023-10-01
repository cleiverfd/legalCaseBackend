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
        Schema::create('lawyers', function (Blueprint $table) {
            $table->id('abo_id');
            $table->string('abo_carga_laboral', 255)->nullable()->comment('cantidad de casos');
            $table->string('abo_disponibilidad', 255)->nullable()->comment('ocupado o libre');
            $table->unsignedBigInteger('nat_id')->nullable();
            $table->foreign('nat_id')
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
        Schema::dropIfExists('lawyers');
    }
};
