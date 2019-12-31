<?php

namespace App\Http\Controllers\Transaction;

use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Database\QueryException;

class TransactionCategoryController extends ApiController
{
    public function __construct()
    {
        $this->middleware('client.credentials')->only(['index']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Transaction $transaction)
    {
        try {
            $categories = $transaction->product->categories;

            return $this->showAll($categories);
        } catch (QueryException $ex) {
            if (!config('app.debug')) {
                return $this->errorResponse('Ocurrió un problema inesperado, intente nuevamente más tarde.', 500);
            }

            return $this->errorResponse($ex->getMessage(), 500);
        }
    }
}
