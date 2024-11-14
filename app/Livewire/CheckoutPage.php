<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariation;
use Faker\Extension\CountryExtension;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CheckoutPage extends Component
{
    public $provinces = [];
    public $cities = [];
    public $selectedProvince = '';
    public $selectedCity = '';
    public $weight;
    public $courierType = '';
    public $shippingCosts = [];
    public $selectedService = '';
    public $shippingCost = 0;

    // Cart data
    public $checkoutItems = [];
    public $subtotal = 0;

    // Shipping data
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $address_search;
    public $selected_address_id;
    public $address_results = [];

    // Constants
    const RAJAONGKIR_API_KEY = '99b9816f8e35c0752539d06949c0556e';
    const ORIGIN_CITY = '501'; // ID kota asal (Jakarta Pusat)

    protected $listeners = ['citySelected' => 'onCitySelected'];

    public function processCheckout()
    {
        try {
            DB::beginTransaction();

            try {
                $customerAddress = Auth::user()->customers->customerAddress;
                $orderCode = 'ORD/' . date('Ymd') . '/' . Str::random(6);

                \Log::info('Starting checkout process', ['order_code' => $orderCode]);

                $order = Order::create([
                    'order_code' => $orderCode,
                    'customer_address_id' => $customerAddress->first()->customer_address_id,
                    'payment_name' => 'midtrans',
                    'shipment_name' => $this->courierType,
                    'order_date' => date('Ymd'),
                    'shopping_cost' => $this->shippingCost,
                    'payment_status' => 'unpaid',
                    'total_amount' => $this->grandTotal,
                ]);

                \Log::info('Order created', ['order' => $order->toArray()]);

                foreach ($this->checkoutItems as $item) {
                    $product = ProductVariation::find($item['variation_id']);

                    if (!$product) {
                        throw new \Exception("Product variation with ID {$item['variation_id']} not found");
                    }

                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Insufficient stock for product {$product->name}");
                    }

                    $orderItem = OrderItem::create([
                        'order_code' => $order->order_code,
                        'variation_id' => $item['variation_id'],
                        'qty' => $item['quantity']
                    ]);

                    $product->decrement('stock', $item['quantity']);
                }

                Session::forget(['checkout_items', 'checkout_subtotal']);

                // Configure Midtrans
                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = false;
                \Midtrans\Config::$isSanitized = true;
                \Midtrans\Config::$is3ds = true;

                $params = array(
                    'transaction_details' => array(
                        'order_id' => $order->order_code,
                        'gross_amount' => (int) $order->total_amount,
                    ),
                    'customer_details' => array(
                        'first_name' => Auth::user()->customers->FName,
                        'last_name' => Auth::user()->customers->LName,
                        'email' => Auth::user()->customers->email,
                        'phone' => Auth::user()->customers->phone,
                    ),
                );

                $snapToken = \Midtrans\Snap::getSnapToken($params);

                DB::commit();

                // Dispatch event dengan data yang diperlukan
                $this->dispatch('showPayment', [
                    'snapToken' => $snapToken,
                    'orderCode' => $order->order_code
                ]);

                return;
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error in transaction', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to process your order: ' . $e->getMessage());
            \Log::error('Checkout Error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'cart_items' => $this->checkoutItems,
                'error' => $e
            ]);
            return null;
        }
    }

    private function getEstimatedDays()
    {
        if (!$this->selectedService || empty($this->shippingCosts)) {
            return null;
        }

        foreach ($this->shippingCosts as $cost) {
            if ($cost['service'] === $this->selectedService) {
                return $cost['cost'][0]['etd'] ?? null;
            }
        }

        return null;
    }

    public function mount()
    {
        // Get selected items from session
        $this->checkoutItems = Session::get('checkout_items', []);
        $this->subtotal = $this->calculateSubtotal();

        // Redirect if no items
        if (empty($this->checkoutItems)) {
            session()->flash('error', 'Please select items from cart first');
            return redirect()->to('/cart');
        }

        $this->loadProvinces();
        $this->calculateTotalWeight();
    }

    private function calculateSubtotal()
    {
        return collect($this->checkoutItems)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function loadProvinces()
    {
        try {
            $response = Http::withHeaders([
                'key' => self::RAJAONGKIR_API_KEY
            ])->get('https://api.rajaongkir.com/starter/province');

            if ($response->successful()) {
                $this->provinces = $response->json()['rajaongkir']['results'];
            } else {
                session()->flash('error', 'Failed to load provinces. Please try again.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to connect to shipping service.');
        }
    }

    public function updatedSelectedProvince($value)
    {
        $this->cities = [];
        $this->selectedCity = '';
        $this->resetShipping();

        if ($value) {
            try {
                $response = Http::withHeaders([
                    'key' => self::RAJAONGKIR_API_KEY
                ])->get('https://api.rajaongkir.com/starter/city', [
                    'province' => $value
                ]);

                if ($response->successful()) {
                    $this->cities = $response->json()['rajaongkir']['results'];
                } else {
                    session()->flash('error', 'Failed to load cities. Please try again.');
                }
            } catch (\Exception $e) {
                session()->flash('error', 'Failed to connect to shipping service.');
            }
        }
    }

    private function resetShipping()
    {
        $this->courierType = '';
        $this->shippingCosts = [];
        $this->selectedService = '';
        $this->shippingCost = 0;
    }

    public function loadCities($provinceId)
    {
        try {
            $response = Http::withHeaders([
                'key' => self::RAJAONGKIR_API_KEY
            ])->get('https://api.rajaongkir.com/starter/city', [
                'province' => $provinceId
            ]);

            if ($response->successful()) {
                $this->cities = $response->json()['rajaongkir']['results'];
            } else {
                session()->flash('error', 'Failed to load cities. Please try again.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to connect to shipping service.');
        }
    }

    private function calculateTotalWeight()
    {
        $this->weight = collect($this->checkoutItems)->sum(function ($item) {
            return $item['product']['weight'] * $item['quantity'];
        });
    }

    public function onCitySelected($cityId)
    {
        $this->selectedCity = $cityId;
        $this->resetShipping();
    }

    public function calculateShipping()
    {
        if (!$this->selectedCity || !$this->weight || !$this->courierType) {
            session()->flash('error', 'Please fill in all shipping information');
            return;
        }

        try {
            $response = Http::withHeaders([
                'key' => self::RAJAONGKIR_API_KEY
            ])->post('https://api.rajaongkir.com/starter/cost', [
                'origin' => self::ORIGIN_CITY,
                'destination' => $this->selectedCity,
                'weight' => $this->weight,
                'courier' => $this->courierType
            ]);

            if ($response->successful()) {
                $this->shippingCosts = $response->json()['rajaongkir']['results'][0]['costs'];
                $this->selectedService = '';
                $this->shippingCost = 0;
            } else {
                session()->flash('error', 'Failed to fetch shipping costs. Please try again.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to connect to shipping service.');
        }
    }

    public function updateShippingCost()
    {
        if ($this->selectedService) {
            foreach ($this->shippingCosts as $cost) {
                if ($cost['service'] === $this->selectedService) {
                    $this->shippingCost = $cost['cost'][0]['value'];
                    break;
                }
            }
        }
    }

    public function getGrandTotalProperty()
    {
        return $this->subtotal + $this->shippingCost + 2000; // 2000 is application fee
    }

    // public function processCheckout()
    // {
    //     $this->validate([
    //         'first_name' => 'required|min:3',
    //         'last_name' => 'required|min:3',
    //         'email' => 'required|email',
    //         'phone' => 'required|min:10',
    //         'address' => 'required|min:10',
    //         'selectedProvince' => 'required',
    //         'selectedCity' => 'required',
    //         'courierType' => 'required',
    //         'selectedService' => 'required',
    //         'state' => 'required',
    //         'zip_code' => 'required'
    //     ]);

    //     // Here you would typically:
    //     // 1. Create order in database
    //     // 2. Clear cart
    //     // 3. Redirect to payment page or show success message

    //     Session::forget(['checkout_items', 'checkout_subtotal']);
    //     session()->flash('success', 'Order placed successfully!');
    //     return redirect()->to('/orders');
    // }
    public function render()
    {
        return view('livewire.checkout-page');
    }
}
