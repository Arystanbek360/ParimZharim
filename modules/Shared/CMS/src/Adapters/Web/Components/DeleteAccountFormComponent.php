<?php declare(strict_types=1);

namespace Modules\Shared\CMS\Adapters\Web\Components;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Illuminate\View\View;
use Modules\Shared\Core\Adapters\Web\BaseUIComponent;
use Modules\Shared\Notification\Application\NotifyAllAdmins;

class DeleteAccountFormComponent extends BaseUIComponent
{
    #[Validate('required')]
    public string $phone_number;

    public function save(): void
    {
        $this->validate();

        $message = "Запрос на удаление аккаунта.\nНомер телефона: $this->phone_number";
        NotifyAllAdmins::make()->handle($message);

        session()->flash('message', 'Ваш запрос на удаление аккаунта отправлен.');

        $this->reset();
    }

    #[Title('Запрос на удаление аккаунта')]
    #[Layout('cms::components.layouts.app')]
    public function render(): View
    {
        return view('cms::components.delete-account-form');
    }
}
