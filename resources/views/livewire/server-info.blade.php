
    <x-filament-widgets::widget>
            <x-filament::section class="cus-alert-border">
                {{-- server offline alert --}}
                Cannot Connect to <strong>{{ config("routeros.host") }}:{{ config("routeros.port") }}</strong>. Some feature may not work!
            </x-filament::section>
    </x-filament-widgets::widget>
