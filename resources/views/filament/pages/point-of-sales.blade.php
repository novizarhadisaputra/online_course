<x-filament-panels::page>
    <div class="grid grid-cols-2 gap-4">
        @livewire('point-of-sales.list-products')

        <div class=" bg-white shadow-xl rounded-2xl p-6 col-auto">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">
                    Your Cart
                </h2>
                <span class="text-sm text-gray-500">{{ count($this->carts) }} items</span>
            </div>

            <!-- Cart Items -->
            <div class="space-y-4">
                <!-- Item -->
                @foreach ($this->carts as $cart)
                    <div class="flex items-center gap-4">
                        <img src="{{ $cart->model->image }}" alt="Product" class="w-16 h-16 rounded-xl object-cover">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800">{{ $cart->model->name }}</h3>
                            <p class="text-sm text-gray-500">Qty: {{ $cart->qty }}</p>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-gray-800">{{ $cart->price->value }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Total -->
            <div class="flex items-center justify-between border-t pt-4">
                <span class="text-lg font-semibold text-gray-700">Total:</span>
                <span class="text-xl font-bold text-blue-600"></span>
            </div>

            <!-- Checkout Button -->
            <div class="flex-row">
                <button
                    class=" bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition duration-300">
                    Proceed to Checkout
                </button>
            </div>
        </div>


    </div>

</x-filament-panels::page>
