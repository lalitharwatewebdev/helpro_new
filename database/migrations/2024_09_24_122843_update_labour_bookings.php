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
            $table->timestamp("start_date")->nullable();
            $table->timestamp("end_date")->nullable();
            $table->timestamp("start_time")->nullable();
            $table->timestamp("end_time")->nullable();
            $table->longText("note")->nullable();
            $table->string("alternate_number")->nullable();
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
            $table->dropColumn(['start_time',"end_time","start_date",'end_date']);
        });
    }
};
