<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class ProductPage extends Component
{
    public $products;
    public $showPopup = false;
    public $popupProduct;
    public $randomizedPrice;

    public function mount()
    {
        $this->loadProducts();
    }

    public function loadProducts()
    {
        $this->products = Product::all();
    }

    public function order($productId)
    {
        $product = Product::findOrFail($productId);

        // Tambah order count
        $product->order += 1;
        $product->save();

        $this->popupProduct = $product;

        // Generate harga acak: misalnya 123456 jadi 123000 + random(100 - 999)
        $base = floor($product->price / 1000) * 1000;
        $random = rand(100, 999);
        $this->randomizedPrice = $base + $random;

        $this->showPopup = true;
    }


    public function render()
    {
        return view('livewire.product-page');
    }
}

