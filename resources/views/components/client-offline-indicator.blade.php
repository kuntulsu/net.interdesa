
<x-filament-widgets::widget>
    <div class="p-4 mb-4 text-sm w-full text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
        @if ($secretDisabled)
            <span class="font-medium">Pelanggan Terisolir</span> Please Enable Client to Continue
        @else
            <span class="font-medium">Client Offline!</span> Please Check Connectivity and Try Again
        @endif
    </div>
</x-filament-widgets::widget>
