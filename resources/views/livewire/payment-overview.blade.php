<x-filament::section>
    <x-slot name="heading">
        Payment Details
    </x-slot>   

    <form>
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                
                <tbody>
                    <tr class="bg-white dark:bg-gray-800">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Nama Pelanggan
                        </th>
                        <td class="px-6 py-4">
                            {{ $this->ownerRecord->nama }}
                        </td>
        
                    </tr>
                    <tr class="bg-white dark:bg-gray-800">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Alamat Pelanggan
                        </th>
                        <td class="px-6 py-4">
                            {{ $this->ownerRecord->alamat }}
                        </td>
        
                    </tr>
                    <tr class="bg-white dark:bg-gray-800">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Nomor Telepon
                        </th>
                        <td class="px-6 py-4">
                             {{ $this->ownerRecord->telp }}
                        </td>
        
                    </tr>
                    <tr class="bg-white dark:bg-gray-800">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Nama Paket
                        </th>
                        <td class="px-6 py-4">
                            {{ $this->ownerRecord->profil->secret->profile }}
                        </td>
        
                    </tr>
                    <tr class="bg-white dark:bg-gray-800">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Diskon
                        </th>
                        <td class="px-6 py-4">
                            <select id="diskon" name="diskon" wire:model.change="diskon" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">Pilih Diskon</option>
                                <option value="pemasangan">Diskon Pemasangan</option>
                              </select>
                        </td>
        
                    </tr>
                    <tr class="bg-white dark:bg-gray-800">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Harga Paket
                        </th>
                        <td class="px-6 py-4">
                            @if($diskon)
                                Rp.{{ number_format($this->ownerRecord->profil->secret->paket->harga?->harga/2, 2) }}
                            @else
                                Rp.{{ number_format($this->ownerRecord->profil->secret->paket->harga?->harga, 2) }}
                            @endif
                        </td>
        
                    </tr>
                    
                    <tr class="bg-white dark:bg-gray-800">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Metode Pembayaran
                        </th>
                        <td class="px-6 py-4">
                            <select id="payment_method" name="payment_method" wire:model.change="payment_method" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                @foreach (\App\PaymentMethodEnum::cases() as $payment_method)
                                
                                    <option value="{{ $payment_method->value }}">{{ $payment_method->name }}</option>
        
                                @endforeach
                              </select>
                        </td>
        
                    </tr>
                    <tr class="bg-white dark:bg-gray-800">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Tanggal Pembayaran
                        </th>
                        <td class="px-6 py-4">
                            {{ \Carbon\Carbon::now()->format("d F Y H:i:s") }}
                        </td>
        
                    </tr>
                    <tr class="bg-white dark:bg-gray-800">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            Operator
                        </th>
                        <td class="px-6 py-4">
                            {{ auth()->user()->name }}
                        </td>
        
                    </tr>
                </tbody>
            </table>
            
        </div>
        <x-filament::button wire:click="pay" type="submit" style="margin-top:1rem">
            Bayar
        </x-filament::button>
    </form>

</x-filament::section>