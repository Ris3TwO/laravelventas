<?php

namespace App\Http\Controllers\Seller;

use App\User;
use App\Seller;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreSellerProductRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        try {
            $products = $seller->products;

            return $this->showAll($products);
        } catch (QueryException $ex) {
            if (!config('app.debug')) {
                return $this->errorResponse('Ocurrió un problema inesperado, intente nuevamente más tarde.', 500);
            }

            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSellerProductRequest $request, User $seller)
    {
        try {
            $data = $request->all();

            $data['status'] = 'not available';
            $data['image'] = $request->image->store('');
            $data['seller_id'] = $seller->id;

            $product = Product::create($data);

            return $this->showOne($product, 201);
        } catch (QueryException $ex) {
            if (!config('app.debug')) {
                return $this->errorResponse('Ocurrió un problema inesperado, intente nuevamente más tarde.', 500);
            }

            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller, Product $product)
    {
        try {
            $request->validate([
                'quantity' => 'integer|min:1',
                'status' => 'in: ' . Product::PRODUCT_AVAILABLE . ',' . Product::PRODUCT_NOT_AVAILABLE,
                'image' => 'image'
            ]);

            $this->checkSeller($seller, $product);

            $product->fill($request->only(
                'name',
                'description',
                'quantity'
            ));

            if ($request->has('status')) {
                $product->status = $request->status;

                if ($product->checkStatus() && $product->categories()->count() == 0) {
                    return $this->errorResponse('Un producto activo debe tener al menos una categoría', 409);
                }
            }

            if ($product->isClean()) {
                return $this->errorResponse('Se debe especificar al menos un valor diferente para actualizar', 422);
            }

            $product->save();

            return $this->showOne($product);
        } catch (QueryException $ex) {
            if (!config('app.debug')) {
                return $this->errorResponse('El recurso no se pudo actualizar de forma exitosa.', 409);
            }

            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller, Product $product)
    {
        try {
            $this->checkSeller($seller, $product);

            Storage::delete($product->image);

            $product->delete();

            return $this->showOne($product);
        } catch (QueryException $ex) {
            if (!config('app.debug')) {
                return $this->errorResponse('El recurso no se pudo eliminar de forma permanentemente.', 409);
            }

            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    protected function checkSeller(Seller $seller, Product $product)
    {
        if ($seller->id != $product->seller_id) {
            throw new HttpException(422, 'El vendedor especificado no es el vendedor real del producto');
        }
    }
}
