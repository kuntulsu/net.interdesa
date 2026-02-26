<x-filament-panels::page>
    <x-filament::section
        icon="heroicon-o-clipboard-document-list"
        icon-color="info"
    >
        <x-slot name="heading">
            Ticket Details
        </x-slot>
        {{-- content --}}
        
        {{ $this->productInfolist }}
        {{-- end of content --}}
    </x-filament::section>
    <x-filament::section
        icon="heroicon-o-wrench"
        icon-color="info"
        :headerActions="$this->createAction"
    >
        <x-slot name="heading">
            Ticket Progress
        </x-slot>
        {{-- content --}}
        <!-- component -->
        <div class="flex flex-col items-center">
            @foreach ($this->record->progress as $progress)
            <div class="w-full max-w-md flex flex-col">
                <div @class([
                    "flex",
                    "items-center", 
                    "dark:bg-slate-800",
                    "gap-4", 
                    "mb-6", 
                    "bg-stone-100" ,
                    "rounded-lg", 
                    "p-2.5",
                    "border",
                    "border-emerald-500" => $progress->is_solved,
                    "border-stone-200" => !$progress->is_solved,
                ])>
                    <div class="relative flex-none">
                    <span class="relative grid h-10 w-10 place-items-center rounded-full bg-stone-800">
                        @if($progress->is_solved)
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-emerald-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        @else

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                            </svg>
                        @endif
                          
                        
                    </span>
                    </div>
                    <div class="flex-1">
                    <p class="text-base font-bold text-stone-800 dark:text-white">{{ $progress->task }}</p>
                    <small class="text-sm text-stone-500">
                        {{ $progress->created_at->format('d F Y H:i:s') }} - {{ $progress->user->name }}
                    </small>
                    </div>
                </div>
        

            </div>
            @endforeach
            <div class="w-full max-w-md flex flex-col">
                <div class="flex items-center dark:bg-slate-800 gap-4 mb-6 bg-stone-100 rounded-lg p-2.5 border border-stone-200">
                    <div class="relative flex-none">
                    <span class="relative grid h-10 w-10 place-items-center rounded-full bg-stone-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                        
                    </span>
                    </div>
                    <div class="flex-1">
                    <p class="text-base font-bold text-stone-800 dark:text-white">Tiket Dibuat</p>
                    <small class="text-sm text-stone-500">
                        {{ $this->record->created_at->format('d F Y H:i:s') }} - {{ $this->record->user->name }}
                    </small>
                    </div>
                </div>
        

            </div>
        
            <!-- Centered Note -->
            <p class="text-center mt-4 text-sm text-gray-700">
            Inspired by 
            <a
                href="https://www.creative-tim.com/david-ui/docs/html/timeline"
                class="text-blue-600 hover:underline"
                target="_blank"
            >
                David UI
            </a>
            Framework
            </p>
        </div>
        
        {{-- end of content --}}
    </x-filament::section>
</x-filament-panels::page>
