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
        Schema::table('labour_accepted_bookings', function (Blueprint $table) {
            $table->string('current_status')->default(0);
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('labour_accepted_bookings', function (Blueprint $table) {
            //
        });
    }
};
