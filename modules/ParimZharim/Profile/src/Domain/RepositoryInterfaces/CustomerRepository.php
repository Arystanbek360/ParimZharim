<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Domain\RepositoryInterfaces;

use Modules\ParimZharim\Profile\Domain\Models\Customer;
use Modules\Shared\Core\Domain\BaseRepositoryInterface;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

interface CustomerRepository extends BaseRepositoryInterface {

    public function getCustomerById(int $customerId): ?Customer;

    public function saveCustomer(Customer $customer): void;

    public function deleteCustomer(int $customerId): void;

    public function getCustomerByPhone(string $phone): ?Customer;

    public function getCustomerByUser(User $user): ?Customer;
}
