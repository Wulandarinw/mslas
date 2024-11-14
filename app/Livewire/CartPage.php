<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Product;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Cart - OTTERU')]
class CartPage extends Component
{
    public $cartItems = [];
    public $subtotal = 0;
    public $itemCount = 0;
    public $selectedItems = [];
    public $selectAll = false;

    public function mount()
    {
        $this->updateCart();
    }

    public function updated($name)
    {
        if ($name === 'selectAll') {
            $this->selectAllItems();
        }
    }

    public function selectAllItems()
    {
        if ($this->selectAll) {
            $this->selectedItems = array_keys($this->cartItems);
        } else {
            $this->selectedItems = [];
        }
    }

    public function updatedSelectedItems()
    {
        // Update selectAll checkbox state based on selected items
        $this->selectAll = !empty($this->cartItems) && 
                          count($this->selectedItems) === count($this->cartItems);
    }

    public function proceedToCheckout()
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Please select at least one item to checkout'
            ]);
            return;
        }

        $selectedProducts = [];
        foreach ($this->selectedItems as $variationId) {
            if (isset($this->cartItems[$variationId])) {
                $cartItem = $this->cartItems[$variationId];
                $product = Product::find($cartItem['product_id']);
                $cartItem['product'] = $product;
                $selectedProducts[$variationId] = $cartItem;
            }
        }

        Session::put('checkout_items', $selectedProducts);
        Session::put('checkout_subtotal', $this->getSelectedSubtotal());

        return redirect()->to('/checkout');
    }

    public function removeItem($variationId)
    {
        CartManagement::removeCartItem($variationId);
        $this->selectedItems = array_values(array_diff($this->selectedItems, [$variationId]));
        $this->updateCart();
        $this->updatedSelectedItems(); // Update selectAll state
        $this->dispatch('update-cart-count')->to(Navbar::class);
    }

    public function updateQuantity($variationId, $quantity)
    {
        if ($quantity < 1) {
            return;
        }
        CartManagement::updateQuantity($variationId, $quantity);
        $this->updateCart();
    }

    public function removeSelected()
    {
        if (empty($this->selectedItems)) {
            return;
        }

        foreach ($this->selectedItems as $variationId) {
            CartManagement::removeCartItem($variationId);
        }
        
        $this->selectedItems = [];
        $this->selectAll = false;
        $this->updateCart();
        $this->dispatch('update-cart-count')->to(Navbar::class);
    }

    private function updateCart()
    {
        $cartData = CartManagement::calculateTotals();
        $this->cartItems = $cartData['items'];
        $this->subtotal = $cartData['subtotal'];
        $this->itemCount = $cartData['item_count'];

        // Cleanup selected items that no longer exist in cart
        $this->selectedItems = array_values(
            array_intersect($this->selectedItems, array_keys($this->cartItems))
        );
    }

    public function getSelectedSubtotal()
    {
        $total = 0;
        foreach ($this->selectedItems as $variationId) {
            if (isset($this->cartItems[$variationId])) {
                $item = $this->cartItems[$variationId];
                $total += $item['quantity'] * $item['price'];
            }
        }
        return $total;
    }

    public function getSelectedQuantityTotal()
    {
        $totalQty = 0;
        foreach ($this->selectedItems as $variationId) {
            if (isset($this->cartItems[$variationId])) {
                $totalQty += $this->cartItems[$variationId]['quantity'];
            }
        }
        return $totalQty;
    }

    public function render()
    {
        return view('livewire.cart-page');
    }
}