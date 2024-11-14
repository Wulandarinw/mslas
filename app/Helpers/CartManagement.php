<?php

namespace App\Helpers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\Auth;

class CartManagement
{
    public static function addItemToCart($variation_id, $quantity = 1)
    {
        $customer_id = Auth::id();
        
        // Get or create cart for customer
        $cart = Cart::firstOrCreate(['customer_id' => $customer_id]);
        
        // Check if item already exists in cart
        $cartItem = CartItem::where([
            'cart_id' => $cart->cart_id,
            'variation_id' => $variation_id
        ])->first();
        
        if ($cartItem) {
            // Update existing cart item quantity
            $cartItem->qty += $quantity;
            $cartItem->save();
        } else {
            // Create new cart item
            CartItem::create([
                'cart_id' => $cart->cart_id,
                'variation_id' => $variation_id,
                'qty' => $quantity
            ]);
        }
        
        return self::getCartItemCount($cart->cart_id);
    }

    public static function removeCartItem($variation_id)
    {
        $cart = self::getCurrentCart();
        if (!$cart) return [];

        CartItem::where([
            'cart_id' => $cart->cart_id,
            'variation_id' => $variation_id
        ])->delete();

        return self::getCartItems();
    }

    public static function updateQuantity($variation_id, $quantity)
    {
        $cart = self::getCurrentCart();
        if (!$cart) return [];

        $cartItem = CartItem::where([
            'cart_id' => $cart->cart_id,
            'variation_id' => $variation_id
        ])->first();

        if ($cartItem) {
            $cartItem->qty = max(1, $quantity);
            $cartItem->save();
        }

        return self::getCartItems();
    }

    public static function clearCart()
    {
        $cart = self::getCurrentCart();
        if ($cart) {
            CartItem::where('cart_id', $cart->cart_id)->delete();
        }
    }

    public static function getCartItems()
    {
        $cart = self::getCurrentCart();
        if (!$cart) return [];

        $items = [];
        $cartItems = CartItem::with(['variation.product'])->where('cart_id', $cart->cart_id)->get();

        foreach ($cartItems as $item) {
            $variation = $item->variation;
            $items[$variation->variation_id] = [
                'product_id' => $variation->product_id,
                'variation_id' => $variation->variation_id,
                'name' => $variation->product->name,
                'color' => $variation->color,
                'material' => $variation->material,
                'image' => $variation->images[0] ?? null,
                'quantity' => $item->qty,
                'price' => $variation->price,
            ];
        }

        return $items;
    }

    public static function calculateTotals()
    {
        $cartItems = self::getCartItems();
        $subtotal = 0;
        $itemCount = 0;

        foreach ($cartItems as &$item) {
            $item['total'] = $item['quantity'] * $item['price'];
            $subtotal += $item['total'];
            $itemCount += $item['quantity'];
        }

        return [
            'items' => $cartItems,
            'subtotal' => $subtotal,
            'item_count' => $itemCount,
        ];
    }

    private static function getCurrentCart()
    {
        $customer_id = Auth::id();
        return Cart::where('customer_id', $customer_id)->first();
    }

    private static function getCartItemCount($cart_id)
    {
        return CartItem::where('cart_id', $cart_id)->count();
    }
}