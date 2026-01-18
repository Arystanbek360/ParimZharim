<?php declare(strict_types=1);

namespace Modules\Shared\Core\Domain;

use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class BasePolicy
 */
abstract class BasePolicy
{
    use HandlesAuthorization;
}
