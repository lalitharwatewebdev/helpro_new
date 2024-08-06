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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->nullable()->constrained()->cascadeOnDelete();
            $table->longText("address");
            $table->foreignId("state_id")->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId("city_id")->nullable()->constrained()->cascadeOnDelete();
            $table->string("pincode");
            $table->enum("is_primary",["yes",'no']);
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
        Schema::dropIfExists('addresses');
    }
};
