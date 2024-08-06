<?php

use App\Http\Controllers\API\v1\AddressController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v1\Auth\AuthController;
use App\Http\Controllers\API\v1\BannerController;
use App\Http\Controllers\API\v1\BusinessSettingsController;
use App\Http\Controllers\API\v1\CartController;
use App\Http\Controllers\API\v1\CategoryController;
use App\Http\Controllers\API\v1\CheckoutController;
use App\Http\Controllers\API\v1\LabourController;
use App\Http\Controllers\API\v1\UserController;
use App\Models\Cart;
use App\Models\Checkout;

Route::prefix('v1')->group(function () {

   

    Route::controller(AuthController::class)->prefix("user")->group(function(){
        Route::post("login","OtpLogin");
    });

    Route::controller(UserController::class)->prefix("user")->group(function(){
        Route::get("get-city","getCity");
        Route::get("get-state","getState");
        
    });

    Route::controller(BusinessSettingsController::class)->prefix("business-settings")->group(function(){
        Route::get("/","get");
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

        Route::controller(LabourController::class)->prefix("labours")->group(function(){
            Route::get("/","get");
        });

        Route::controller(CartController::class)->prefix("carts")->group(function(){
            Route::get("/","get");
            Route::post("store","add");
            Route::post("delete","delete");
        });

        Route::controller(CheckoutController::class)->prefix("checkouts")->group(function(){
            Route::post("store","store");
        });

        Route::controller(AddressController::class)->prefix("addresses")->group(function(){
            Route::post("store","store");
            Route::post("set-address-primary","setAddressPrimary");
            Route::get("edit","edit");
            Route::post("delete","delete");
            Route::get("/","get");
        });


        Route::prefix("labours")->group(function(){
            Route::controller(BannerController::class)->prefix("banners")->group(function(){
                Route::get("/","getLabourSlider");
            });
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
