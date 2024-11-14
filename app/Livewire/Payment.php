<?php

namespace App\Livewire;

use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Request;
use Livewire\Component;

class Payment extends Component
{
    private $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function show(Order $order)
    {
        if (empty($order->snap_token)) {
            $snapToken = $this->midtransService->createTransaction($order);
            $order->update(['snap_token' => $snapToken]);
        }

        return view('payment.show', compact('order'));
    }

    public function notification(Request $request)
    {
        $notification = json_decode($request->getContent(), true);

        $order = Order::where('order_code', $notification['order_id'])->firstOrFail();

        $this->midtransService->updateOrderPayment(
            $order,
            $notification['transaction_status']
        );

        return response()->json(['status' => 'success']);
    }

    public function success($orderNumber)
    {
        $order = Order::where('order_code', $orderNumber)->firstOrFail();
        return view('payment.success', compact('order'));
    }

    public function pending($orderNumber)
    {
        $order = Order::where('order_code', $orderNumber)->firstOrFail();
        return view('payment.pending', compact('order'));
    }

    public function error($orderNumber)
    {
        $order = Order::where('order_code', $orderNumber)->firstOrFail();
        return view('payment.error', compact('order'));
    }

    public function cancel($orderNumber)
    {
        $order = Order::where('order_code', $orderNumber)->firstOrFail();
        return view('payment.cancel', compact('order'));
    }
    public function render(Order $order)
    {
        if (empty($order->snap_token)) {
            $snapToken = $this->midtransService->createTransaction($order);
            $order->update(['snap_token' => $snapToken]);
        }

        return view('livewire.payment', compact('order'));
    }
}
