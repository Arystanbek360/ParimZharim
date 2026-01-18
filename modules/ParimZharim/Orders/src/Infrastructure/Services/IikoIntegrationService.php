<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Infrastructure\Services;

use Modules\ParimZharim\Orders\Domain\Services\IikoIntegrationServiceInterface;
use Modules\Shared\Core\Infrastructure\BaseService;

class IikoIntegrationService extends BaseService implements IikoIntegrationServiceInterface {
    public function sendOrderToIiko(array $orderData): array
    {
        // TODO: Implement sendOrderToIiko() method.
        return [];
    }
}
