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
        Schema::create('labour_redeems', function (Blueprint $table) {
            $table->id();
            $table->foreignId("labour_id")->nullable()->constrained("users")->nullOnDelete();
            $table->string("amount")->nullable();
            $table->enum('payment_status',['accepted','rejected','pending'])->default("pending");
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
        Schema::dropIfExists('labour_redeems');
    }
};
