<?php

namespace App\View\Components\Support;

use Illuminate\View\Component;

class Tabs extends Component
{
    public array $items;
    public bool $vertical;

    public function __construct(
        array $items = [],
        bool $vertical = false
    ) {
        $this->items = $items;
        $this->vertical = $vertical;
    }

    public function render()
    {
        return view('components.support.tabs');
    }
}