<?php declare(strict_types=1);

namespace Modules\Shared\Core\Domain;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 *
 * @mixin Builder
 */
abstract class BaseModel extends Model {}
