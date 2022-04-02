<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuscriptionPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suscription_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('suscription_id');
            $table->decimal('amount', 10, 2 );
            $table->boolean('response');
            $table->timestamps();

            $table->foreign('suscription_id')->references('id')->on('suscriptions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suscription_payments');
    }
}
