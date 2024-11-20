<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('labour_bookings', function (Blueprint $table) {
            $table->string('labour_amount')->nullable();
            $table->string('commission_amount')->nullable();
            $table->string('total_labour_charges')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('labour_bookings', function (Blueprint $table) {
            //
        });
    }
};
