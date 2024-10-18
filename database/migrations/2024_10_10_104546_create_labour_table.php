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
        Schema::create('labours', function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable();
            $table->string("phone")->nullable();
            $table->string("email")->nullable();
            $table->string("device_id")->nullable();
            $table->foreignId("state_id")->nullable()->constrained()->nullOnDelete();
            $table->foreignId("city_id")->nullable()->constrained()->nullOnDelete();
            $table->string("profile_pic")->nullable();
            $table->string("aadhaar_number")->nullable();
            $table->string("aadhaar_card_front")->nullable();
            $table->string("aadhaar_card_back")->nullable();
            $table->string("pan_card_number")->nullable();
            $table->string("pan_front")->nullable();
            $table->string("bank_name")->nullable();
            $table->string("account_number")->nullable();
            $table->string("IFSC_code")->nullable();
            $table->string("branch_address")->nullable();
            $table->enum("labour_status",['pending','accepted','rejected'])->default("pending");
            $table->string("address")->nullable();
            $table->enum("gender",['male','female'])->nullable();
            $table->string("lat_long")->nullable();
            $table->string("qualification")->nullable();
            $table->string("availability")->nullable();
            $table->string("preferred_shift")->nullable();
            $table->enum("is_online",['yes','no'])->default("yes");
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
        Schema::dropIfExists('labours');
    }
};
