<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Category;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;


#[Title('Products - OTTERU')]
class ProdutcsPage extends Component
{
    use LivewireAlert;
    use WithPagination;
   
    #[Url()]
    public $selected_categories = [];

    #[Url]
    public $search = '';

    #[Url]
    public $search_by = 'name';

    #[Url]
    public $sort = 'latest';

    #[Url]
    public $price_min = '';

    #[Url]
    public $price_max = '';

    #[Url]
    public $weight_min = '';

    #[Url]
    public $weight_max = '';

    #[Url]
    public $status = 'Publish';

    #[Url]
    public $dimension = '';

    public $showFilters = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'search_by' => ['except' => 'name'],
        'sort' => ['except' => 'latest'],
        'selected_categories' => ['except' => []],
        'price_min' => ['except' => ''],
        'price_max' => ['except' => ''],
        'weight_min' => ['except' => ''],
        'weight_max' => ['except' => ''],
        'status' => ['except' => 'Publish'],
        'dimension' => ['except' => '']
    ];

    public function mount()
    {
        // Get search query from URL if coming from navbar
        $urlSearch = request()->query('search');
        if ($urlSearch) {
            $this->search = $urlSearch;
        }
    }


    public function updating($name)
    {
        if (in_array($name, [
            'search',
            'selected_categories',
            'sort',
            'search_by',
            'price_min',
            'price_max',
            'weight_min',
            'weight_max',
            'status',
            'dimension'
        ])) {
            $this->resetPage();
        }
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function resetFilters()
    {
        $this->reset([
            'selected_categories',
            'sort',
            'search',
            'search_by',
            'price_min',
            'price_max',
            'weight_min',
            'weight_max',
            'status',
            'dimension'
        ]);
        $this->resetPage();
    }

    public function addToCart($product_id)
    {
        $total_count = CartManagement::addItemToCart($product_id);
        $this->dispatch('update-cart-count', total_count: $total_count)->to(Navbar::class);
        $this->alert('success', 'Product added to the cart successfully!', [
            'position' => 'button-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    public function render()
    {
        $query = Product::query()
            ->with(['variations', 'category', 'shop']);

        // Apply status filter (default to Published)
        $query->where('status', $this->status);

        // Apply search based on search_by selection
        if (trim($this->search) !== '') {
            $searchTerm = '%' . trim($this->search) . '%';
            
            switch ($this->search_by) {
                case 'name':
                    $query->where('name', 'like', $searchTerm);
                    break;
                case 'description':
                    $query->where('desc', 'like', $searchTerm);
                    break;
                case 'dimension':
                    $query->where('dimension', 'like', $searchTerm);
                    break;
                case 'category':
                    $query->whereHas('category', function($q) use ($searchTerm) {
                        $q->where('name', 'like', $searchTerm);
                    });
                    break;
                case 'all':
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('name', 'like', $searchTerm)
                          ->orWhere('desc', 'like', $searchTerm)
                          ->orWhere('dimension', 'like', $searchTerm)
                          ->orWhereHas('category', function($sq) use ($searchTerm) {
                              $sq->where('name', 'like', $searchTerm);
                          });
                    });
                    break;
            }
        }

        // Apply category filter
        if (!empty($this->selected_categories)) {
            $query->whereIn('category_code', $this->selected_categories);
        }

        // Apply price range filter
        if ($this->price_min !== '') {
            $query->whereHas('variations', function($q) {
                $q->where('price', '>=', $this->price_min);
            });
        }
        if ($this->price_max !== '') {
            $query->whereHas('variations', function($q) {
                $q->where('price', '<=', $this->price_max);
            });
        }

        // Apply weight filter
        if ($this->weight_min !== '') {
            $query->where('weight', '>=', $this->weight_min);
        }
        if ($this->weight_max !== '') {
            $query->where('weight', '<=', $this->weight_max);
        }

        // Apply dimension filter
        if ($this->dimension !== '') {
            $query->where('dimension', 'like', '%' . $this->dimension . '%');
        }

        // Apply sorting
        switch ($this->sort) {
            case 'latest':
                $query->latest();
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy(function($q) {
                    $q->select('price')
                      ->from('product_variations')
                      ->whereColumn('product_variations.product_id', 'products.product_id')
                      ->orderBy('price', 'asc')
                      ->limit(1);
                });
                break;
            case 'price_desc':
                $query->orderBy(function($q) {
                    $q->select('price')
                      ->from('product_variations')
                      ->whereColumn('product_variations.product_id', 'products.product_id')
                      ->orderBy('price', 'desc')
                      ->limit(1);
                });
                break;
            case 'weight_asc':
                $query->orderBy('weight', 'asc');
                break;
            case 'weight_desc':
                $query->orderBy('weight', 'desc');
                break;
        }

        return view('livewire.produtcs-page', [
            'products' => $query->paginate(9),
            'categories' => Category::get(['category_code', 'name'])
        ]);
    }
}