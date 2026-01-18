<?php

namespace Modules\ParimZharim\ProductsAndServices\Tests\Domain\RolesAndPermissions;

use Modules\ParimZharim\ProductsAndServices\Domain\RolesAndPermissions\ProductsAndServicesPermission;
use PHPUnit\Framework\TestCase;

class ProductsAndServicesPermissionTest extends TestCase
{
    /** @test */
    public function it_returns_correct_label_for_view_permission()
    {
        $permission = ProductsAndServicesPermission::VIEW_PRODUCTS_AND_SERVICES;
        $this->assertEquals('Просмотр меню и услуг', $permission->label());
    }

    /** @test */
    public function it_returns_correct_label_for_manage_permission()
    {
        $permission = ProductsAndServicesPermission::MANAGE_PRODUCTS_AND_SERVICES;
        $this->assertEquals('Управление меню и услугами', $permission->label());
    }

    /** @test */
    public function it_returns_correct_labels_array()
    {
        $expectedLabels = [
            'View products and services' => 'Просмотр меню и услуг',
            'Manage products and services' => 'Управление меню и услугами',
        ];

        $this->assertEquals($expectedLabels, ProductsAndServicesPermission::labels());
    }
}
