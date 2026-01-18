<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidInputData;

class ValidateAuthenticationPhoneNumber extends BaseAction {
    /**
     * @throws InvalidInputData
     */
    public function handle(string $phone): void {
        // validate that data contains a valid phone number
        if (!preg_match('/^\+[0-9]{10,14}$/', $phone)) {
            throw new InvalidInputData("Invalid phone number");
        }
    }

}
