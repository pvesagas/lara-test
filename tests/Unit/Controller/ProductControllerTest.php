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
        $oResult->assertSuccessful();
        $this->assertNotEmpty($oResult->json('data'));
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
        $oResult->assertSuccessful();
        $this->assertNotEmpty($oResult->json('data'));
        $this->assertArrayHasKey('next_page_url', $oResult->json('data'));
    }

    public function test_controller_get_one_product_must_return_result_true_when_product_table_is_not_empty()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);
        $oResult = $this->get($this->sProductApiUrl . '/1');
        $oResult->assertSuccessful();
        $this->assertNotEmpty($oResult->json('data'));
    }

    public function test_controller_add_product_must_return_result_false_when_parameters_are_empty()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);
        $oResult = $this->post($this->sProductApiUrl);
        $this->assertEquals(400, $oResult->getStatusCode());
        self::assertArrayHasKey('name', $oResult->json('error'));
        self::assertArrayHasKey('description', $oResult->json('error'));
        self::assertArrayHasKey('price', $oResult->json('error'));
        self::assertArrayHasKey('category_no', $oResult->json('error'));
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
        self::assertArrayHasKey('category_no', $oResult->json('error'));
        self::assertCount(1, $oResult->json('error'));
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
        self::assertArrayHasKey('price', $oResult->json('error'));
        self::assertCount(1, $oResult->json('error'));
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
        self::assertArrayHasKey('name', $oResult->json('error'));
        self::assertCount(1, $oResult->json('error'));
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
        self::assertArrayHasKey('description', $oResult->json('error'));
        self::assertNotEmpty($oResult->json('error'));
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

        self::assertEquals(200, $oResult->getStatusCode());
        self::assertNotEmpty($oResult->json('data'));
        self::assertNull($oResult->json('error'));
        self::assertNotNull((new ProductModel())->find($oResult->json('data')['id']));
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
        self::assertArrayHasKey('category_no', $oResult->json('error'));
        self::assertCount(1, $oResult->json('error'));
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
        self::assertArrayHasKey('price', $oResult->json('error'));
        self::assertCount(1, $oResult->json('error'));
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
        self::assertArrayHasKey('name', $oResult->json('error'));
        self::assertCount(1, $oResult->json('error'));
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
        self::assertArrayHasKey('description', $oResult->json('error'));
        self::assertNotEmpty($oResult->json('error'));
    }

    public function test_controller_update_product_must_return_result_true_when_product_updated_in_database()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);

        $oResult = $this->put($this->sProductApiUrl . '/1', [
            'description' => 'Update Description'
        ]);
        $oResult->assertSuccessful();
        $this->assertNotEmpty($oResult->json('data'));
        self::assertEquals('Update Description', $oResult->json('data')['description']);
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
        $oResult->assertSuccessful();
        $this->assertNotEmpty($oResult->json('data'));
        $this->assertEquals($iCurrentProductCount - 1, $iAfterDeleteProductCount);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->sProductApiUrl = env('APP_URL', 'https://lara-test.local.com') . '/api/product';
    }
}
