<div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto">
    <div class="w-full font-poppins mb-6 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg shadow">
        <div class="flex flex-wrap gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Search products..." 
                       class="w-full p-2 border rounded dark:bg-gray-800 dark:text-gray-200">
            </div>
            
            <!-- Search By Dropdown -->
            <select wire:model.live="search_by" 
                    class="p-2 border rounded dark:bg-gray-800 dark:text-gray-200">
                <option value="name">Search by Name</option>
                <option value="description">Search by Description</option>
                <option value="dimension">Search by Dimension</option>
                <option value="category">Search by Category</option>
                <option value="all">Search All</option>
            </select>

            <!-- Sort Dropdown -->
            <select wire:model.live="sort" 
                    class="p-2 border rounded dark:bg-gray-800 dark:text-gray-200">
                <option value="latest">Latest</option>
                <option value="oldest">Oldest</option>
                <option value="name_asc">Name (A-Z)</option>
                <option value="name_desc">Name (Z-A)</option>
            </select>

            <!-- Toggle Filters Button -->
            <button wire:click="toggleFilters" 
                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                {{ $showFilters ? 'Hide Filters' : 'Show Filters' }}
            </button>

            <!-- Reset Filters Button -->
            <button wire:click="resetFilters" 
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Reset Filters
            </button>
        </div>

        <!-- Extended Filters -->
        @if($showFilters)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
            <!-- Categories -->
            <div class="p-4 border rounded dark:border-gray-700">
                <h3 class="font-semibold mb-2 dark:text-gray-200">Categories</h3>
                <div class="space-y-2 max-h-40 overflow-y-auto">
                    @foreach($categories as $category)
                    <label class="flex items-center dark:text-gray-300">
                        <input type="checkbox" 
                               wire:model.live="selected_categories" 
                               value="{{ $category->category_code }}" 
                               class="mr-2">
                        {{ $category->name }}
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Price Range -->
            <div class="p-4 border rounded dark:border-gray-700">
                <h3 class="font-semibold mb-2 dark:text-gray-200">Price Range</h3>
                <div class="space-y-2">
                    <input type="number" 
                           wire:model.live="price_min" 
                           placeholder="Min Price" 
                           class="w-full p-2 border rounded dark:bg-gray-800 dark:text-gray-200">
                    <input type="number" 
                           wire:model.live="price_max" 
                           placeholder="Max Price" 
                           class="w-full p-2 border rounded dark:bg-gray-800 dark:text-gray-200">
                </div>
            </div>

            <!-- Weight Range -->
            <div class="p-4 border rounded dark:border-gray-700">
                <h3 class="font-semibold mb-2 dark:text-gray-200">Weight (g)</h3>
                <div class="space-y-2">
                    <input type="number" 
                           wire:model.live="weight_min" 
                           placeholder="Min Weight" 
                           class="w-full p-2 border rounded dark:bg-gray-800 dark:text-gray-200">
                    <input type="number" 
                           wire:model.live="weight_max" 
                           placeholder="Max Weight" 
                           class="w-full p-2 border rounded dark:bg-gray-800 dark:text-gray-200">
                </div>
            </div>

            <!-- Additional Filters -->
            <div class="p-4 border rounded dark:border-gray-700">
                <h3 class="font-semibold mb-2 dark:text-gray-200">Additional Filters</h3>
                <div class="space-y-2">
                    <input type="text" 
                           wire:model.live="dimension" 
                           placeholder="Dimension" 
                           class="w-full p-2 border rounded dark:bg-gray-800 dark:text-gray-200">
                </div>
            </div>
        </div>
        @endif
    </div>
    <section class="py-5 bg-gray-50 font-poppins dark:bg-gray-800 rounded-lg">
        <div class="px-4 py-4 mx-auto max-w-7xl lg:py-6 md:px-6">
            <div class="flex flex-wrap mb-24 -mx-3">
                <div class="w-full px-3 lg:w-4/4">
                    <div class="flex flex-wrap items-center ">
                        @foreach ($products as $product)
                            <div class="w-full px-2 mb-6 sm:w-1/2 md:w-1/5" wire:key="{{ $product->id }}">
                                <div class="border border-gray-300 dark:border-gray-700">
                                    <div class="relative bg-gray-200">
                                        <a href="/products/{{ $product->name }}" class="">
                                            @php
                                                $firstVariation = $product->variations->first();
                                                $firstImage =
                                                    $firstVariation &&
                                                    is_array($firstVariation->images) &&
                                                    !empty($firstVariation->images)
                                                        ? $firstVariation->images[0]
                                                        : null;
                                            @endphp
                                            @if ($firstImage)
                                                <img src="{{ asset('storage/' . $firstImage) }}"
                                                    alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                            @else
                                                <img src="{{ asset('template/assets/img/product-placeholder.jpg') }}"
                                                    class="w-full h-48 object-cover" alt="No image available">
                                            @endif
                                        </a>
                                    </div>
                                    <div class="p-3 ">
                                        <div class="flex items-center justify-between gap-2 mb-2">
                                            <h3 class="text-xl font-medium dark:text-gray-400">
                                                {{ $product->name }}
                                            </h3>
                                        </div>
                                        <p class="text-lg ">
                                            <span
                                                class="text-green-600 dark:text-green-600">Rp {{ number_format($product->variations->min('price'), 0, ',', '.') }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- pagination start -->
                    <div class="flex justify-end mt-6">
                        {{ $products->links() }}
                    </div>
                    <!-- pagination end -->
                </div>
            </div>
        </div>
    </section>
</div>