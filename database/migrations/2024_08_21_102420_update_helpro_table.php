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
        Schema::table('rejected_bookings', function (Blueprint $table) {
            $table->foreignId("booking_id")->nullable()->constrained()->nullOnDelete(); 
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rejected_bookings', function (Blueprint $table) {
            //
            $table->dropForeign("checkout_id");
        });
    }
};
