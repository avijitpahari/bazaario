<?php

namespace App\View\Components\Support;

use Illuminate\View\Component;

class SectionHeader extends Component
{
    public string $icon;
    public string $title;
    public string $subtitle;
    public ?int $count;
    public string $countLabel;

    public function __construct(
        string $icon = 'dashboard',
        string $title = 'Section Title',
        string $subtitle = '',
        ?int $count = null,
        string $countLabel = ''
    ) {
        $this->icon = $icon;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->count = $count;
        $this->countLabel = $countLabel;
    }

    public function render()
    {
        return view('components.support.section-header');
    }
}