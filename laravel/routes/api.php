<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SocialLoginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
	return $request->user();
});

Route::get('test', function () {
	$test = Storage::disk('images')->url('product/product_1.jpg');
	return response()->json(['message' => $test], 200);
})->middleware('apiResponseLog');

Route::prefix('member')->controller(UserController::class)->group(function () {
	// 註冊
	Route::post('/register', 'register');
	// 登入
	Route::post('/login', 'login');
	//處理三方登入
	Route::post('/handleSocialLogin', 'handleSocialLogin');
	// 會員資訊
	Route::get('/info', 'info')->middleware('auth:api');
});

Route::prefix('socialLogin')->controller(SocialLoginController::class)->group(function () {
	// 取得社群登入網址
	Route::get('/getUrl', 'getSocialLoginUrl');
	// 社群登入 callback
	Route::get('/{gateway}/callback', 'callback');
});

