<?php

use App\Http\Controllers\Admin\LabourBusinessSettings;
use App\Http\Controllers\API\v1\AddressController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\v1\Auth\AuthController;
use App\Http\Controllers\API\v1\BannerController;
use App\Http\Controllers\API\v1\BookingController;
use App\Http\Controllers\API\v1\BusinessSettingsController;
use App\Http\Controllers\API\v1\CartController;
use App\Http\Controllers\API\v1\CategoryController;
use App\Http\Controllers\API\v1\CheckoutController;
use App\Http\Controllers\API\v1\LabourController;
use App\Http\Controllers\API\v1\UserController;
use App\Http\Controllers\API\v1\PromoCodeController;
use App\Http\Controllers\API\v1\ReferralController;
use App\Http\Controllers\API\v1\WalletController;



use App\Http\Controllers\API\v1\Labour\Auth\AuthController as LabourAuthController;
use App\Http\Controllers\API\v1\Labour\RejectBookingController;
use App\Http\Controllers\API\v1\Labour\SliderController;
use App\Http\Controllers\API\v1\Labour\WalletController as LabourWalletController;
use App\Http\Controllers\API\v1\Labour\LabourController as LabourUserController;
use App\Http\Controllers\API\v1\LabourRedeemController;

Route::prefix('v1')->group(function () {



    Route::controller(AuthController::class)->prefix("user")->group(function () {
        // Route::post("login", "OtpLogin");
        Route::post("otp-login","OTPLogin");
        Route::post("get-otp","generateOTP");
        Route::post("google-login","googleLogin");
    });



    Route::controller(UserController::class)->prefix("user")->group(function () {
        Route::get("get-city", "getCity");
        Route::get("get-state", "getState");
    });

    Route::controller(BusinessSettingsController::class)->prefix("business-settings")->group(function () {
        Route::get("/", "get");
        Route::get("labour", "labourGet");
    });

    // Route::controller(LabourBusinessSettings::class)->prefix("labour-business")

    Route::controller(CategoryController::class)->prefix("category")->group(function () {
        Route::get("/", "get");
    });

    Route::controller(UserController::class)->prefix("user")->group(function () {
        Route::post("sign-up", "store");
        Route::get("/", "profile");
        Route::post("logout", "logOut");
    });


    Route::controller(BannerController::class)->prefix("banner")->group(function () {
            Route::get("/", 'get');
        });


        Route::controller(UserController::class)->prefix("user")->group(function () {
            Route::get("/", "profile");
        });

    Route::group(['middleware' => "auth:sanctum"], function () {
        Route::controller(UserController::class)->prefix("user")->group(function () {
            Route::post("sign-up", "store");
            // Route::get("/", "profile");
            Route::post("logout", "logOut");
        });

        Route::controller(PromoCodeController::class)->prefix("promo-code")->group(function () {
            Route::get("/", "get");
        });

        Route::controller(LabourController::class)->prefix("labours")->group(function () {
            Route::get("/", "get");
        });

        Route::controller(CartController::class)->prefix("carts")->group(function () {
            Route::get("/", "get");
            Route::post("store", "add");
            Route::post("delete", "delete");
        });

        Route::controller(CategoryController::class)->prefix("category")->group(function () {
            Route::post('get-area', "getArea");
        });

        Route::controller(CheckoutController::class)->prefix("checkouts")->group(function () {
            Route::post("store", "store");
            Route::post("fetch-order", "fetchOrder");
            Route::get("get-booking", "bookingData");
        });

        Route::controller(AddressController::class)->prefix("addresses")->group(function () {
            Route::post("store", "store");
            Route::post("set-address-primary", "setAddressPrimary");
            Route::get("edit", "edit");
            Route::post("delete", "delete");
            Route::get("/", "get");
            Route::post("update", "update");
        });

        Route::controller(BookingController::class)->prefix("bookings")->group(function () {
            Route::get("/", "get");
        });


        Route::prefix("labours")->group(function () {
            Route::controller(BannerController::class)->prefix("banners")->group(function () {
                Route::get("/", "getLabourSlider");
            });
        });

        Route::controller(ReferralController::class)->prefix("referrals")->group(function () {
            Route::post("add-referral", "addReferral");
        });

        Route::controller(WalletController::class)->prefix("wallets")->group(function(){
            Route::post("add-amount","createAmount");
            Route::post("fetch-amount","fetchAmount");
            Route::get("wallet-transactions","walletTransaction");
        });
    });



    // labour authController
    Route::prefix("labour")->group(function () {
        Route::controller(LabourAuthController::class)->group(function () {
            // Route::post("login", "OtpLogin");
        Route::post("get-otp", "generateOTP");
        Route::post("otp-login", 'OTPLogin');
        Route::post("google-login","googleLogin");
        });



        Route::group(['middleware' => "auth:sanctum"], function () {
            Route::controller(LabourAuthController::class)->group(function () {
                Route::post("sign-up", "signUp");
                Route::post("logout", "logOut");
            });

            // Route::controller(SliderController::class)->grou

            Route::controller(RejectBookingController::class)->group(function () {
                Route::post("reject-booking", "rejectBooking");
            });

            Route::controller(LabourUserController::class)->group(function () {
                Route::get("profile", "profile");
                Route::post("online-status", "activeStatus");
                Route::get("/", "get");
                Route::get("history", "history");
                Route::get("accepted-booking", "acceptedBooking");
                Route::get('rejected-booking', "rejectedBooking");
                Route::post("accept-user-booking", "AcceptedUserBooking");
                Route::post("reject-user-booking", "rejectUserBooking");
                Route::post("accept-reject-booking", 'acceptRejectBooking');
            });

            Route::controller(LabourWalletController::class)->prefix("wallets")->group(function(){
                Route::post("redeem-wallet-amount","redeemAmount");
                Route::get("transactions","transactions");
            });

            Route::controller(LabourRedeemController::class)->prefix("redeem")->group(function(){
                Route::post("redeem-amount","redeemAmount");
                Route::get("redeem-history","getHistory");
            });
        });
    });
});
