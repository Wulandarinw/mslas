<div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto bg-white">
    <!-- Profile Header -->
    <div class="flex items-center space-x-4 mb-8">
        <div class="w-16 h-16 rounded-full bg-gray-200 overflow-hidden">
            <img src="{{ asset('profile-default.jpg') }}" alt="Profile" class="w-full h-full object-cover">
        </div>
        <h1 class="text-2xl font-semibold text-gray-800">{{ $FName }} {{ $LName }}</h1>
    </div>

    <!-- Navigation -->
    <div class="flex mb-8 border-b">
        <button class="px-4 py-2 text-gray-600 border-b-2 border-blue-600">
            My Profile
        </button>
    </div>

    <!-- Form -->
    <form wire:submit.prevent="save" class="space-y-6">
        <div class="text-xl font-semibold mb-4">Ubah Biodata Diri</div>

        <!-- First & Last Name -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">FIRST NAME</label>
                <input type="text" wire:model="FName" 
                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500">
                @error('FName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">LAST NAME</label>
                <input type="text" wire:model="LName" 
                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500">
                @error('LName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-2">EMAIL</label>
            <input type="email" wire:model="email" 
                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500" disabled>
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Phone & Gender -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">NO TELEPON</label>
                <input type="text" wire:model="phone" 
                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500">
                @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-2">JENIS KELAMIN</label>
                <div class="flex space-x-4">
                    <label class="flex items-center">
                        <input type="radio" wire:model="gender" value="Laki-laki" class="mr-2">
                        <span>Laki-laki</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" wire:model="gender" value="Perempuan" class="mr-2">
                        <span>Perempuan</span>
                    </label>
                </div>
                @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Birth Date -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-2">TANGGAL LAHIR</label>
            <input type="date" wire:model="date_of_birth" 
                class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-1 focus:ring-purple-500">
            @error('date_of_birth') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" 
                class="w-full sm:w-auto whitespace-nowrap p-3 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600">
                SIMPAN
            </button>
        </div>

        @if (session()->has('message'))
            <div class="p-4 bg-green-100 text-green-700 rounded-md">
                {{ session('message') }}
            </div>
        @endif
    </form>
</div>