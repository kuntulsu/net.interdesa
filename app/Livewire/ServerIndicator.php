<?php

namespace App\Livewire;

use App\Models\System\Resource;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Lazy;

#[Lazy]
class ServerIndicator extends Component
{
    public int $isUp;
    public function render(): View
    {
        $check = Resource::first();
        $this->isUp = $check ? true : false;
        return view("livewire.server-indicator");
    }
}
