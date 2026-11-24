<?php

namespace App\View\Components\Support;

use Illuminate\View\Component;

class Avatar extends Component
{
    public string $type;
    public string $size;
    public string $name;
    public ?string $status;
    public ?string $srLabel;

    public function __construct(
        string $type = 'customer',
        string $size = 'md',
        string $name = 'User',
        ?string $status = null,
        ?string $srLabel = null
    ) {
        $this->type = $type;
        $this->size = $size;
        $this->name = $name;
        $this->status = $status;
        $this->srLabel = $srLabel;
    }

    public function render()
    {
        return view('components.support.avatar');
    }
}