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
        Schema::create('tb_distrito', function (Blueprint $table) {
            $table->id('dis_id');
            $table->string('dis_nombre', 255);
            $table->unsignedBigInteger('pro_id');
            $table->foreign('pro_id')
                ->references('pro_id')
                ->on('tb_provincia')
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
        Schema::dropIfExists('tb_distrito');
    }
};
