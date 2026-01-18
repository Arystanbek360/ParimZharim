<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\NovaServiceProvider::class,
    \Modules\Shared\IdentityAndAccessManagement\IdentityAndAccessManagementModuleProvider::class,
    \Modules\Shared\Notification\NotificationModuleProvider::class,
    \Modules\Shared\CMS\CmsModuleProvider::class,
    \Modules\ParimZharim\Objects\ObjectsModuleProvider::class,
    \Modules\ParimZharim\Profile\ProfileModuleProvider::class,
    \Modules\ParimZharim\ProductsAndServices\ProductsAndServicesModuleProvider::class,
    \Modules\ParimZharim\Orders\OrdersModuleProvider::class,
    \Modules\ParimZharim\WebApp\WebAppModuleProvider::class,
    \Modules\ParimZharim\LoyaltyProgram\LoyaltyProgramModuleProvider::class,
    \Modules\Shared\Payment\PaymentModuleProvider::class,
    \Modules\Shared\Security\SecurityModuleProvider::class
];
