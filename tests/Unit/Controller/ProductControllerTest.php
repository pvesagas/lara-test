<?php

namespace Tests\Unit\Controller;

use App\Models\ProductModel;
use Database\Seeders\CategorySeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private $sProductApiUrl;

    public function test_controller_get_all_product_must_return_result_false_when_product_table_is_empty()
    {
        $oResult = $this->get($this->sProductApiUrl);
        $oResult->assertJson([
            'result' => false,
            'error'  => 'Product table is empty'
        ]);
    }

    public function test_controller_get_all_product_must_return_result_true_when_product_table_is_not_empty()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);
        $oResult = $this->get($this->sProductApiUrl);
        $oResult->assertOk();
        $this->assertNotEmpty($oResult->json('data'));
        $this->assertArrayHasKey('name', $oResult->json('data')[0]);
    }

    public function test_controller_get_all_products_must_return_result_true_with_pagination_when_product_table_is_not_empty()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);

        $sParams = http_build_query([
            'paginate' => true,
            'limit'      => 5
        ]);
        $oResult = $this->get($this->sProductApiUrl . '?' . $sParams);
        $oResult->assertOk();
        $this->assertNotEmpty($oResult->json('data'));
        $this->assertArrayHasKey('next_page_url', $oResult->json('data'));
        $this->assertNotNull($oResult->json('data')['next_page_url']);
    }

    public function test_controller_get_all_products_must_return_two_products_containing_category_2_when_category_parameter_is_present()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oProductModel = new ProductModel();
        $oProductModel->create([
            'category_no' => 1,
            'name'        => 'Product 1',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);
        $oProductModel->create([
            'category_no' => 2,
            'name'        => 'Product 2',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);
        $oProductModel->create([
            'category_no' => 2,
            'name'        => 'Item 1',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);
        $sParams = http_build_query([
            'paginate' => true,
            'limit'    => 10,
            'category' => 2
        ]);

        $oResult = $this->get($this->sProductApiUrl . '?' . $sParams);

        $oResult->assertOk();
        $this->assertNotEmpty($oResult->json('data'));
        $this->assertArrayHasKey('next_page_url', $oResult->json('data'));
        $this->assertNull($oResult->json('data')['next_page_url']);
        $this->assertCount(2, $oResult->json('data')['data']);
    }

    public function test_controller_get_all_products_must_return_two_products_containing_the_value_of_search_parameter()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oProductModel = new ProductModel();
        $oProductModel->create([
            'category_no' => 1,
            'name'        => 'Product 1',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);
        $oProductModel->create([
            'category_no' => 2,
            'name'        => 'Product 2',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);
        $oProductModel->create([
            'category_no' => 2,
            'name'        => 'Item 1',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);
        $sParams = http_build_query([
            'paginate' => true,
            'limit'    => 10,
            'search'   => 'Product'
        ]);

        $oResult = $this->get($this->sProductApiUrl . '?' . $sParams);

        $oResult->assertOk();
        $this->assertNotEmpty($oResult->json('data'));
        $this->assertArrayHasKey('next_page_url', $oResult->json('data'));
        $this->assertNull($oResult->json('data')['next_page_url']);
        $this->assertCount(2, $oResult->json('data')['data']);
    }

    public function test_controller_get_all_products_must_return_one_product_containing_the_value_of_search_parameter_and_category()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oProductModel = new ProductModel();
        $oProductModel->create([
            'category_no' => 1,
            'name'        => 'Product 1',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);
        $oProductModel->create([
            'category_no' => 2,
            'name'        => 'Product',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);
        $oProductModel->create([
            'category_no' => 2,
            'name'        => 'Item 1',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);
        $sParams = http_build_query([
            'paginate' => true,
            'limit'    => 10,
            'category' => 2,
            'search'   => 'Product'
        ]);

        $oResult = $this->get($this->sProductApiUrl . '?' . $sParams);

        $oResult->assertOk();
        $this->assertNotEmpty($oResult->json('data'));
        $this->assertArrayHasKey('next_page_url', $oResult->json('data'));
        $this->assertNull($oResult->json('data')['next_page_url']);
        $this->assertCount(1, $oResult->json('data')['data']);
    }

    public function test_controller_get_one_product_must_return_result_false_when_product_is_not_in_database()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oResult = $this->get($this->sProductApiUrl . '/1');
        $oResult->assertOk();
        $this->assertNotEmpty($oResult->json('error'));
    }

    public function test_controller_get_one_product_must_return_result_true_when_product_table_is_not_empty()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);
        $oResult = $this->get($this->sProductApiUrl . '/1');
        $oResult->assertOk();
        $this->assertNotEmpty($oResult->json('data'));
    }

    public function test_controller_add_product_must_return_result_false_when_parameters_are_empty()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);
        $oResult = $this->post($this->sProductApiUrl);
        $this->assertEquals(400, $oResult->getStatusCode());
        $this->assertArrayHasKey('name', $oResult->json('error'));
        $this->assertArrayHasKey('description', $oResult->json('error'));
        $this->assertArrayHasKey('price', $oResult->json('error'));
        $this->assertArrayHasKey('category_no', $oResult->json('error'));
    }

    public function test_controller_add_product_must_return_result_false_when_category_no_parameter_is_empty()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);
        $oResult = $this->post($this->sProductApiUrl, [
            'name'        => 'Product 1',
            'description' => 'This is a description',
            'price'       => 10.15
        ]);
        $this->assertEquals(400, $oResult->getStatusCode());
        $this->assertArrayHasKey('category_no', $oResult->json('error'));
        $this->assertCount(1, $oResult->json('error'));
    }

    public function test_controller_add_product_must_return_result_false_when_price_parameter_is_empty()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);
        $oResult = $this->post($this->sProductApiUrl, [
            'category_no' => 1,
            'name'        => 'Product 1',
            'description' => 'This is a description',
        ]);
        $this->assertEquals(400, $oResult->getStatusCode());
        $this->assertArrayHasKey('price', $oResult->json('error'));
        $this->assertCount(1, $oResult->json('error'));
    }

    public function test_controller_add_product_must_return_result_false_when_name_parameter_is_empty()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);
        $oResult = $this->post($this->sProductApiUrl, [
            'category_no' => 1,
            'price'       => 10.15,
            'description' => 'This is a description',
        ]);
        $this->assertEquals(400, $oResult->getStatusCode());
        $this->assertArrayHasKey('name', $oResult->json('error'));
        $this->assertCount(1, $oResult->json('error'));
    }

    public function test_controller_add_product_must_return_result_false_when_description_parameter_is_empty()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);
        $oResult = $this->post($this->sProductApiUrl, [
            'category_no' => 1,
            'price'       => 10.15,
            'name'        => 'Product 1',
        ]);
        $this->assertEquals(400, $oResult->getStatusCode());
        $this->assertArrayHasKey('description', $oResult->json('error'));
        $this->assertNotEmpty($oResult->json('error'));
    }

    public function test_controller_add_product_must_return_result_true_when_product_added_to_database()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);
        $oResult = $this->post($this->sProductApiUrl, [
            'category_no' => 1,
            'price'       => 10.15,
            'name'        => 'Product 1',
            'description' => 'This is a description',
        ]);

        $this->assertEquals(200, $oResult->getStatusCode());
        $this->assertNotEmpty($oResult->json('data'));
        $this->assertNull($oResult->json('error'));
        $this->assertNotNull((new ProductModel())->find($oResult->json('data')['id']));
    }

    public function test_controller_update_product_must_return_result_false_when_product_is_not_existing()
    {
        $oResult = $this->put($this->sProductApiUrl . '/1');
        $oResult->assertNotFound();
        $this->assertNotEmpty($oResult->json('error'));
    }

    public function test_controller_update_product_must_return_result_false_when_category_no_parameter_is_invalid()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);
        $oResult = $this->put($this->sProductApiUrl . '/1', [
            'category_no' => 'Test',
        ]);
        $this->assertEquals(400, $oResult->getStatusCode());
        $this->assertArrayHasKey('category_no', $oResult->json('error'));
        $this->assertCount(1, $oResult->json('error'));
    }

    public function test_controller_update_product_must_return_result_false_when_price_parameter_is_invalid()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);
        $oResult = $this->put($this->sProductApiUrl . '/1', [
            'price' => 'test'
        ]);

        $this->assertEquals(400, $oResult->getStatusCode());
        $this->assertArrayHasKey('price', $oResult->json('error'));
        $this->assertCount(1, $oResult->json('error'));
    }

    public function test_controller_update_product_must_return_result_false_when_name_parameter_is_invalid()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);
        $oResult = $this->put($this->sProductApiUrl . '/1', [
            'name' => 1
        ]);
        $this->assertEquals(400, $oResult->getStatusCode());
        $this->assertArrayHasKey('name', $oResult->json('error'));
        $this->assertCount(1, $oResult->json('error'));
    }

    public function test_controller_update_product_must_return_result_false_when_description_parameter_is_invalid()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);
        $oResult = $this->put($this->sProductApiUrl . '/1', [
            'description' => 1,
        ]);
        $this->assertEquals(400, $oResult->getStatusCode());
        $this->assertArrayHasKey('description', $oResult->json('error'));
        $this->assertNotEmpty($oResult->json('error'));
    }

    public function test_controller_update_product_must_return_result_true_when_product_updated_in_database()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);

        $oResult = $this->put($this->sProductApiUrl . '/1', [
            'description' => 'Update Description'
        ]);
        $oResult->assertOk();
        $this->assertNotEmpty($oResult->json('data'));
        $this->assertEquals('Update Description', $oResult->json('data')['description']);
    }


    public function test_controller_delete_product_must_return_result_false_when_product_is_not_existing()
    {
        $oResult = $this->delete($this->sProductApiUrl . '/1');
        $oResult->assertNotFound();
        $this->assertNotEmpty($oResult->json('error'));
    }

    public function test_controller_delete_product_must_return_result_true_when_product_deleted_in_database()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);
        $oModel = new ProductModel();
        $iCurrentProductCount = $oModel->all()->count();
        $oResult = $this->delete($this->sProductApiUrl . '/1');
        $iAfterDeleteProductCount = $oModel->all()->count();
        $oResult->assertOk();
        $this->assertNotEmpty($oResult->json('data'));
        $this->assertEquals($iCurrentProductCount - 1, $iAfterDeleteProductCount);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->sProductApiUrl = env('APP_URL', 'https://lara-test.local.com') . '/api/product';
    }
}
