<?php

namespace App\Repositories;

use App\Exceptions\BackendException;
use App\Models\CategoryModel;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    /**
     * @var CategoryModel
     */
    private $oModel;

    public function __construct(CategoryModel $oModel)
    {
        $this->oModel = $oModel;
    }

    /**
     * @param bool|null $bPaginate
     * @param int|null $iPage
     * @param int|null $iLimit
     * @return CategoryModel[]|Collection
     */
    public function getAllCategory(?bool $bPaginate = false, ?int $iPage = 1, ?int $iLimit = 10) //: CategoryModel|Collection
    {
        if ($bPaginate === true) {
            $aData = $this->oModel->paginate($iLimit, '*', 'page', $iPage);
            $aUrlParams = [
                'paginate' => true,
                'limit'    => $iLimit,
            ];

            return $aData->withPath(config('app.url') . '/api/product?' . http_build_query($aUrlParams));
        }

        return $this->oModel->all();
    }

    /**
     * @param int $iCategoryId
     * @return ?CategoryModel
     */
    public function getCategory(int $iCategoryId): ?CategoryModel
    {
        return $this->oModel->find($iCategoryId);
    }

    /**
     * @param array $aRequest
     * @return CategoryModel
     */
    public function storeCategory(array $aRequest): CategoryModel
    {
        return $this->oModel->create($aRequest);
    }

    /**
     * @param array $aRequest
     * @param int $iCategoryId
     * @return mixed
     * @throws BackendException
     */
    public function updateCategory(array $aRequest, int $iCategoryId): bool
    {
        $aData = $this->oModel->find($iCategoryId);
        if ($aData === null) {
            throw new BackendException('Category not found', 404);
        }

        return $aData->update($aRequest);
    }

    /**
     * @param int $iCategoryId
     * @return mixed
     * @throws BackendException
     */
    public function deleteCategory(int $iCategoryId): bool
    {
        $aData = $this->oModel->find($iCategoryId);
        if ($aData === null) {
            throw new BackendException('Category not found', 404);
        }

        return $aData->delete($iCategoryId);
    }
}
