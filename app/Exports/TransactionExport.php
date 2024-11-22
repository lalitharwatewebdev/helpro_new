<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function collection()
    {
        return Booking::with(["user", "labour", "checkout"])->Active()->get()->map(function ($transaction) {
            return [
                'User' => $transaction->user->name,
                'Labour' => $transaction->labour->name ?? '',
                'Total Amount' => $transaction->total_amount,
                'OTP' => $transaction->otp,
                'Start Date' => $transaction->checkout->start_date,
                'End Date' => $transaction->checkout->end_date,
                'Start Time' => $transaction->checkout->start_time,
                'End Time' => $transaction->checkout->end_time,
                'Address' => $transaction->checkout->address->address,
                'State' => $transaction->checkout->address->states->name,
                'City' => $transaction->checkout->address->cities->name,

            ];

        });

    }

    public function headings(): array
    {
        return [
            "User",
            "Labour",
            "Total Amount",
            "OTP",
            "Start Date",
            "End Date",
            "Start Time",
            "End Time",
            "Address",
            "State",
            "City",

        ];
    }
}
