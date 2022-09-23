<?php

namespace App\Repositories;

use App\Exceptions\BackendException;
use App\Models\ProductModel;
use Exception;
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
     * @throws Exception
     */
    public function updateProduct(array $aRequest, int $iProductId)
    {
        $aData = $this->oModel->find($iProductId);
        if ($aData === null) {
            throw new BackendException('Product not found', 404);
        }

        return $aData->update($aRequest);
    }

    /**
     * @param int $iProductId
     * @return mixed
     * @throws BackendException
     */
    public function deleteProduct(int $iProductId)
    {
        $aData = $this->oModel->find($iProductId);
        if ($aData === null) {
            throw new BackendException('Product not found', 404);
        }

        return $aData->delete();
    }

}
