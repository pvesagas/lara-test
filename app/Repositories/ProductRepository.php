<?php

namespace App\Repositories;

use App\Models\ProductModel;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    /**
     * @var ProductModel
     */
    private $oModel;

    /**
     * @param ProductModel $oModel
     */
    public function __construct(ProductModel  $oModel)
    {
        $this->oModel = $oModel;
    }

    /**
     * @param ?bool $bPaginate
     * @param ?int $iPage
     * @param ?int $iLimit
     * @return ProductModel[]|Collection
     */
    public function getAllProduct(?bool $bPaginate = false, ?int $iPage = 1, ?int $iLimit = 10)
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
     * @param int $iProductId
     * @return mixed
     */
    public function getProduct(int $iProductId)
    {
        return $this->oModel->find($iProductId);
    }

    /**
     * @param array $aRequest
     * @return mixed
     */
    public function storeProduct(array $aRequest)
    {
        return $this->oModel->create($aRequest);
    }

    /**
     * @param array $aRequest
     * @param int $iProductId
     * @return mixed
     */
    public function updateProduct(array $aRequest, int $iProductId)
    {
        return $this->oModel->where(['id' => $iProductId])->update($aRequest);
    }

    /**
     * @param int $iProductId
     * @return mixed
     */
    public function deleteProduct(int $iProductId)
    {
        return $this->oModel->find($iProductId)->delete();
    }

}
