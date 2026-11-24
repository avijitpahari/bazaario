<?php

namespace App\View\Components\Support;

use Illuminate\View\Component;

class DecisionCard extends Component
{
    public string $value;
    public bool $selected;
    public bool $disabled;
    public string $icon;
    public string $title;
    public string $description;
    public string $accentColor;

    public function __construct(
        string $value = '',
        bool $selected = false,
        bool $disabled = false,
        string $icon = '',
        string $title = '',
        string $description = '',
        string $accentColor = 'rose'
    ) {
        $this->value = $value;
        $this->selected = $selected;
        $this->disabled = $disabled;
        $this->icon = $icon;
        $this->title = $title;
        $this->description = $description;
        $this->accentColor = $accentColor;
    }

    public function render()
    {
        return view('components.support.decision-card');
    }
}