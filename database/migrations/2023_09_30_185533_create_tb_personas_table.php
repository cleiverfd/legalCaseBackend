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
        Schema::create('persons', function (Blueprint $table) {
            $table->id('per_id');
            $table->unsignedBigInteger('nat_id')->nullable();
            $table->unsignedBigInteger('jur_id')->nullable();
            $table->foreign('nat_id')
                ->references('nat_id')
                ->on('people_naturals')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('jur_id')
                ->references('jur_id')
                ->on('people_juridics')
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
        Schema::dropIfExists('persons');
    }
};
