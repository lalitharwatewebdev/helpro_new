<?php

use App\Http\Controllers\API\v1\SiteController;
use Illuminate\Support\Facades\Route;

Route::controller(SiteController::class)->prefix("site")->group(function(){
    Route::get("privacy-policy","privacyPolicy");
});