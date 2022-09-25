<?php

namespace Tests\Unit\Repository;

use App\Exceptions\BackendException;
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

        $this->assertTrue($oResult instanceof Collection);
        $this->assertCount( 2, $oResult);
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

        $this->assertTrue($oResult instanceof LengthAwarePaginator);
        $this->assertTrue($oResult->toArray()['next_page_url'] !== null);
    }

    public function test_get_all_product_must_return_product_with_category_equal_to_1_when_searched_using_category()
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
            'category_no' => 2,
            'name'        => 'Product 2',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);
        $oProductRepository->storeProduct([
            'category_no' => 2,
            'name'        => 'Item 1',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);

        $oResult = $oProductRepository->getAllProduct(true, 1, 2, 1);
        $this->assertTrue($oResult instanceof LengthAwarePaginator);
        $this->assertTrue($oResult->toArray()['next_page_url'] === null);
        $this->assertCount(1, $oResult);
    }

    public function test_get_all_product_must_return_product_with_category_equal_to_2_and_name_to_item_when_searched_using_category_and_search_query()
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
            'category_no' => 2,
            'name'        => 'Product 2',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);
        $oProductRepository->storeProduct([
            'category_no' => 2,
            'name'        => 'Item 1',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);

        $oResult = $oProductRepository->getAllProduct(true, 1, 2, 2, 'Item');
        $this->assertTrue($oResult instanceof LengthAwarePaginator);
        $this->assertTrue($oResult->toArray()['next_page_url'] === null);
        $this->assertCount(1, $oResult);
    }

    public function test_get_all_product_must_return_product_with_name_product_when_searched()
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
            'name'        => 'Item 1',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);

        $oResult = $oProductRepository->getAllProduct(true, 1, 2, null, 'Product');
        $this->assertTrue($oResult instanceof LengthAwarePaginator);
        $this->assertTrue($oResult->toArray()['next_page_url'] === null);
        $this->assertCount(2, $oResult);
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

        $this->assertTrue($oResult instanceof ProductModel);
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

        $this->assertEquals($aProductOne['category_no'], $oProductRepository->getProduct(1)->category_no);
        $this->assertEquals($aProductOne['name'], $oProductRepository->getProduct(1)->name);
        $this->assertEquals($aProductOne['description'], $oProductRepository->getProduct(1)->description);
        $this->assertEquals($aProductOne['price'], $oProductRepository->getProduct(1)->price);

        $this->assertEquals($aProductTwo['category_no'], $oProductRepository->getProduct(2)->category_no);
        $this->assertEquals($aProductTwo['name'], $oProductRepository->getProduct(2)->name);
        $this->assertEquals($aProductTwo['description'], $oProductRepository->getProduct(2)->description);
        $this->assertEquals($aProductTwo['price'], $oProductRepository->getProduct(2)->price);
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

        $this->assertEquals($aUpdateRequest['category_no'], $oProductModel->find(1)->category_no);
        $this->assertEquals($aUpdateRequest['name'], $oProductModel->find(1)->name);
        $this->assertEquals($aUpdateRequest['description'], $oProductModel->find(1)->description);
        $this->assertEquals($aUpdateRequest['price'], $oProductModel->find(1)->price);
    }

    public function test_update_product_must_throw_exception_when_product_is_not_found_in_database()
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

        $this->expectException(BackendException::class);
        $this->expectExceptionCode(404);
        $oProductRepository->updateProduct($aUpdateRequest, 5);

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
        $this->assertTrue($oProductModel->all()->isEmpty());
    }

    private function createCategory()
    {
        $oCategory = new CategoryModel();
        $oCategory->create([
            'category' => 'Category 1'
        ]);
        $oCategory->create([
            'category' => 'Category 2'
        ]);
    }
}
