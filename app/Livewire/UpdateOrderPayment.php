<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Order;

class UpdateOrderPayment extends Component
{
    public $orderCode;

    public function updatePaymentStatus()
    {
        $order = Order::where('order_code', $this->orderCode)->first();

        if ($order) {
            $order->payment_status = 'paid';
            $order->payment_date = now(); // Set payment date to now
            $order->save();

            // You can also emit an event or display a success message here
            session()->flash('message', 'Payment status updated successfully.');
        } else {
            session()->flash('error', 'Order not found.');
        }
    }

}
