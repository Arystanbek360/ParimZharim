<?php

namespace Modules\Shared\CMS\Adapters\Web\Components;


use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

use Modules\Shared\CMS\Application\Actions\GetContentBySlug;
use Modules\Shared\CMS\Domain\Models\Content;
use Modules\Shared\Core\Adapters\Web\BaseUIComponent;

class PrivacyPolicyComponent extends BaseUIComponent
{

    public Content $content;

    public function mount(GetContentBySlug $getContentBySlug): void
    {
        $this->content = $getContentBySlug->handle('privacy-policy');
    }

    #[Title('Политика конфиденциальности')]
    #[Layout('cms::components.layouts.app')]
    public function render(): View
    {
        return view('cms::components.privacy-policy', [
            'content' => $this->content,
        ]); // Укажите макет здесь
    }

}
