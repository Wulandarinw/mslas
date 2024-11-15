<div>
    {{-- Address List --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-medium text-gray-900">Alamat</h2>
                <button
                    wire:click="openModal"
                    class="px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-md hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500"
                >
                    TAMBAH
                </button>
            </div>

            <div class="space-y-6">
                @foreach($addresses as $address)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium">{{ $address->fullName }}</h3>
                                <p class="text-gray-600">{{ $address->phone }}</p>
                                <p class="text-gray-600">
                                    {{ $address->address_detail }}, 
                                    {{ $address->address->kelurahan }}, 
                                    {{ $address->address->kecamatan }}, 
                                    {{ $address->address->kabupaten }}, 
                                    {{ $address->address->provinsi }}, 
                                    {{ $address->address->kodepos }}
                                </p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $address->mark_as === 'Rumah' ? 'bg-pink-100 text-pink-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $address->mark_as }}
                            </span>
                        </div>
                        <div class="mt-4 flex space-x-4">
                            <button wire:click="$emit('editAddress', {{ $address->id }})" class="text-gray-600 hover:text-gray-900">
                                <span class="text-sm">EDIT</span>
                            </button>
                            <button wire:click="$emit('deleteAddress', {{ $address->id }})" class="text-red-600 hover:text-red-900">
                                <span class="text-sm">DELETE</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Alamat Baru</h3>
                        
                        <form wire:submit.prevent="save" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input type="text" wire:model="name" class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                <input type="text" wire:model="phone" class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Provinsi</label>
                                    <select wire:model="provinsi" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm">
                                        <option value="">Pilih Provinsi</option>
                                        @foreach($provinces as $province)
                                            <option value="{{ $province->provinsi }}">{{ $province->provinsi }}</option>
                                        @endforeach
                                    </select>
                                    @error('provinsi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Kabupaten</label>
                                    <select wire:model="kabupaten" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm">
                                        <option value="">Pilih Kabupaten</option>
                                        @foreach($kabupatens as $kab)
                                            <option value="{{ $kab->kabupaten }}">{{ $kab->kabupaten }}</option>
                                        @endforeach
                                    </select>
                                    @error('kabupaten') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Kecamatan</label>
                                    <select wire:model="kecamatan" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm">
                                        <option value="">Pilih Kecamatan</option>
                                        @foreach($kecamatans as $kec)
                                            <option value="{{ $kec->kecamatan }}">{{ $kec->kecamatan }}</option>
                                        @endforeach
                                    </select>
                                    @error('kecamatan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Kelurahan</label>
                                    <select wire:model="kelurahan" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-pink-500 focus:border-pink-500 sm:text-sm">
                                        <option value="">Pilih Kelurahan</option>
                                        @foreach($kelurahans as $kel)
                                            <option value="{{ $kel->kelurahan }}">{{ $kel->kelurahan }}</option>
                                        @endforeach
                                    </select>
                                    @error('kelurahan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kode Pos</label>
                                <input type="text" wire:model="kodepos" readonly class="mt-1 bg-gray-50 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('kodepos') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Detail Lainnya (Nama Jalan, Gedung, No. Rumah)</label>
                                <textarea wire:model="street_address" rows="3" class="mt-1 focus:ring-pink-500 focus:border-pink-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                @error('street_address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tandai Sebagai:</label>
                                <div class="flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" wire:model="type" value="Rumah" class="form-radio h-4 w-4 text-pink-600 border-gray-300 focus:ring-pink-500">
                                        <span class="ml-2">Rumah</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" wire:model="type" value="Kantor" class="form-radio h-4 w-4 text-pink-600 border-gray-300 focus:ring-pink-500">
                                        <span class="ml-2">Kantor</span>
                                    </label>
                                </div>
                                @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </form>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button 
                            wire:click="save"
                            type="button" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-pink-600 text-base font-medium text-white hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            OK
                        </button>
                        <button 
                            wire:click="closeModal"
                            type="button" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            NANTI SAJA
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>