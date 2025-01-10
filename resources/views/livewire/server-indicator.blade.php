<div wire:poll.5s="">
    @if($this->isUp)

        <x-filament::button icon="heroicon-m-arrows-up-down" color="success">
            Connected
        </x-filament::button>
    @else
        <x-filament::button icon="heroicon-m-link-slash" color="danger">
            Disconnected
        </x-filament::button>
    @endif
</div>