<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BlogController;
use App\Http\Controllers\Api\V1\NewAndEventController;
use App\Http\Controllers\Api\V1\RecruitmentController;
use App\Http\Controllers\Api\V1\VisitorController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

Route::namespace('Api\V1')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::post('io-register', [AuthController::class, 'register']);
        Route::post('io-login', [AuthController::class, 'login']);

        //Blogs
        Route::get('blogs', [BlogController::class, 'index']);
        Route::get('blogs/{blog}', [BlogController::class, 'show']);


        //Recruitments
        Route::get('recruitments', [RecruitmentController::class, 'index']);
        Route::get('recruitments/{recruitment}', [RecruitmentController::class, 'show']);

        //News And Event
        Route::get('newsandevents', [NewAndEventController::class, 'index']);
        Route::get('newsandevents/{newsandevents}', [NewAndEventController::class, 'show']);

        //Visitors
        Route::get('visitor-counts', [VisitorController::class, 'visitorCount']);
        Route::get('visitor-increment', [VisitorController::class, 'visitorIncrement']);


        Route::middleware(['auth:api'])->group(function () {
            //User
            Route::get('user', [AuthController::class, 'user']);
            Route::put('user/update-information', [AuthController::class, 'updateUser']);
            Route::put('user/change-password', [AuthController::class, 'changePassword']);
            Route::post('user/logout', [AuthController::class, 'logout']);

            //Blogs
            Route::post('blogs', [BlogController::class, 'store']);
            Route::put('blogs/{blog}', [BlogController::class, 'update']);
            Route::delete('blogs/{blog}', [BlogController::class, 'destroy']);

            //Recruitments
            Route::post('recruitments', [RecruitmentController::class, 'store']);
            Route::put('recruitments/{recruitment}', [RecruitmentController::class, 'update']);
            Route::delete('recruitments/{recruitment}', [RecruitmentController::class, 'destroy']);

            //News and Events
            Route::post('newsandevents', [NewAndEventController::class, 'store']);
            Route::put('newsandevents/{newsandevents}', [NewAndEventController::class, 'update']);
            Route::delete('newsandevents/{newsandevents}', [NewAndEventController::class, 'destroy']);
        });


        if (App::environment('local')) {
            Route::get('routes', function () {
                $routes = [];

                foreach (Route::getRoutes()->getIterator() as $route) {
                    if (strpos($route->uri, 'api') !== false) {
                        $routes[] = $route->uri;
                    }
                }

                return response()->json($routes);
            });
        }
    });
});
