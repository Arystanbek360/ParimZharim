<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Application\Actions;

use Illuminate\Support\Facades\DB;
use Modules\ParimZharim\Profile\Domain\RepositoryInterfaces\CustomerRepository;
use Modules\ParimZharim\Profile\Application\ApplicationError\CannotDeleteCustomerProfile;
use Modules\ParimZharim\Profile\Domain\Errors\CustomerNotFound;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\LogoutAllDevices;
use Throwable;

class DeleteCustomer extends BaseAction {

    public function __construct(
        private readonly CustomerRepository $customerRepository
    )
    {}
    /**
     * @throws CustomerNotFound
     * @throws CannotDeleteCustomerProfile
     */
    public function handle(int $customerId): void
    {
        try {
            DB::beginTransaction();
            $customer = GetCustomerById::make()->handle($customerId);
            if ($customer) {
                $user = $customer->user;
                $this->customerRepository->deleteCustomer($customerId);
                if ($user) {
                    LogoutAllDevices::make()->handle($user);
                }
            }
            DB::commit();
        } catch (CustomerNotFound $e) {
            // Specific case where the customer is not found
            DB::rollBack();
            throw $e;
        } catch (Throwable $e) {
            // General error handling
            DB::rollBack();
            throw new CannotDeleteCustomerProfile($customerId, $e->getMessage());
        }
    }
}
