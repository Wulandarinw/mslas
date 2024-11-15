<?php
namespace App\Livewire\Partials;

use App\Helpers\CartManagement;
use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class Navbar extends Component
{
    public $totalCount = 0;
    
    #[Url]
    public $search = '';

    #[Url]
    public $selected_categories = [];

    public function mount()
    {
        $this->updateCartCount();
    }

    #[On('update-cart-count')]
    public function updateCartCount()
    {
        $cartTotals = CartManagement::calculateTotals();
        $this->totalCount = $cartTotals['item_count'];
    }

    public function performSearch()
    {
        // Only redirect if search is not empty
        if (trim($this->search) !== '') {
            $this->redirect('/products?search=' . urlencode($this->search));
        }
    }

    public function render()
    {
        $categories = Category::get(['category_code', 'name']);

        return view('livewire.partials.navbar', [
            'categories' => $categories,
        ]);
    }
}