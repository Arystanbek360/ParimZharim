<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Adapters\Api\Transformers;

use Illuminate\Support\Carbon;
use Modules\Shared\Core\Adapters\Api\BaseTransformer;
use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Core\Domain\BaseValueObject;
use Modules\Shared\Notification\Domain\Models\Notification;

class NotificationTransformer extends BaseTransformer
{

    public function transform(Notification|BaseDTO|array|BaseValueObject|BaseModel $data): array
    {
        $sentAt = Carbon::parse($data->sent_at)->setTimezone('Asia/Almaty')->format('Y-m-d H:i:s');
        return [
            'id' => $data->id,
            'title' => $data->title,
            'message' => $data->body,
            'is_read' => (bool) $data->read_at,
            'sent_at' => $sentAt,
        ];
    }
}
