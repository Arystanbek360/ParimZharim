<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\ParimZharim\Profile\Database\Factories\EmployeeFactory;
use Modules\ParimZharim\Profile\Domain\Errors\CannotCreateUserForThisEmployee;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\IdentityAndAccessManagement\Domain\Errors\UserWithEmailAlreadyExists;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\Role;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Throwable;

/**
 * Class Employee
 *
 * @property int $id
 * @property string $name
 * @property ?string $phone
 * @property ?string $email
 * @property string $job_title
 * @property ?User $user
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Employee extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'profile_employees';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return EmployeeFactory */
    protected static function newFactory(): EmployeeFactory
    {
        return EmployeeFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (Employee $employee) {
            self::createUserIfNotExistsAndUpdateUser($employee);
        });
        static::updating(function (Employee $employee) {
            self::createUserIfNotExistsAndUpdateUser($employee);
        });
        static::deleting(function (Employee $employee) {
            if ($employee->user) {
                $employee->user->delete();
            }
        });
        static::restoring(function (Employee $employee) {
            $user = $employee->user()->withTrashed()->first();
            if ($user) {
                $user->restore();
            }
        });
    }

    /**
     * @throws Throwable
     */
    private static function createUserIfNotExistsAndUpdateUser($employee): void
    {
        try {
            DB::beginTransaction();
            self::createAssociatedUserIfNotExists($employee);
            self::updateAssociatedUser($employee);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws CannotCreateUserForThisEmployee
     */
    private static function createAssociatedUserIfNotExists(Employee $employee): void
    {
        // find if user already associated with this employee
        $associatedUser = $employee->user;
        if (!$associatedUser) {
            try {
                DB::beginTransaction();
                $user = User::create([
                    'name' => $employee->name,
                    'email' => null,
                    'phone' => null,
                    'password' => Str::password(64)
                ]);
                $employee->user()->associate($user);
                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                throw new CannotCreateUserForThisEmployee();
            }
        }
    }

    /**
     * @throws UserWithEmailAlreadyExists
     */
    private static function updateAssociatedUser(Employee $employee): void
    {
        $user = $employee->user;

        if ($employee->email) {
            $existingUser = User::where('email', $employee->email)->first();
            if ($existingUser && $existingUser->id !== $user->id) {
                throw new UserWithEmailAlreadyExists($employee->email);
            }
        }

        if ($user) {
            $user->name = $employee->name;
            $user->email = $employee->email;
            $user->save();
        }
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'idm_model_has_roles', 'model_id', 'role_id', 'user_id')
            ->where('idm_model_has_roles.model_type', User::class);
    }
}
