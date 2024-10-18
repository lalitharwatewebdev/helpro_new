<?php

use App\Http\Controllers\Api\v3\CategoryController;

Route::prefix("v3")->group(function(){
    Route::get("/",function(){
        return "dfsdf";
    });
    Route::prefix("category")->controller(CategoryController::class)->group(function(){
        Route::get("/","get");
        Route::get("get-labour-count-as-per-area","getLabourCountAsPerCategory");
    });
});