<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Profile\Application\ApplicationErrors\InvalidInputData;
use Modules\Shared\Profile\Application\DTO\ProfileData;
use Modules\Shared\Profile\Domain\Models\Profile;
use Modules\Shared\Profile\Domain\Repositories\ProfileRepository;
use Throwable;


class CreateProfileForUserIfNotExists extends BaseAction
{

    public function __construct(
        private readonly ProfileRepository $profileRepository
    )
    {}
    /**
     * @throws Throwable
     */
    public function handle(ProfileData $data, User $user): void
    {
        if (!$data->phone) {
            throw new InvalidInputData('Phone are required');
        }

        $customer = GetProfileByPhone::make()->handle($data->phone);
        if ($customer) {
            return;
        }

        $customer = new Profile();

        $customer->name = $data->name ?? $data->phone;
        $customer->phone = $data->phone;
        $customer->email = $data->email;

        $this->profileRepository->saveProfileAndAssociateWithUser($customer, $user);
    }
}
