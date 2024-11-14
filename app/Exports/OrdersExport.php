<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Order::with('customerAddress.customer', 'orderItems.variation')->get();
    }

    public function headings(): array
    {
        return [
            'Order Code',
            'Customer Address',
            'Payment Name',
            'Shipment Name',
            'Order Date',
            'Shopping Cost',
            'Payment Status',
            'Payment Date',
            'Shipment Status',
            'Total Amount',
            'Items',
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_code,
            $order->customerAddress->address ?? 'N/A',
            $order->payment_name,
            $order->shipment_name,
            $order->order_date,
            $order->shopping_cost,
            $order->payment_status,
            $order->payment_date,
            $order->shipment_status,
            $order->total_amount,
            // Concatenate order items and their quantities
            $order->orderItems->map(function ($item) {
                return $item->variation->name . ' (Qty: ' . $item->qty . ')';
            })->join(', '),
        ];
    }
}
