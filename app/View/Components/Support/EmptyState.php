<?php

namespace App\View\Components\Support;

use Illuminate\View\Component;

class EmptyState extends Component
{
    public string $icon;
    public string $title;
    public string $description;

    public function __construct(
        string $icon = 'inbox',
        string $title = 'No items found',
        string $description = ''
    ) {
        $this->icon = $icon;
        $this->title = $title;
        $this->description = $description;
    }

    public function render()
    {
        return view('components.support.empty-state');
    }
}