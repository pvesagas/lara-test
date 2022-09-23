<?php

namespace Tests\Unit\Controller;

use App\Models\CategoryModel;
use Database\Seeders\CategorySeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var string $sCategoryApiUrl
     */
    private $sCategoryApiUrl;

    public function test_controller_get_all_category_must_return_result_false_when_category_table_is_empty()
    {
        $oResult = $this->get($this->sCategoryApiUrl);
        $oResult->assertJson([
            'result' => false,
            'error'  => 'Category table is empty'
        ]);
    }

    public function test_controller_get_all_category_must_return_result_true_when_category_table_is_not_empty()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oResult = $this->get($this->sCategoryApiUrl);
        $oResult->assertOk();
        $this->assertNotEmpty($oResult->json('data'));
        $this->assertArrayHasKey('category', $oResult->json('data')[0]);
    }

    public function test_controller_get_all_categories_must_return_result_true_with_pagination_when_category_table_is_not_empty()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $sParams = http_build_query([
            'paginate' => true,
            'limit'      => 2
        ]);

        $oResult = $this->get($this->sCategoryApiUrl . '?' . $sParams);
        $oResult->assertOk();
        $this->assertNotEmpty($oResult->json('data'));
        $this->assertArrayHasKey('next_page_url', $oResult->json('data'));
        $this->assertNotNull($oResult->json('data')['next_page_url']);
    }

    public function test_controller_get_one_category_must_return_result_false_when_category_is_not_in_database()
    {
        $oResult = $this->get($this->sCategoryApiUrl . '/1');
        $oResult->assertOk();
        $this->assertNotEmpty($oResult->json('error'));
    }

    public function test_controller_get_one_category_must_return_result_true_when_category_table_is_not_empty()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oResult = $this->get($this->sCategoryApiUrl . '/1');
        $oResult->assertOk();
        $this->assertNotEmpty($oResult->json('data'));
    }

    public function test_controller_add_category_must_return_result_false_when_category_parameter_is_empty()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);
        $oResult = $this->post($this->sCategoryApiUrl, [
            'category' => ''
        ]);
        $this->assertEquals(400, $oResult->getStatusCode());
        $this->assertArrayHasKey('category', $oResult->json('error'));
        $this->assertCount(1, $oResult->json('error'));
    }

    public function test_controller_add_category_must_return_result_false_when_category_parameter_is_invalid()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oSeeder->call(ProductSeeder::class);
        $oResult = $this->post($this->sCategoryApiUrl, [
            'category' => true
        ]);
        $this->assertEquals(400, $oResult->getStatusCode());
        $this->assertArrayHasKey('category', $oResult->json('error'));
        $this->assertCount(1, $oResult->json('error'));
    }

    public function test_controller_add_category_must_return_result_false_when_parameters_are_empty()
    {
        $oResult = $this->post($this->sCategoryApiUrl);
        $this->assertEquals(400, $oResult->getStatusCode());
        $this->assertArrayHasKey('category', $oResult->json('error'));
    }

    public function test_controller_add_category_must_return_result_true_when_category_added_to_database()
    {
        $oResult = $this->post($this->sCategoryApiUrl, [
            'category' => 'Category Test'
        ]);

        $this->assertEquals(200, $oResult->getStatusCode());
        $this->assertNotEmpty($oResult->json('data'));
        $this->assertNull($oResult->json('error'));
        $this->assertNotNull((new CategoryModel())->find($oResult->json('data')['id']));
    }

    public function test_controller_update_category_must_return_result_false_when_category_is_not_existing()
    {
        $oResult = $this->put($this->sCategoryApiUrl . '/999');
        $oResult->assertNotFound();
        $this->assertNotEmpty($oResult->json('error'));
    }

    public function test_controller_update_category_must_return_result_false_when_category_parameter_is_invalid()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oResult = $this->put($this->sCategoryApiUrl . '/1', [
            'category' => false,
        ]);
        $this->assertEquals(400, $oResult->getStatusCode());
        $this->assertArrayHasKey('category', $oResult->json('error'));
        $this->assertCount(1, $oResult->json('error'));
    }

    public function test_controller_update_category_must_return_result_true_when_category_updated_in_database()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);

        $oResult = $this->put($this->sCategoryApiUrl . '/1', [
            'category' => 'Updated category'
        ]);
        $oResult->assertOk();
        $this->assertNotEmpty($oResult->json('data'));
        $this->assertEquals('Updated category', $oResult->json('data')['category']);
    }

    public function test_controller_delete_category_must_return_result_false_when_category_is_not_existing()
    {
        $oResult = $this->delete($this->sCategoryApiUrl . '/1');
        $oResult->assertNotFound();
        $this->assertNotEmpty($oResult->json('error'));
    }

    public function test_controller_delete_category_must_return_result_true_when_category_deleted_in_database()
    {
        $oSeeder = new DatabaseSeeder();
        $oSeeder->call(CategorySeeder::class);
        $oModel = new CategoryModel();
        $iCurrentProductCount = $oModel->all()->count();

        $oResult = $this->delete($this->sCategoryApiUrl . '/1');
        $iAfterDeleteProductCount = $oModel->all()->count();
        $oResult->assertOk();
        $this->assertNotEmpty($oResult->json('data'));
        $this->assertEquals($iCurrentProductCount - 1, $iAfterDeleteProductCount);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->sCategoryApiUrl = env('APP_URL', 'https://lara-test.local.com') . '/api/category';
    }
}
