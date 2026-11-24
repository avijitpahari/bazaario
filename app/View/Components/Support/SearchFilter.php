<?php

namespace App\View\Components\Support;

use Illuminate\View\Component;

class SearchFilter extends Component
{
    public string $action;
    public string $method;
    public array $search;
    public array $filters;
    public array $hidden;

    public function __construct(
        string $action = '',
        string $method = 'GET',
        array $search = ['name' => 'search', 'placeholder' => 'Search...', 'value' => ''],
        array $filters = [],
        array $hidden = []
    ) {
        $this->action = $action;
        $this->method = $method;
        $this->search = $search;
        $this->filters = $filters;
        $this->hidden = $hidden;
    }

    public function render()
    {
        return view('components.support.search-filter');
    }
}