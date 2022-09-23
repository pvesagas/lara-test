<?php

namespace Tests\Unit;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_product_must_return_all_product_in_database()
    {
        $this->createCategory();
        $oProductModel = new ProductModel();
        $oProductRepository = new ProductRepository($oProductModel);

        $oProductRepository->storeProduct([
            'category_no' => 1,
            'name'        => 'Product 12',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);
        $oProductRepository->storeProduct([
            'category_no' => 1,
            'name'        => 'Product 13',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);

        $oResult = $oProductRepository->getAllProduct();

        self::assertTrue($oResult instanceof Collection);
        self::assertEquals( 2, $oResult->count());
    }

    public function test_get_all_product_must_return_all_product_in_database_with_pagination_and_limit_2()
    {
        $this->createCategory();
        $oProductModel = new ProductModel();
        $oProductRepository = new ProductRepository($oProductModel);

        $oProductRepository->storeProduct([
            'category_no' => 1,
            'name'        => 'Product 1',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);
        $oProductRepository->storeProduct([
            'category_no' => 1,
            'name'        => 'Product 2',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);
        $oProductRepository->storeProduct([
            'category_no' => 1,
            'name'        => 'Product 3',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);

        $oResult = $oProductRepository->getAllProduct(true, 1, 2);

        self::assertTrue($oResult instanceof LengthAwarePaginator);
        self::assertTrue($oResult->toArray()['next_page_url'] !== null);
    }

    public function test_get_product_must_return_a_product_in_database()
    {
        $this->createCategory();
        $oProductModel = new ProductModel();
        $oProductRepository = new ProductRepository($oProductModel);

        $oProductRepository->storeProduct([
            'category_no' => 1,
            'name'        => 'Product 12',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);

        $oResult = $oProductRepository->getProduct(1);

        self::assertTrue($oResult instanceof ProductModel);
    }

    public function test_store_product_must_save_in_database()
    {
        $this->createCategory();
        $oProductModel = new ProductModel();
        $oProductRepository = new ProductRepository($oProductModel);

        $aProductOne = [
            'category_no' => 1,
            'name'        => 'Product 12',
            'description' => 'This is a description',
            'price'       => 123
        ];
        $aProductTwo = [
            'category_no' => 1,
            'name'        => 'Product 13',
            'description' => 'This is a different description',
            'price'       => 10.15
        ];

        $oProductRepository->storeProduct($aProductOne);
        $oProductRepository->storeProduct($aProductTwo);

        self::assertEquals($aProductOne['category_no'], $oProductRepository->getProduct(1)->category_no);
        self::assertEquals($aProductOne['name'], $oProductRepository->getProduct(1)->name);
        self::assertEquals($aProductOne['description'], $oProductRepository->getProduct(1)->description);
        self::assertEquals($aProductOne['price'], $oProductRepository->getProduct(1)->price);

        self::assertEquals($aProductTwo['category_no'], $oProductRepository->getProduct(2)->category_no);
        self::assertEquals($aProductTwo['name'], $oProductRepository->getProduct(2)->name);
        self::assertEquals($aProductTwo['description'], $oProductRepository->getProduct(2)->description);
        self::assertEquals($aProductTwo['price'], $oProductRepository->getProduct(2)->price);
    }

    public function test_update_product_must_update_in_database()
    {
        $this->createCategory();
        $oProductModel = new ProductModel();
        $oProductRepository = new ProductRepository($oProductModel);
        $oProductModel->create([
            'category_no' => 1,
            'name'        => 'Product 12',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);

        $aUpdateRequest = [
            'category_no' => 1,
            'name'        => 'Product 13',
            'description' => 'This is an updated description',
            'price'       => 123
        ];

        $oProductRepository->updateProduct($aUpdateRequest, 1);

        self::assertEquals($aUpdateRequest['category_no'], $oProductModel->find(1)->category_no);
        self::assertEquals($aUpdateRequest['name'], $oProductModel->find(1)->name);
        self::assertEquals($aUpdateRequest['description'], $oProductModel->find(1)->description);
        self::assertEquals($aUpdateRequest['price'], $oProductModel->find(1)->price);
    }

    public function test_delete_product_must_reflect_in_database()
    {
        $this->createCategory();
        $oProductModel = new ProductModel();
        $oProductRepository = new ProductRepository($oProductModel);
        $oProductModel->create([
            'category_no' => 1,
            'name'        => 'Product 12',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);

        $oProductRepository->deleteProduct(1);
        self::assertTrue($oProductModel->all()->isEmpty());
    }

    private function createCategory()
    {
        $oCategory = new CategoryModel();
        $oCategory->create([
            'category' => 'Category 1'
        ]);
    }
}
