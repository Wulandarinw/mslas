<!-- resources/views/livewire/checkout-page.blade.php -->
<div>
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Customer Information -->
            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                    <h2 class="text-xl font-semibold mb-6">Customer Information</h2>
                    <div class="mb-9">
                        <!-- Name Fields -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 dark:text-white mb-1" for="first_name">
                                    First Name
                                </label>
                                <input wire:model.defer='first_name'
                                    class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none"
                                    id="first_name" type="text">
                                @error('first_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 dark:text-white mb-1" for="last_name">
                                    Last Name
                                </label>
                                <input wire:model.defer='last_name'
                                    class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none"
                                    id="last_name" type="text">
                                @error('last_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="mt-4">
                            <label class="block text-gray-700 dark:text-white mb-1" for="phone">
                                Phone
                            </label>
                            <input wire:model.defer='phone'
                                class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none"
                                id="phone" type="text">
                            @error('phone')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mt-4">
                            <label class="block text-gray-700 dark:text-white mb-1" for="email">
                                Email
                            </label>
                            <input wire:model.defer='email'
                                class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none"
                                id="email" type="email">
                            @error('email')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Location Selection -->
                        <div class="mt-6 space-y-4">
                            <!-- Province Selection -->
                            <div class="mt-4">
                                <label class="block text-gray-700 dark:text-white mb-1">Province</label>
                                <select wire:model.live="selectedProvince"
                                    class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none">
                                    <option value="">Select Province</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province['province_id'] }}">
                                            {{ $province['province'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('selectedProvince')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="mt-4">
                                <label class="block text-gray-700 dark:text-white mb-1">City</label>
                                <select wire:model.live="selectedCity"
                                    class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none">
                                    <option value="">Select City</option>
                                    @foreach ($cities as $city)
                                        <option value="{{ $city['city_id'] }}">
                                            {{ $city['type'] }} {{ $city['city_name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('selectedCity')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>



                            {{-- <!-- District Selection -->
                            <div>
                                <label class="block text-gray-700 dark:text-white mb-1">District</label>
                                <select wire:model.live="selectedDistrict"
                                    class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none"
                                    {{ !$selectedCity ? 'disabled' : '' }}>
                                    <option value="">Select District</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district['id'] }}">
                                            {{ $district['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('selectedDistrict')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Village Selection -->
                            <div>
                                <label class="block text-gray-700 dark:text-white mb-1">Village</label>
                                <select wire:model.live="selectedVillage"
                                    class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none"
                                    {{ !$selectedDistrict ? 'disabled' : '' }}>
                                    <option value="">Select Village</option>
                                    @foreach ($villages as $village)
                                        <option value="{{ $village['id'] }}">
                                            {{ $village['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('selectedVillage')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div> --}}

                            <!-- Postal Code -->
                            <div>
                                <label class="block text-gray-700 dark:text-white mb-1">Postal Code</label>
                                <input wire:model.defer='postalCode'
                                    class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none"
                                    type="text">
                                @error('postalCode')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Detailed Address -->
                            <div>
                                <label class="block text-gray-700 dark:text-white mb-1">Detailed Address</label>
                                <textarea wire:model.defer="detailedAddress" rows="3"
                                    class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none"
                                    placeholder="Street name, building number, etc."></textarea>
                                @error('detailedAddress')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="lg:col-span-1">
                <!-- Shipping Method -->
                <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                    <h2 class="text-xl font-semibold mb-4">Shipping Method</h2>

                    <div class="grid grid-cols-1 gap-4">
                        {{-- @if ($selectedCity) --}}
                        <div>
                            <label class="block text-gray-700 dark:text-white mb-1">Courier</label>
                            <select wire:model="courierType" wire:change="calculateShipping"
                                class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none">
                                <option value="">Select Courier</option>
                                <option value="jne">JNE</option>
                                <option value="pos">POS Indonesia</option>
                                <option value="tiki">TIKI</option>
                            </select>
                            @error('courierType')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        @if (count($shippingCosts) > 0)
                            <div class="mt-4">
                                <label class="block text-gray-700 dark:text-white mb-1">Available Services:</label>
                                <select wire:model="selectedService" wire:change="updateShippingCost"
                                    class="w-full rounded-lg border py-2 px-3 dark:bg-gray-700 dark:text-white dark:border-none">
                                    <option value="">Select Service</option>
                                    @foreach ($shippingCosts as $cost)
                                        <option value="{{ $cost['service'] }}">
                                            {{ $cost['service'] }} ({{ $cost['description'] }}) -
                                            Rp {{ number_format($cost['cost'][0]['value'], 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('selectedService')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                        {{-- @endif --}}
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                    <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping Cost</span>
                            <span class="font-medium">Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Application Fee</span>
                            <span class="font-medium">Rp 2.000</span>
                        </div>
                        <div class="flex justify-between border-t pt-3 mt-3 font-semibold text-lg">
                            <span>Total</span>
                            <span>Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <!-- Add debug info in development -->
                    <button wire:click="processCheckout" wire:loading.attr="disabled" wire:loading.class="opacity-50"
                        class="w-full mt-6 bg-green-500 text-white py-3 rounded-md hover:bg-green-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ !$selectedService ? 'disabled' : '' }}>
                        <span wire:loading.remove>Proceed to Payment</span>
                        <span wire:loading>Processing...</span>
                    </button>

                    <!-- Loading indicator -->
                    <div wire:loading wire:target="processCheckout"
                        class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                        <div class="bg-white p-6 rounded-lg">
                            <p class="text-lg">Processing your order...</p>
                        </div>
                    </div>
                </div>

                <!-- Cart Items -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Cart Items</h2>
                    <div class="space-y-4">
                        @foreach ($checkoutItems as $item)
                            <div class="flex items-start space-x-4 pb-4 border-b last:border-b-0 last:pb-0">
                                <div class="flex-shrink-0">
                                    <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}"
                                        class="w-16 h-16 object-cover rounded-lg">
                                </div>
                                <div class="flex-grow">
                                    <h3 class="font-medium">{{ $item['name'] }}</h3>
                                    <div class="text-sm text-gray-500 space-y-1 mt-1">
                                        <p>Quantity: {{ $item['quantity'] }}</p>
                                        <p>Weight: {{ $item['product']['weight'] * $item['quantity'] }} gr</p>
                                        <p>Price: Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 font-medium">
                                    Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script type="text/javascript">
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showPayment', (data) => {
                // Data dari backend akan berbentuk array, ambil elemen pertama
                const snapToken = data[0].snapToken;
                window.snap.pay(snapToken, {
                    onSuccess: function(result) {
                        window.location.href = '/';
                    },
                    onPending: function(result) {
                        alert("Waiting your payment!");
                        console.log(result);
                    },
                    onError: function(result) {
                        alert("Payment failed!");
                        console.log(result);
                    },
                    onClose: function() {
                        alert('You closed the popup without finishing the payment');
                    }
                });
            });
        });
    </script>
</div>
