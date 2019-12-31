<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Database\QueryException;

class BuyerSellerController extends ApiController
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
            $sellers = $buyer->transactions()->with('product.seller')
                ->get()
                ->pluck('product.seller')
                ->unique('id')
                ->values();

            return $this->showAll($sellers);
        } catch (QueryException $ex) {
            if (!config('app.debug')) {
                return $this->errorResponse('Ocurrió un problema inesperado, intente nuevamente más tarde.', 500);
            }

            return $this->errorResponse($ex->getMessage(), 500);
        }
    }
}
