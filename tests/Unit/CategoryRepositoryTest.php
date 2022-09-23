<?php

namespace Tests\Unit;

use App\Models\CategoryModel;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class CategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_category_must_return_data_in_database()
    {
        $oModel = new CategoryModel();
        $oRepository = new CategoryRepository($oModel);

        $oRepository->storeCategory([
           'category' => 'Category Test 1'
        ]);
        $oRepository->storeCategory([
           'category' => 'Category Test 2'
        ]);
        $oRepository->storeCategory([
           'category' => 'Category Test 3'
        ]);

        $oResult = $oRepository->getAllCategory();
        self::assertTrue($oResult instanceof Collection);
        self::assertEquals(3, $oModel->all()->count());
    }

    public function test_get_all_category_must_return_paginated_data()
    {
        $oModel = new CategoryModel();
        $oRepository = new CategoryRepository($oModel);

        $oRepository->storeCategory([
            'category' => 'Category Test 1'
        ]);
        $oRepository->storeCategory([
            'category' => 'Category Test 2'
        ]);
        $oRepository->storeCategory([
            'category' => 'Category Test 3'
        ]);

        $oResult = $oRepository->getAllCategory(true, 1, 2);
        self::assertTrue($oResult instanceof LengthAwarePaginator);
        self::assertTrue($oResult->toArray()['next_page_url'] !== null);
    }

    public function test_get_category_must_return_one_data()
    {
        $oModel = new CategoryModel();
        $oRepository = new CategoryRepository($oModel);

        $oRepository->storeCategory([
            'category' => 'Category Test 1'
        ]);
        $oRepository->storeCategory([
            'category' => 'Category Test 2'
        ]);
        $oRepository->storeCategory([
            'category' => 'Category Test 3'
        ]);

        $oResult = $oRepository->getCategory(1);

        self::assertTrue($oResult instanceof CategoryModel);
        self::assertEquals('Category Test 1', $oRepository->getCategory(1)->category);
        self::assertEquals('Category Test 2', $oRepository->getCategory(2)->category);
        self::assertEquals('Category Test 3', $oRepository->getCategory(3)->category);
    }

    public function test_category_update_must_reflect_in_database()
    {
        $oModel = new CategoryModel();
        $oRepository = new CategoryRepository($oModel);

        $oRepository->storeCategory([
            'category' => 'Category Test'
        ]);

        $oRepository->updateCategory(['category' => 'Category Updated'], 1);
        self::assertEquals('Category Updated', $oModel->find(1)->category);
    }

    public function test_category_delete_must_reflect_in_database()
    {
        $oModel = new CategoryModel();
        $oRepository = new CategoryRepository($oModel);

        $oRepository->storeCategory([
            'category' => 'Category Test'
        ]);

        $oRepository->deleteCategory(1);

        self::assertTrue($oModel->all()->isEmpty());
    }
}
