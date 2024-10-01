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
        Schema::create('accepted_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId("labour_id")->nullable()->contrained("users")->nullOnDelete();
            $table->foreignId("booking_id")->nullable()->constrained()->nullOnDelete();
            $table->decimal("amount")->nullable();
            $table->integer("otp")->nullable();
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
        Schema::dropIfExists('accepted_bookings');
    }
};
