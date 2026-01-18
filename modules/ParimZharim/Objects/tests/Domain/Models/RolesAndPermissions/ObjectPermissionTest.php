<?php
namespace Modules\ParimZharim\Objects\Tests\Domain\Models\RolesAndPermissions;
use Modules\ParimZharim\Objects\Tests\TestCase;
use Modules\ParimZharim\Objects\Domain\RolesAndPermissions\ObjectPermission;

class ObjectPermissionTest extends TestCase
{
    public function testLabelMethod()
    {
        $this->assertEquals('Просмотр объектов', ObjectPermission::VIEW_OBJECTS->label(), "Label for VIEW_OBJECTS should be 'Просмотр объектов'");
        $this->assertEquals('Управление объектами', ObjectPermission::MANAGE_OBJECTS->label(), "Label for MANAGE_OBJECTS should be 'Управление объектами'");
    }

    public function testLabelsMethod()
    {
        $expected = [
            'View objects' => 'Просмотр объектов',
            'Manage objects' => 'Управление объектами'
        ];
        $labels = ObjectPermission::labels();
        $this->assertEquals($expected, $labels, "Labels method should return correct labels array");
    }
}