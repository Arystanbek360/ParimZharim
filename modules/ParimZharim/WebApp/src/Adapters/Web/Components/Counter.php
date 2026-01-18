<?php

namespace Modules\ParimZharim\WebApp\Adapters\Web\Components;

use Modules\Shared\Core\Adapters\Web\BaseUIComponent;

class Counter extends BaseUIComponent
{
    public $count = 1;

    public function increment()
    {
        $this->count++;
    }

    public function decrement()
    {
        $this->count--;
    }

    public function render()
    {
        return view('webapp::components.counter');
    }
}
