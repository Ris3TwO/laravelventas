<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Transformers\CategoryTransformer;
use Illuminate\Database\QueryException;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class CategoryController extends ApiController
{
    public function __construct()
    {
        $this->middleware('client.credentials')->only(['index', 'show']);
        $this->middleware('auth:api')->except(['index', 'show']);
        $this->middleware('transform.input:' . CategoryTransformer::class)->only(['store', 'update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $categories = Category::all();

            return $this->showAll($categories);
        } catch (QueryException $ex) {
            if (!config('app.debug')) {
                return $this->errorResponse('El recurso no se pudo obtener, intente nuevamente más tarde.', 409);
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
    public function store(Request $request)
    {
        $this->allowedAdminAction();
        try {
            $data = $request->validate([
                'name' => 'required|min:2|max:20',
                'description' => 'required|min:2|max:150',
            ]);

            $category = Category::create($data);

            return $this->showOne($category, 201);
        } catch (QueryException $ex) {
            if (!config('app.debug')) {
                return $this->errorResponse('El recurso no se pudo obtener, intente nuevamente más tarde.', 409);
            }

            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        try {
            return $this->showOne($category);
        } catch (QueryException $ex) {
            if (!config('app.debug')) {
                return $this->errorResponse('El recurso no se pudo obtener, intente nuevamente más tarde.', 409);
            }

            return $this->errorResponse($ex->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $this->allowedAdminAction();
        try {
            $request->validate([
                'name' => 'min:2|max:20',
                'description' => 'min:2|max:150',
            ]);


            $category->fill($request->only(
                'name',
                'description'
            ));

            if ($category->isClean()) {
                return $this->errorResponse('Debe especificar al menos un valor diferente para actualizar', 422);
            }

            $category->save();

            return $this->showOne($category);
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
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $this->allowedAdminAction();
        try {
            $category->delete();

            return $this->showOne($category);
        } catch (QueryException $ex) {
            if (!config('app.debug')) {
                return $this->errorResponse('El recurso no se pudo eliminar de forma permanentemente.', 409);
            }

            return $this->errorResponse($ex->getMessage(), 500);
        }
    }
}
