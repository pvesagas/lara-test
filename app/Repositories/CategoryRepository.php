<?php

namespace App\Repositories;

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
    public function getAllCategory(?bool $bPaginate = false, ?int $iPage = 1, ?int $iLimit = 10)
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
     * @return mixed
     */
    public function getCategory(int $iCategoryId)
    {
        return $this->oModel->find($iCategoryId);
    }

    /**
     * @param array $aRequest
     * @return mixed
     */
    public function storeCategory(array $aRequest)
    {
        return $this->oModel->create($aRequest);
    }

    /**
     * @param array $aRequest
     * @param int $iCategoryId
     * @return mixed
     */
    public function updateCategory(array $aRequest, int $iCategoryId)
    {
        return $this->oModel->where(['id' => $iCategoryId])->update($aRequest);
    }

    /**
     * @param int $iCategoryId
     * @return mixed
     */
    public function deleteCategory(int $iCategoryId)
    {
        return $this->oModel->find($iCategoryId)->delete();
    }
}
