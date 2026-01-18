<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\ParimZharim\Profile\Domain\Errors\CustomerNotFound;
use Modules\ParimZharim\Profile\Domain\Models\Customer;
use Modules\ParimZharim\Profile\Domain\RepositoryInterfaces\CustomerRepository;
use Modules\Shared\Core\Infrastructure\BaseRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Throwable;

class EloquentCustomerRepository extends BaseRepository implements CustomerRepository
{

    public function getCustomerById(int $customerId): ?Customer
    {
        return Customer::find($customerId);
    }

    public function saveCustomer(Customer $customer): void
    {
        $customer->save();
    }

    public function saveCustomerAndAssociateWithUser(Customer $customer, User $user): void
    {
        $customer->user_id = $user->id;
        $this->saveCustomer($customer);
    }

    /**
     * @throws CustomerNotFound
     * *@throws Throwable
     */
    public function deleteCustomer(int $customerId): void
    {
        DB::beginTransaction();
        try {
            $customer = $this->getCustomerById($customerId);
            if (!$customer) {
                throw new CustomerNotFound($customerId);
            }

            $customer->name = 'Deleted User';
            $customer->phone = '0000000000' . $customerId;
            $customer->email = 'deleted+' . $customerId . '@deleted.user';
            $customer->date_of_birth = null;
            $this->saveCustomer($customer);
            $customer->delete();

            $user = $customer->user;
            if ($user) {
                $user->email = 'deleted+' . $customerId . '@deleted.user';
                $user->name = 'Deleted User';
                $user->phone = '0000000000' . $customerId;
                $user->save();
                $user->delete();
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getCustomerByPhone(string $phone): ?Customer
    {
        return Customer::where('phone', $phone)->first();
    }

    public function getCustomerByUser(User $user): ?Customer
    {
        return Customer::where('user_id', $user->id)->first();
    }
}
