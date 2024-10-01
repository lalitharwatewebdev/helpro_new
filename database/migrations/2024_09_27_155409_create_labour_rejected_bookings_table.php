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
        Schema::create('labour_rejected_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId("labour_id")->constrained("users")->cascadeOnDelete();
            $table->foreignId("booking_id")->constrained("labour_bookings")->cascadeOnDelete();
            $table->timestamps();
            $table->enum('status', ['active', 'blocked'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('labour_rejected_bookings');
    }
};
