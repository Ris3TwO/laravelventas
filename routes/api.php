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
    Route::resource('buyers.sellers', 'Buyer\BuyerSellerController', ['only' => ['index']]);
    Route::resource('buyers.products', 'Buyer\BuyerProductController', ['only' => ['index']]);
    Route::resource('buyers.categories', 'Buyer\BuyerCategoryController', ['only' => ['index']]);
    Route::resource('buyers.transactions', 'Buyer\BuyerTransactionController', ['only' => ['index']]);

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
    Route::resource('transactions.categories', 'Transaction\TransactionCategoryController', ['only' => ['index']]);
    Route::resource('transactions.sellers', 'Transaction\TransactionSellerController', ['only' => ['index']]);
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
