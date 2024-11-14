<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Product Detail - ESLAS')]
class ProductDetailPage extends Component
{
    use LivewireAlert;

    public $name;
    public $quantity = 1;
    public $selectedVariationId;

    public function mount($name)
    {
        $this->name = $name;
    }
    
    public function increaseQty()
    {
        $this->quantity++;
    }

    public function decreaseQty()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }
    
    public function addToCart()
    {
        if (!$this->selectedVariationId) {
            $this->alert('error', 'Please select a product variation before adding to cart.', [
                'position' => 'button-end',
                'timer' => 3000,
                'toast' => true,
            ]);
            return;
        }

        $itemCount = CartManagement::addItemToCart($this->selectedVariationId, $this->quantity);

        $this->dispatch('update-cart-count')->to(Navbar::class);

        session()->flash('alert', [
            'type' => 'success',
            'message' => 'Product added to the cart successfully!',
            'position' => 'button-end',
            'timer' => 3000,
            'toast' => true,
        ]);
        
        return redirect('/');
    }

    public function render()
    {
        $product = Product::where('name', $this->name)->with('variations')->firstOrFail();
        return view('livewire.product-detail-page', [
            'product' => $product,
        ]);
    }
}