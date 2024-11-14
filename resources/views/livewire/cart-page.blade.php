<div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-semibold">Shopping Cart</h1>
            @if (count($selectedItems) > 0)
                <button wire:click="removeSelected"
                    class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition" x-data
                    @click="confirm('Are you sure you want to remove selected items?') || event.stopImmediatePropagation()">
                    Remove Selected ({{ count($selectedItems) }})
                </button>
            @endif
        </div>

        <div class="flex flex-col md:flex-row gap-4">
            <div class="md:w-3/4">
                <div class="bg-white overflow-x-auto rounded-lg shadow-md p-6 mb-4">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="text-left font-semibold p-2">
                                    <div class="flex items-center">
                                        <input type="checkbox" wire:model.live="selectAll"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    </div>
                                </th>
                                <th class="text-left font-semibold p-2">Product</th>
                                <th class="text-left font-semibold p-2">Price</th>
                                <th class="text-left font-semibold p-2">Quantity</th>
                                <th class="text-left font-semibold p-2">Total</th>
                                <th class="text-left font-semibold p-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cartItems as $variationId => $item)
                                <tr wire:key="cart-item-{{ $variationId }}">
                                    <td class="p-2">
                                        <input type="checkbox" value="{{ $variationId }}"
                                            wire:model.live="selectedItems"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    </td>
                                    <td class="p-2">
                                        <div class="flex items-center">
                                            <img class="h-16 w-16 mr-4 object-cover rounded"
                                                src="{{ url('storage', $item['image']) }}" alt="{{ $item['name'] }}">
                                            <div>
                                                <p class="font-semibold">{{ $item['name'] }}</p>
                                                <p class="text-sm text-gray-600">{{ $item['color'] }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-2">
                                        {{ Number::currency($item['price'], 'IDR') }}
                                    </td>
                                    <td class="p-2">
                                        <div class="flex items-center">
                                            <button
                                                wire:click="updateQuantity('{{ $variationId }}', {{ $item['quantity'] - 1 }})"
                                                class="border rounded-md py-2 px-4 hover:bg-gray-100 transition"
                                                @if ($item['quantity'] <= 1) disabled @endif>
                                                -
                                            </button>
                                            <span class="text-center w-12">{{ $item['quantity'] }}</span>
                                            <button
                                                wire:click="updateQuantity('{{ $variationId }}', {{ $item['quantity'] + 1 }})"
                                                class="border rounded-md py-2 px-4 hover:bg-gray-100 transition">
                                                +
                                            </button>
                                        </div>
                                    </td>
                                    <td class="p-2">
                                        {{ Number::currency($item['quantity'] * $item['price'], 'IDR') }}
                                    </td>
                                    <td class="p-2">
                                        <button wire:click="removeItem('{{ $variationId }}')"
                                            class="bg-slate-300 border-2 border-slate-400 rounded-lg px-3 py-1 hover:bg-red-500 hover:text-white hover:border-red-700 transition"
                                            wire:loading.attr="disabled"
                                            wire:target="removeItem('{{ $variationId }}')">
                                            <span wire:loading.remove wire:target="removeItem('{{ $variationId }}')">
                                                Remove
                                            </span>
                                            <span wire:loading wire:target="removeItem('{{ $variationId }}')">
                                                Removing...
                                            </span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8">
                                        <p class="text-2xl font-semibold text-slate-500">
                                            No items available in cart!
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="md:w-1/4">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h2 class="text-lg font-semibold mb-4">Summary</h2>

                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span>Subtotal ({{ $this->getSelectedQuantityTotal() }})</span>
                            <span>{{ Number::currency($this->getSelectedSubtotal(), 'IDR') }}</span>
                        </div>
                        <hr>
                        <div class="flex justify-between font-semibold">
                            <span>Grand Total</span>
                            <span>{{ Number::currency($this->getSelectedSubtotal(), 'IDR') }}</span>
                        </div>
                    </div>

                    @if ($itemCount > 0)
                        <button wire:click="proceedToCheckout" @class([
                            'w-full mt-6 py-2 px-4 rounded-lg transition',
                            'bg-blue-500 text-white hover:bg-blue-600' => count($selectedItems) > 0,
                            'bg-gray-300 cursor-not-allowed' => count($selectedItems) === 0,
                        ])
                            @disabled(count($selectedItems) === 0)>
                            Checkout
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
