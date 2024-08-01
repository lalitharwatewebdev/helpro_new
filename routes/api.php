<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\Auth\AuthController;
use App\Http\Controllers\API\v1\BannerController;
use App\Http\Controllers\API\v1\BusinessSettingsController;
use App\Http\Controllers\API\v1\CategoryController;
use App\Http\Controllers\API\v1\UserController;

Route::prefix('v1')->group(function () {

    Route::get('user', function(){
        return "testing here and now";
    });

    Route::controller(AuthController::class)->prefix("user")->group(function(){
        Route::post("login","OtpLogin");
    });

    Route::controller(UserController::class)->prefix("user")->group(function(){
        Route::get("get-city","getCity");
        Route::get("get-state","getState");
        
    });


    Route::group(['middleware' => "auth:sanctum"],function(){
        Route::controller(UserController::class)->prefix("user")->group(function(){
            Route::post("sign-up","store");
            Route::get("/","profile");
            Route::post("logout","logOut");
        });

        Route::controller(BannerController::class)->prefix("banner")->group(function(){
            Route::get("/",'get');
        });

        Route::controller(CategoryController::class)->prefix("category")->group(function(){
            Route::get("/","get");
        });

        Route::controller(BusinessSettingsController::class)->prefix("business-settings")->group(function(){
            Route::get("/","get");
        });

        // Route::controller(BusinessSettingsController::class)->prefix("")
    });

    // Route::controller(AuthController::class)->prefix('user')->group(function () {
    //     Route::post('register', 'register');
    //     Route::post('login', 'loginOne');
    // });
    // Route::group(['middleware' => 'auth:sanctum'], function () {
    //     Route::controller(PostController::class)->prefix('post')->group(function(){
    //         Route::post('store', 'store')->name('store');
    //     });

    //     Route::controller(FavouriteController::class)->prefix('favourite')->group(function(){
    //         Route::post('store' , 'store');
    //     });
    // });
});
