<?php

namespace App\Livewire;

use App\Models\System\Resource;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class ServerIndicator extends Component 
{
    public int $isUp;
    public function render(): View
    {
        $check = Resource::first();
        if($check) {
            $this->isUp = true;
        }else{
            $this->isUp = false;
        }
        return view('livewire.server-indicator');
    }
}
