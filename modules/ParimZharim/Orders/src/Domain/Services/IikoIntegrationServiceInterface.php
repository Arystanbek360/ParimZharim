<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Services;

use Modules\Shared\Core\Domain\BaseServiceInterface;

interface IikoIntegrationServiceInterface extends BaseServiceInterface {
    public function sendOrderToIiko(array $orderData): array;
}
