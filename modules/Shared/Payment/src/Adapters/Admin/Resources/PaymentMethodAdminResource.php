<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Adapters\Admin\Resources;

use Illuminate\Support\Carbon;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentItem;
use Modules\Shared\Payment\Domain\Models\PaymentMethod;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;
use Stepanenko3\NovaJson\Fields\JsonRepeatable;
use Throwable;

class PaymentMethodAdminResource extends BaseAdminResource
{

    public static $model = PaymentMethod::class;

    public static $title = 'id';

    public static $search = [
        'id',
    ];

    public static function label(): string
    {
        return 'Методы оплаты';
    }

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'type')->sortable()
                ->displayUsing(function ($value) {
                    return PaymentMethodType::from($value)->label();
                })
            ->readonly(),
            Boolean::make('Доступен в мобильном приложении', 'is_available_for_mobile')->sortable(),
            Boolean::make('Доступен в административной панели', 'is_available_for_admin')->sortable(),
            Boolean::make('Доступен на сайте', 'is_available_for_web')->sortable(),
        ];
    }

}
