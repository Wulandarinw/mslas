<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomerExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Customer::all();
    }

    public function headings(): array
    {
        return [
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Gender',
            'Date of Birth',
            'Created At',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->FName,
            $customer->LName,
            $customer->email,
            $customer->phone,
            $customer->gender,
            $customer->date_of_birth,
            $customer->created_at->format('Y-m-d H:i:s'),
        ];
    }
}