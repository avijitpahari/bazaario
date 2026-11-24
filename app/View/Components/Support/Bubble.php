<?php

namespace App\View\Components\Support;

use Illuminate\View\Component;

class Bubble extends Component
{
    public string $sender;
    public string $message;
    public string $time;
    public bool $avatar;
    public string $name;
    public ?string $attachment;
    public ?string $systemType;

    public function __construct(
        string $sender = 'customer',
        string $message = '',
        string $time = '',
        bool $avatar = true,
        string $name = '',
        ?string $attachment = null,
        ?string $systemType = null
    ) {
        $this->sender = $sender;
        $this->message = $message;
        $this->time = $time;
        $this->avatar = $avatar;
        $this->name = $name;
        $this->attachment = $attachment;
        $this->systemType = $systemType;
    }

    public function render()
    {
        return view('components.support.bubble');
    }
}