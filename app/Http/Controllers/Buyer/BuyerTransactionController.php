<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
        try {
            $transactions = $buyer->transactions;

            return $this->showAll($transactions);
        } catch (QueryException $ex) {
            if (!config('app.debug')) {
                return $this->errorResponse('Ocurrió un problema inesperado, intente nuevamente más tarde.', 500);
            }

            return $this->errorResponse($ex->getMessage(), 500);
        }
    }
}
