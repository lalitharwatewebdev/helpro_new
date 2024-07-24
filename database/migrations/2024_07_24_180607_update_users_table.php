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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId("state")->nullable()->constrained()->nullOnDelete();
            $table->foreignId("city")->nullable()->constrained()->nullOnDelete();
            $table->string("profile_pic")->nullable();
            $table->string("aadhaar_number")->nullable();
            $table->string("aadhaar_card_front")->nullable();
            $table->string("aadhaar_card_back")->nullable();
            $table->string("pan_card_number")->nullable();
            $table->string("bank_name")->nullable();
            $table->string("IFSC_code")->nullable();
            $table->string("branch_address")->nullable();
            $table->decimal("rate_per_day",8,2);
            $table->enum("labour_status",['pending',"accepted","rejected"])->default("pending");
            $table->enum('type',["user","labour"]);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
