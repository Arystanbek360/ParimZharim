<?php

namespace App\Providers;

use App\Nova\Dashboards\Main;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Actions\ActionEvent;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Modules\ParimZharim\LoyaltyProgram\Adapters\Admin\Resources\DiscountTierAdminResource as LoyaltyProgramModuleDiscountTierAdminResource;
use Modules\ParimZharim\LoyaltyProgram\Adapters\Admin\Resources\LoyaltyProgramCustomerAdminResource;
use Modules\ParimZharim\Objects\Adapters\Admin\Resources\CategoryAdminResource as ObjectsModuleCategoryAdminResource;
use Modules\ParimZharim\Objects\Adapters\Admin\Resources\ServiceObjectAdminResource as ObjectsModuleServiceObjectAdminResource;
use Modules\ParimZharim\Objects\Adapters\Admin\Resources\TagAdminResource as ObjectsModuleTagAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses\ActiveOrdersAdminResource as OrdersModuleActiveOrdersAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses\FailedOrderPaymentAdminResource as OrderModuleFailedOrderPaymentAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses\MobileAppOrdersAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses\OrdersToSyncInExternalSystemAdminResource as OrdersModuleOrdersToSyncInExternalSystemOrdersAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses\PendingOrderPaymentAdminResource as OrderModulePendingOrderPaymentAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses\WaitCancellationOrdersAdminResource as OrdersModuleWaitCancellationOrdersAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses\WaitConfirmationOrdersAdminResource as OrdersModuleWaitConfirmationOrdersAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\Lenses\WaitServiceOrdersAdminResource as OrdersModuleWaitServiceOrdersAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\OrderableProductAdminResource as OrdersModuleOrderableProductAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\OrderableServiceAdminResource as OrdersModuleOrderableServiceAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\OrderableServiceObjectAdminResource as OrdersModuleOrderableObjectAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\OrderableServiceObjectOrderItemAdminResource as OrdersModuleOrderItemServiceObjectAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\OrderAdminResource as OrdersModuleOrderAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\OrderCreatorAdminResource as OrdersModuleOrderCreatorAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\OrderCustomerAdminResource as OrdersModuleOrderCustomerAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\OrderItemProductAdminResource as OrdersModuleOrderItemProductAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\OrderItemServiceAdminResource as OrdersModuleOrderItemServiceAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\OrderPaymentAdminResource as OrderModuleOrderPaymentAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\PlanAdminResource as OrdersModulePlanAdminResource;
use Modules\ParimZharim\Orders\Adapters\Admin\Resources\ScheduleAdminResource as OrdersModuleScheduleAdminResource;
use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Models\OrderPayment;
use Modules\ParimZharim\Orders\Domain\Models\OrderSource;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\ParimZharim\Orders\NovaComponents\ReservationsTool\ReservationsTool as OrdersModuleReservationsTool;
use Modules\ParimZharim\ProductsAndServices\Adapters\Admin\Resources\ProductAdminResource as ProductsAndServicesModuleProductAdminResource;
use Modules\ParimZharim\ProductsAndServices\Adapters\Admin\Resources\ProductCategoryAdminResource as ProductsAndServicesModuleProductCategoryAdminResource;
use Modules\ParimZharim\ProductsAndServices\Adapters\Admin\Resources\ServiceAdminResource as ProductsAndServicesModuleServiceAdminResource;
use Modules\ParimZharim\ProductsAndServices\Adapters\Admin\Resources\ServiceCategoryAdminResource as ProductsAndServicesModuleServiceCategoryAdminResource;
use Modules\ParimZharim\Profile\Adapters\Admin\Resources\CustomerAdminResource as ProfileModuleCustomerAdminResource;
use Modules\ParimZharim\Profile\Adapters\Admin\Resources\EmployeeAdminResource as ProfileModuleEmployeeAdminResource;
use Modules\Shared\CMS\Adapters\Admin\Resources\ContentAdminResource as ContentModuleContentAdminResource;
use Modules\Shared\IdentityAndAccessManagement\Adapters\Admin\Resources\PermissionAdminResource as IdentityAndAccessManagementModulePermissionAdminResource;
use Modules\Shared\IdentityAndAccessManagement\Adapters\Admin\Resources\PhoneVerificationCodeAdminResource as IdentityAndAccessManagementModulePhoneVerificationCodeAdminResource;
use Modules\Shared\IdentityAndAccessManagement\Adapters\Admin\Resources\RoleAdminResource as IdentityAndAccessManagementModuleRoleAdminResource;
use Modules\Shared\IdentityAndAccessManagement\Adapters\Admin\Resources\UserAdminResource as IdentityAndAccessManagementModuleUserAdminResource;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\PhoneVerificationCode;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\Roles;
use Modules\Shared\Notification\Adapters\Admin\NotificationAdminResource as NotificationModuleNotificationAdminResource;
use Modules\Shared\Payment\Adapters\Admin\Resources\PaymentAdminResource as PaymentModulePaymentAdminResource;
use Modules\Shared\Payment\Adapters\Admin\Resources\PaymentItemAdminResource as PaymentModulePaymentItemAdminResource;
use Modules\Shared\Payment\Adapters\Admin\Resources\PaymentMethodAdminResource as PaymentModulePaymentMethodAdminResource;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;
use Modules\Shared\Security\Adapters\Admin\ActionEventAdminResource as SecurityModuleActionEventAdminResource;


class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::userTimezone(function (Request $request) {
            return 'Asia/Almaty';
        });

        Nova::mainMenu(function (Request $request) {
            //Так как сюда приходит авторизованный запрос удалённого пользователя, приходится отдавать пустое меню, чтобы сработал logout
            if (empty($request->user())) {
                return [];
            }
            return [
                MenuSection::dashboard(Main::class)->icon('chart-bar'),
                MenuSection::make('Заказы', [
                    MenuItem::link('Таблица Резервов', '/reservations-tool')
                        ->canSee(function (NovaRequest $request) {
                            return $request->user()->can('viewAny', Order::class);
                        }),
//                    MenuItem::lens(OrdersModuleOrderableObjectAdminResource::class, OrdersModuleOrderableObjectSlotsTableAdminResource::class)
//                        ->canSee(function (NovaRequest $request) {
//                            return $request->user()->can('viewAny', Order::class);
//                        }),
                    MenuItem::lens(OrdersModuleOrderAdminResource::class, OrdersModuleActiveOrdersAdminResource::class)
                        ->canSee(function (NovaRequest $request) {
                            return $request->user()->can('viewAny', Order::class);
                        }),
                    MenuItem::lens(OrdersModuleOrderAdminResource::class, OrdersModuleWaitConfirmationOrdersAdminResource::class)
                        ->withBadgeIf(fn() => Order::where('deleted_at', '=', null)
                            ->whereIn('status', [
                                OrderStatus::CREATED,
                            ])->count(), 'info', fn() => Order::where('deleted_at', '=', null)
                                ->whereIn('status', [
                                    OrderStatus::CREATED,
                                ])->count() > 0)
                        ->canSee(function (NovaRequest $request) {
                            return $request->user()->can('viewAny', Order::class);
                        }),
                    MenuItem::lens(OrdersModuleOrderAdminResource::class, MobileAppOrdersAdminResource::class)
                        ->withBadgeIf(fn() => Order::where('deleted_at', '=', null)
                            ->whereIn('metadata->source', [
                                OrderSource::MOBILE_APP,
                            ])
                            ->whereIn('status', [
                                OrderStatus::CREATED,
                            ])->count(), 'info', fn() => Order::where('deleted_at', '=', null)
                                ->whereIn('metadata->source', [
                                    'MOBILE_APP',
                                ])
                                ->whereIn('status', [
                                    OrderStatus::CREATED,
                                ])->count() > 0)
                        ->canSee(function (NovaRequest $request) {
                            return $request->user()->can('viewAny', Order::class);
                        }),
                    MenuItem::lens(OrdersModuleOrderAdminResource::class, OrdersModuleOrdersToSyncInExternalSystemOrdersAdminResource::class)
                        ->withBadgeIf(fn() => Order::where('deleted_at', '=', null)
                            ->whereIn('status', [
                                OrderStatus::CONFIRMED
                            ])
                            ->whereJsonContains('metadata->is_synced_in_external_system', false)->count(), 'info', fn() => Order::where('deleted_at', '=', null)
                                ->whereIn('status', [
                                    OrderStatus::CONFIRMED
                                ])->whereJsonContains('metadata->is_synced_in_external_system', false)->count() > 0)
                        ->canSee(function (NovaRequest $request) {
                            return $request->user()->can('viewAny', Order::class);
                        }),
                    MenuItem::lens(OrdersModuleOrderAdminResource::class, OrdersModuleWaitServiceOrdersAdminResource::class)
                        ->withBadgeIf(fn() => Order::where('deleted_at', '=', null)
                            ->whereIn('status', [
                                OrderStatus::FINISHED,
                            ])->count(), 'info', fn() => Order::where('deleted_at', '=', null)
                                ->whereIn('status', [
                                    OrderStatus::FINISHED,
                                ])->count() > 0)
                        ->canSee(function (NovaRequest $request) {
                            return $request->user()->can('viewAny', Order::class);
                        }),
                    MenuItem::lens(OrdersModuleOrderAdminResource::class, OrdersModuleWaitCancellationOrdersAdminResource::class)
                        ->withBadgeIf(fn() => Order::where('deleted_at', '=', null)
                            ->whereIn('status', [
                                OrderStatus::CANCELLATION_REQUESTED,
                            ])->count(), 'info', fn() => Order::where('deleted_at', '=', null)
                                ->whereIn('status', [
                                    OrderStatus::CANCELLATION_REQUESTED,
                                ])->count() > 0)
                        ->canSee(function (NovaRequest $request) {
                            return $request->user()->can('viewAny', Order::class);
                        }),

                    MenuItem::resource(OrdersModuleOrderAdminResource::class)->name('Все заказы'),
                ])->icon('shopping-bag')->collapsable(),

                MenuSection::make('Платежи', [
                    MenuItem::resource(OrderModuleOrderPaymentAdminResource::class)->name('Платежи к заказам'),
                    MenuItem::lens(OrderModuleOrderPaymentAdminResource::class, OrderModuleFailedOrderPaymentAdminResource::class)
                        ->withBadgeIf(fn() => OrderPayment::whereIn('status', [
                            PaymentStatus::FAILED
                        ])->where(function ($query) {
                            $query->whereNull('metadata->is_marked_as_shown')
                                ->orWhere('metadata->is_marked_as_shown', false);
                        })->count(), 'info', fn() => OrderPayment::whereIn('status', [
                                PaymentStatus::FAILED
                            ])->where(function ($query) {
                                $query->whereNull('metadata->is_marked_as_shown')
                                    ->orWhere('metadata->is_marked_as_shown', false);
                            })->count() > 0)
                        ->canSee(function (NovaRequest $request) {
                            return $request->user()->can('viewAny', OrderPayment::class);
                        }),
                    MenuItem::lens(OrderModuleOrderPaymentAdminResource::class, OrderModulePendingOrderPaymentAdminResource::class)
                        ->withBadgeIf(fn() => OrderPayment::whereIn('status', [
                            PaymentStatus::PENDING
                        ])->count(), 'info', fn() => OrderPayment::whereIn('status', [
                                PaymentStatus::PENDING
                            ])->count() > 0)
                        ->canSee(function (NovaRequest $request) {
                            return $request->user()->can('viewAny', OrderPayment::class);
                        }),
                ])->icon('credit-card')->collapsable(),

                MenuSection::make('Настройки бронирования', [
                    MenuItem::resource(OrdersModulePlanAdminResource::class)->name('Тарифы'),
                    MenuItem::resource(OrdersModuleScheduleAdminResource::class)->name('Расписание'),
                ])->icon('cog')->collapsable(),

                MenuSection::make('Меню и услуги', [
                    MenuItem::resource(OrdersModuleOrderableProductAdminResource::class)->name('Меню'),
                    MenuItem::resource(ProductsAndServicesModuleProductCategoryAdminResource::class)->name('Категории меню'),
                    MenuItem::resource(OrdersModuleOrderableServiceAdminResource::class)->name('Услуги'),
                    MenuItem::resource(ProductsAndServicesModuleServiceCategoryAdminResource::class)->name('Категории услуг'),
                ])->icon('shopping-cart')->collapsable(),

                MenuSection::make('Программа лояльности', [
                    MenuItem::resource(LoyaltyProgramModuleDiscountTierAdminResource::class)->name('Управление скидками'),
                    MenuItem::resource(LoyaltyProgramCustomerAdminResource::class)->name('Участники программы лояльности'),
                ])->icon('gift')->collapsable(),

                MenuSection::make('Профили', [
                    MenuItem::resource(ProfileModuleEmployeeAdminResource::class)->name('Сотрудники'),
                    MenuItem::resource(OrdersModuleOrderCustomerAdminResource::class)->name('Клиенты'),
                    MenuItem::resource(IdentityAndAccessManagementModuleRoleAdminResource::class)->name('Роли'),
                    MenuItem::resource(IdentityAndAccessManagementModulePermissionAdminResource::class)->name('Разрешения'),
                ])->icon('users')->collapsable(),

                MenuSection::make('Объекты', [
                    MenuItem::resource(OrdersModuleOrderableObjectAdminResource::class)->name('Объекты'),
                    MenuItem::resource(ObjectsModuleCategoryAdminResource::class)->name('Категории объектов'),
                    MenuItem::resource(ObjectsModuleTagAdminResource::class)->name('Теги объектов'),
                ])->icon('home')->collapsable(),

                MenuSection::make('CMS', [
                    MenuItem::resource(ContentModuleContentAdminResource::class)->name('Контент'),
                ])->icon('document')->collapsable(),

                MenuSection::make('Уведомления', [
                    MenuItem::resource(NotificationModuleNotificationAdminResource::class)->name('Уведомления'),
                ])->icon('mail')->collapsable(),

                MenuSection::make('Пользователи', [
                    MenuItem::resource(IdentityAndAccessManagementModuleUserAdminResource::class)->name('Пользователи'),
                ])->icon('user')->collapsable()->canSee(function (NovaRequest $request) {
                    return $request->user()->hasRole(Roles::SUPER_ADMIN);
                }),

                MenuSection::make('Настройки', [
                    MenuItem::resource(PaymentModulePaymentMethodAdminResource::class)->name('Платежные методы'),
                ])->icon('cog')->collapsable(),

                MenuSection::make('Настройки безопасности', [
                    MenuItem::resource(SecurityModuleActionEventAdminResource::class)->name('Лог действий')
                        ->canSee(function (NovaRequest $request) {
                            return $request->user()->can('viewAny', ActionEvent::class);
                        }),
                    MenuItem::resource(IdentityAndAccessManagementModulePhoneVerificationCodeAdminResource::class)->name('Коды верификации')
                        ->canSee(function (NovaRequest $request) {
                            return $request->user()->can('viewAny', PhoneVerificationCode::class);
                        }),
                ])->icon('cog')->collapsable(),


            ];
        });
        Nova::footer(function ($request) {
            return Blade::render('<div style="text-align: center;">© Парим-Жарим 2024. Все права защищены.</div>');
        });
    }

    /**
     * Register the application's Nova resources.
     *
     * @return void
     */
    protected function resources(): void
    {
        Nova::resources([
            IdentityAndAccessManagementModuleUserAdminResource::class,
            IdentityAndAccessManagementModuleRoleAdminResource::class,
            IdentityAndAccessManagementModulePermissionAdminResource::class,
            IdentityAndAccessManagementModulePhoneVerificationCodeAdminResource::class,
            PaymentModulePaymentAdminResource::class,
            PaymentModulePaymentItemAdminResource::class,
            PaymentModulePaymentMethodAdminResource::class,
            ContentModuleContentAdminResource::class,
            SecurityModuleActionEventAdminResource::class,
            ObjectsModuleServiceObjectAdminResource::class,
            ObjectsModuleCategoryAdminResource::class,
            ObjectsModuleTagAdminResource::class,
            ProfileModuleEmployeeAdminResource::class,
            ProfileModuleCustomerAdminResource::class,
            LoyaltyProgramCustomerAdminResource::class,
            ProductsAndServicesModuleProductAdminResource::class,
            ProductsAndServicesModuleProductCategoryAdminResource::class,
            ProductsAndServicesModuleServiceAdminResource::class,
            ProductsAndServicesModuleServiceCategoryAdminResource::class,
            OrdersModuleOrderAdminResource::class,
            OrdersModuleOrderableObjectAdminResource::class,
            OrdersModuleOrderableProductAdminResource::class,
            OrdersModuleOrderableServiceAdminResource::class,
            OrdersModuleOrderCustomerAdminResource::class,
            OrdersModulePlanAdminResource::class,
            OrdersModuleScheduleAdminResource::class,
            OrdersModuleOrderItemServiceAdminResource::class,
            OrdersModuleOrderItemProductAdminResource::class,
            OrdersModuleOrderItemServiceObjectAdminResource::class,
            OrdersModuleOrderCreatorAdminResource::class,
            OrderModuleOrderPaymentAdminResource::class,
            NotificationModuleNotificationAdminResource::class,
            LoyaltyProgramModuleDiscountTierAdminResource::class,
        ]);
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function (User $user) {
            return $user->hasRole(Roles::ADMIN) || $user->hasRole(Roles::SUPER_ADMIN);
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new \App\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            new OrdersModuleReservationsTool,
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
