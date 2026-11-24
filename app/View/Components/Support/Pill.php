<?php

namespace App\View\Components\Support;

use Illuminate\View\Component;

class Pill extends Component
{
    public string $variant;
    public string $value;
    public string $size;
    public bool $animate;
    public bool $dot;

    public function __construct(
        string $variant = 'default',
        string $value = '',
        string $size = 'md',
        bool $animate = false,
        bool $dot = false
    ) {
        $this->variant = $variant;
        $this->value = $value;
        $this->size = $size;
        $this->animate = $animate;
        $this->dot = $dot;
    }

    public function render()
    {
        return view('components.support.pill');
    }
}