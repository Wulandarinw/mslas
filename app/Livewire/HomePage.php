<?php

namespace App\Livewire;

use App\Models\category;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Component;


#[Title('Home Page - OTTERU')]

class HomePage extends Component{
    public function render(){
        $categories = Category::all();
        $products = Product::where('status', 'Publish')->get();

        return view('livewire.home-page', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
