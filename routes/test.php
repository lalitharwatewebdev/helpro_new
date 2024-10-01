<?php

use App\Http\Controllers\API\v1\TestController;
use Illuminate\Support\Facades\Route;

Route::controller(TestController::class)->prefix("test-api")->group(function(){
    Route::post("/","index");
});