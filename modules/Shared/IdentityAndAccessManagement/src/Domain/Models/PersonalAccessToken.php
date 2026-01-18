<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken {

    protected $table = 'idm_personal_access_tokens';
}
