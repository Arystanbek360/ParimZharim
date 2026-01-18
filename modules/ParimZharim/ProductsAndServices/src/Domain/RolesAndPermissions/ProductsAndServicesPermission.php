<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Domain\RolesAndPermissions;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

/**
 * Enum ProductsAndServicesPermission
 *
 * Перечисление разрешений для управления меню и услугами.
 *
 * @property string $value Значение разрешения.
 *
 * @example
 * $permission = ProductsAndServicesPermission::VIEW_PRODUCTS_AND_SERVICES;
 * echo $permission->label(); // Выводит: "Просмотр меню и услуг"
 *
 * @see BaseEnum
 *
 * @version 1.0.0
 * @since 2024-04-24
 */
enum ProductsAndServicesPermission: string implements BaseEnum
{
    use BaseEnumTrait;
    case VIEW_PRODUCTS_AND_SERVICES = 'View products and services';
    case MANAGE_PRODUCTS_AND_SERVICES = 'Manage products and services';

    /**
     * Возвращает отображаемое наименование для разрешения.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::VIEW_PRODUCTS_AND_SERVICES => 'Просмотр меню и услуг',
            self::MANAGE_PRODUCTS_AND_SERVICES => 'Управление меню и услугами',
        };
    }

    /**
     * Возвращает отображаемое описание для разрешения.
     *
     * @return string
     */
    public function description(): string
    {
        return match ($this) {
            self::VIEW_PRODUCTS_AND_SERVICES => 'Просмотр списка и информации меню и услуг',
            self::MANAGE_PRODUCTS_AND_SERVICES => 'Создание, редактирование и удаление меню и услуг',
        };
    }
}
