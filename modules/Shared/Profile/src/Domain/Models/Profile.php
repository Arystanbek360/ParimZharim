<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Core\Domain\Models\TypeModelResolver;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\Role;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Profile\Domain\Services\ProfileService;

/**
 * Class Profile
 *
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property ?string $phone
 * @property ?string $email
 * @property string $type
 * @property ?User $user
 * @property array $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Profile extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'profile_profiles';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'type',
        'metadata',
        'user_id',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function booted(): void
    {
        static::creating(function (Profile $profile) {
            ProfileService::createUserIfNotExistsAndUpdateUser($profile);
        });
        static::updating(function (Profile $profile) {
            ProfileService::createUserIfNotExistsAndUpdateUser($profile);
        });
        static::deleting(function (Profile $profile) {
            ProfileService::deleteProfileWithUser($profile);
        });
        static::restoring(function (Profile $profile) {
            ProfileService::restoreProfileWithUser($profile);
        });
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'idm_model_has_roles', 'model_id', 'role_id', 'user_id')
            ->where('idm_model_has_roles.model_type', User::class);
    }

    public function newFromBuilder($attributes = [], $connection = null)
    {
        $attributes = (array) $attributes;

        $type = $attributes['type'] ?? null;

        /** @var TypeModelResolver $typeModelResolver */
        $typeModelResolver = App::make(TypeModelResolver::class);
        $modelClass = $typeModelResolver->getModelClass(static::class, $type);

        if ($modelClass && is_subclass_of($modelClass, static::class)) {
            $model = new $modelClass();
            $model->exists = true;
            $model->setRawAttributes($attributes, true);
            $model->setConnection($connection ?: $this->getConnectionName());

            return $model;
        }

        return parent::newFromBuilder($attributes, $connection);
    }

}
