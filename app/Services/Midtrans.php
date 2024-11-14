<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Order;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createTransaction(Order $order)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order->order_code,
                'gross_amount' => round($order->total_amount),
            ],
            'customer_details' => $this->getCustomerDetails($order),
            'item_details' => $this->getItemDetails($order),
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            throw new \Exception('Failed to create Midtrans transaction: ' . $e->getMessage());
        }
    }

    private function getCustomerDetails(Order $order)
    {
        $customerAddress = $order->customerAddress;
        
        return [
            'first_name' => $customerAddress->customer->FName,
            'last_name' => $customerAddress->customer->LName,
            'email' => $customerAddress->customer->email,
            'phone' => $customerAddress->phone,
            'shipping_address' => [
                'first_name' => $customerAddress->customer->FName,
                'last_name' => $customerAddress->customer->LName,
                'email' => $customerAddress->customer->email,
                'phone' => $customerAddress->phone,
                'address' => $customerAddress->address_id,
                'country_code' => 'IDN'
            ],
        ];
    }

    private function getItemDetails(Order $order)
    {
        $items = [];
        
        foreach ($order->orderItems as $item) {
            $variation = $item->variation;
            $product = $variation->product;
            
            $items[] = [
                'id' => $variation->variation_id,
                'price' => round($variation->price),
                'quantity' => $item->qty,
                'name' => $product->name . ' - ' . $variation->color . ' (' . $variation->material . ')'
            ];
        }

        // Add shipping cost as an item
        if ($order->shopping_cost > 0) {
            $items[] = [
                'id' => 'SHIPPING',
                'price' => round($order->shopping_cost),
                'quantity' => 1,
                'name' => 'Shipping Cost (' . $order->shipment_name . ')'
            ];
        }

        return $items;
    }

    public function updateOrderPayment(Order $order, $transactionStatus)
    {
        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                $order->payment_status = 'paid';
                $order->payment_date = now();
                break;
            case 'pending':
                $order->payment_status = 'pending';
                break;
            case 'deny':
            case 'expire':
            case 'cancel':
                $order->payment_status = 'failed';
                break;
        }
        
        $order->save();
    }
}