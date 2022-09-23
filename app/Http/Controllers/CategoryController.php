<?php

namespace App\Http\Controllers;

use App\Exceptions\BackendException;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Repositories\CategoryRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * @var CategoryRepository
     */
    private $oRepository;

    public function __construct(CategoryRepository $oService)
    {
        $this->oRepository = $oService;
    }

    /**
     * @param Request $oRequest
     * @return JsonResponse
     */
    public function getAllCategory(Request $oRequest): JsonResponse
    {
        $oCategories = $this->oRepository->getAllCategory(
            $oRequest->input('paginate'),
            $oRequest->input('page'),
            $oRequest->input('limit'),
        );

        if ($oCategories->isEmpty() === true) {
            return response()->json([
                'result' => false,
                'error'  => 'Category table is empty'
            ]);

        }
        return response()->json([
            'result' => true,
            'data'   => $oCategories
        ]);
    }

    /**
     * @param int $iCategoryId
     * @return JsonResponse
     */
    public function getCategory(int $iCategoryId): JsonResponse
    {
        $oCategory = $this->oRepository->getCategory($iCategoryId);
        if ($oCategory === null) {
            return response()->json([
                'result' => false,
                'error'  => 'Category not found'
            ]);
        }

        return response()->json([
            'result' => true,
            'data'   => $oCategory
        ]);
    }

    /**
     * @param StoreCategoryRequest $oRequest
     * @return JsonResponse
     */
    public function storeCategory(StoreCategoryRequest $oRequest): JsonResponse
    {
        try {
            return response()->json(
                [
                    'result' => false,
                    'data' => $this->oRepository->storeCategory($oRequest->validated()),
                ]
            );
        } catch (Exception $oException) {
            Log::error('Store Category', ['request' => $oRequest->all(), 'message' => $oException->getMessage()]);
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
     * @param int $iCategoryId
     * @param UpdateCategoryRequest $oRequest
     * @return JsonResponse
     * @throws Exception
     */
    public function updateCategory(int $iCategoryId, UpdateCategoryRequest $oRequest): JsonResponse
    {
        try {
            $bResult = $this->oRepository->updateCategory($oRequest->validated(), $iCategoryId);
            return response()->json([
                'result' => (bool) $bResult,
                'data'   => $this->oRepository->getCategory($iCategoryId),
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
     * @param int $iCategoryId
     * @return JsonResponse
     */
    public function deleteCategory(int $iCategoryId): JsonResponse
    {
        try {
            $bResult = $this->oRepository->deleteCategory($iCategoryId);
            return response()->json([
                'result' => (bool) $bResult,
                'data'   => [
                    'id' => $iCategoryId
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
