<?php

use App\Http\Controllers\Paypal\PaypalController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('checkout');
});

// Route::get('checkout', function () {
//     return view('checkout');
// });

Route::post('/test-post', function () {
    return 'POST route working';
});

// Route::post('/paypal/create-order', [PaypalController::class,'createOrder'])->name('paypal.create_order');
Route::post('/paypal/create-order', 'PaypalController@createOrder')->name('paypal.create_order');
Route::get('/paypal/success', 'PaypalController@success')->name('paypal.success');
Route::get('/paypal/cancel', 'PaypalController@cancel')->name('paypal.cancel');
