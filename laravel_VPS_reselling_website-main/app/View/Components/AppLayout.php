<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $keywords = null,
        public string $robots = 'index, follow',
        public ?string $canonical = null,
        public ?string $ogImage = null,
        public mixed $schema = null,
        public string $headerVariant = 'hero',
        public bool $hideHeader = false,
        public bool $hideFooter = false,
    ) {}

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
