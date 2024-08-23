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
        Schema::create('razor_pay_redeems', function (Blueprint $table) {
            $table->id();
            $table->foreignId("labour_id")->nullable()->constrained("users")->nullOnDelete();
            $table->string("payment_id");
            $table->string("amount")->nullable();
            $table->longText("notes")->nullable();
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
        Schema::dropIfExists('razor_pay_redeems');
    }
};
