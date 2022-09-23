<?php

namespace App\Repositories;

use App\Exceptions\BackendException;
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
    public function getAllProduct(?bool $bPaginate = false, ?int $iPage = 1, ?int $iLimit = 10) //: ProductModel|Collection
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
     * @return ?ProductModel
     */
    public function getProduct(int $iProductId): ?ProductModel
    {
        return $this->oModel->find($iProductId);
    }

    /**
     * @param array $aRequest
     * @return ProductModel
     */
    public function storeProduct(array $aRequest): ProductModel
    {
        return $this->oModel->create($aRequest);
    }

    /**
     * @param array $aRequest
     * @param int $iProductId
     * @return bool
     * @throws BackendException
     */
    public function updateProduct(array $aRequest, int $iProductId): bool
    {
        $aData = $this->oModel->find($iProductId);
        if ($aData === null) {
            throw new BackendException('Product not found', 404);
        }

        return $aData->update($aRequest);
    }

    /**
     * @param int $iProductId
     * @return bool
     * @throws BackendException
     */
    public function deleteProduct(int $iProductId): bool
    {
        $aData = $this->oModel->find($iProductId);
        if ($aData === null) {
            throw new BackendException('Product not found', 404);
        }

        return $aData->delete();
    }

}
