<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['domain' => 'api.' . Config::get('app.url')], function () {

    /**
     * Fallback
     */
    Route::fallback(function(){
        return response()->json([
            'message' => 'Página no encontrada. Si el error persiste, póngase en contacto con el administrador a través del correo: info@laravelventas.test'], 404);
    });

    /**
     * Buyers
     */
    Route::resource('buyers', 'Buyer\BuyerController', ['only' => ['index', 'show']]);

    /**
     * Categories
     */
    Route::resource('categories', 'Category\CategoryController', ['except' => ['create', 'edit']]);
    /**
     * Products
     */
    Route::resource('products', 'Product\ProductController', ['only' => ['index', 'show']]);

    /**
     * Transactions
     */
    Route::resource('transactions', 'Transaction\TransactionController', ['only' => ['index', 'show']]);

    /**
     * Sellers
     */
    Route::resource('sellers', 'Seller\SellerController', ['only' => ['index', 'show']]);

    /**
     * Users
     */
    Route::resource('users', 'User\UserController', ['except' => ['create', 'edit']]);
});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
