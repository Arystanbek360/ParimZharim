<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\ParimZharim\Profile\Database\Factories\CustomerFactory;
use Modules\ParimZharim\Profile\Domain\Errors\CannotCreateUserForThisCustomer;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\IdentityAndAccessManagement\Domain\Errors\UserWithPhoneAlreadyExists;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Throwable;

/**
 * Class Customer
 *
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property ?string $phone
 * @property ?string $email
 * @property ?Carbon $date_of_birth
 * @property ?User $user
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Customer extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'profile_customers';

    protected $casts = [
        'date_of_birth' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return CustomerFactory */
    protected static function newFactory(): CustomerFactory
    {
        return CustomerFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (Customer $customer) {
            self::createUserIfNotExistsAndUpdateUser($customer);
        });
        static::updating(function (Customer $customer) {
            self::createUserIfNotExistsAndUpdateUser($customer);
        });
    }

    /**
     * @throws Throwable
     */
    private static function createUserIfNotExistsAndUpdateUser($customer): void
    {
        try {
            DB::beginTransaction();
            self::createAssociatedUserIfNotExists($customer);
            self::updateAssociatedUser($customer);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws CannotCreateUserForThisCustomer
     */
    private static function createAssociatedUserIfNotExists(Customer $customer): void
    {
        // find if user already associated with this customer
        $associatedUser = $customer->user;
        if (!$associatedUser) {
            try {
                DB::beginTransaction();
                $user = User::create([
                    'name' => $customer->name,
                    'email' => null,
                    'phone' => null,
                    'password' => Str::password(64)
                ]);
                $customer->user()->associate($user);
                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                throw new CannotCreateUserForThisCustomer();
            }
        }
    }

    /**
     * @throws UserWithPhoneAlreadyExists
     */
    private static function updateAssociatedUser(Customer $customer): void
    {
        $user = $customer->user;

        if ($customer->phone) {
            $existingUser = User::where('phone', $customer->phone)->first();
            if ($existingUser && $existingUser->id !== $user->id) {
                throw new UserWithPhoneAlreadyExists($customer->phone);
            }
        }

        if ($user) {
            $user->name = $customer->name;
            $user->phone = $customer->phone;
            $user->save();
        }
    }
}
