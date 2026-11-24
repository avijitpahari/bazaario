<?php

namespace App\View\Components\Support;

use Illuminate\View\Component;

class Card extends Component
{
    public string $elevation;
    public bool $padded;

    public function __construct(
        string $elevation = 'surface',
        bool $padded = true
    ) {
        $this->elevation = $elevation;
        $this->padded = $padded;
    }

    public function render()
    {
        return view('components.support.card');
    }
}