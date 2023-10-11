<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Pay extends Migration
{
      /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('pa_id');
            $table->string('pa_fecha_hora', 255)->nullable();
            $table->string('pa_monto', 255)->nullable();
            $table->text('pa_concepto', 255)->nullable();
            $table->text('pa_metodo_pago', 255)->nullable();
            $table->unsignedBigInteger('per_id')->nullable();
            $table->unsignedBigInteger('exp_id')->nullable();
            $table->unsignedBigInteger('abo_id')->nullable();

            $table->foreign('per_id')
                ->references('per_id')
                ->on('persons')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('exp_id')
                ->references('exp_id')
                ->on('proceedings')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('abo_id')
                ->references('abo_id')
                ->on('lawyers')
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
        Schema::dropIfExists('payments');
    }
}
