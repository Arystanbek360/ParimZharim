<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Adapters\Admin\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\Shared\Core\Adapters\Admin\BaseAdminAction;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\UpdateUserProfile;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\UserProfileData;
use Illuminate\Validation\Rules;
use Throwable;

class ChangePasswordAdminAction extends BaseAdminAction
{

    /**
     * @throws Throwable
     */
    public function handle(ActionFields $fields, Collection $models): Collection
    {

        foreach ($models as $model) {
            try {
                $user = $model->user;
                $userData = new UserProfileData(
                    password: $fields->password
                );
                UpdateUserProfile::make()->handle($user, $userData);
            } catch (Throwable $e) {
                throw $e;
            }
        }
        return $models;
    }

    public function fields(NovaRequest $request): array
    {
        return [
            Password::make('Пароль', 'password')
                ->onlyOnForms()
                ->creationRules('required', Rules\Password::defaults())
                ->updateRules('nullable', Rules\Password::defaults()),
        ];
    }

    public function name(): string
    {
        return 'Изменить пароль';
    }
}
