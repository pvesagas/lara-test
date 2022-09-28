<?php

namespace App\Http\Controllers;

use App\Exceptions\BackendException;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * @var ProductRepository
     */
    private $oRepository;

    public function __construct(ProductRepository $oService)
    {
        $this->oRepository = $oService;
    }

    /**
     * @param Request $oRequest
     * @return JsonResponse
     */
    public function getAllProduct(Request $oRequest)
    {
        $oProducts = $this->oRepository->getAllProduct(
            $oRequest->input('paginate'),
            $oRequest->input('page'),
            $oRequest->input('limit'),
            $oRequest->input('category'),
            $oRequest->input('search'),
        );

        if ($oProducts->isEmpty() === true) {
            return response()->json([
                'result' => false,
                'error'  => 'Product table is empty'
            ]);

        }
        return response()->json([
            'result' => true,
            'data'   => $oProducts,
        ]);
    }

    /**
     * @param int $iProductId
     * @return JsonResponse
     */
    public function getProduct(int $iProductId)
    {
        $oProducts = $this->oRepository->getProduct($iProductId);
        if ($oProducts === null) {
            return response()->json([
                'result' => false,
                'error'  => 'Product not found'
            ]);
        }

        return response()->json([
            'result' => true,
            'data'   => $oProducts
        ]);
    }

    /**
     * @param StoreProductRequest $oRequest
     * @return JsonResponse
     */
    public function storeProduct(StoreProductRequest $oRequest)
    {
        try {
            return response()->json(
                [
                    'result' => true,
                    'data' => $this->oRepository->storeProduct($oRequest->validated()),
                ]
            );
        } catch (Exception $oException) {
            Log::error('Store Product', ['request' => $oRequest->all(), 'message' => $oException->getMessage()]);
            return response()->json(
                [
                    'result' => false,
                    'error' => 'Something went wrong',
                ],
                422
            );
        }
    }

    /**
     * @param int $iProductId
     * @param UpdateProductRequest $oRequest
     * @return JsonResponse
     * @throws Exception
     */
    public function updateProduct(int $iProductId, UpdateProductRequest $oRequest)
    {
        try {
            $bResult = $this->oRepository->updateProduct($oRequest->validated(), $iProductId);
            return response()->json([
                'result' => (bool) $bResult,
                'data'   => $this->oRepository->getProduct($iProductId),
            ]);
        } catch (BackendException $oException) {
            return response()->json(
                [
                    'result' => false,
                    'error' => $oException->getMessage(),
                ],
                $oException->getCode()
            );
        }
    }

    /**
     * @param int $iProductId
     * @return JsonResponse
     */
    public function deleteProduct(int $iProductId)
    {
        try {
            $bResult = $this->oRepository->deleteProduct($iProductId);
            return response()->json([
                'result' => (bool) $bResult,
                'data'   => [
                    'id' => $iProductId
                ],
            ]);
        } catch (BackendException $oException) {
            return response()->json(
                [
                    'result' => false,
                    'error' => $oException->getMessage(),
                ],
                $oException->getCode()
            );
        }
    }
}
