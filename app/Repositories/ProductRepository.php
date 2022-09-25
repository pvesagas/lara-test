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
     * @param bool|null $bPaginate
     * @param int|null $iPage
     * @param int|null $iLimit
     * @param int|null $iCategory
     * @param string|null $sSearch
     * @return ProductModel[]|Collection
     */
    public function getAllProduct(?bool $bPaginate = false, ?int $iPage = 1, ?int $iLimit = 10, ?int $iCategory = null, ?string $sSearch = null) //: ProductModel|Collection
    {
        $aData = $this->oModel;
        $aUrlParams = [
            'limit' => $iLimit,
        ];
        if (empty($iCategory) === false) {
            $aData = $aData->where('category_no', '=', $iCategory);
            $aUrlParams['paginate'] = true;
        }

        if (empty($sSearch) === false) {
            $aData = $aData->where('name', 'LIKE', '%' . $sSearch . '%')
                ->orWhere('description', 'LIKE', '%' . $sSearch . '%')
                ->orWhere('price', 'LIKE', '%' . $sSearch . '%');
            $aUrlParams['search'] = $sSearch;
        }

        if ($bPaginate === true) {
            $aData = $aData->paginate($iLimit, '*', 'page', $iPage);
            $aUrlParams['paginate'] = true;
            return $aData->withPath(config('app.url') . '/api/product?' . http_build_query($aUrlParams));
        }
        return $aData->get();
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
