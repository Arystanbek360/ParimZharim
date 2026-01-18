<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Adapters\Admin\Resources;

use Illuminate\Support\Carbon;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentItem;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;
use Stepanenko3\NovaJson\Fields\JsonRepeatable;
use Throwable;

class PaymentItemAdminResource extends BaseAdminResource
{

    public static $model = PaymentItem::class;

    public static $title = 'id';

    public static $search = [
        'id', 'name'
    ];

    public static function label(): string
    {
        return 'Позиции платежа';
    }

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Название', 'name'),
            Currency::make('Цена', 'price')
                ->currency('KZT'),
            Number::make('Количество', 'quantity'),
        ];
    }

}
