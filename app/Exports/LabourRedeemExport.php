<?php

namespace App\Exports;

use App\Models\Booking;
use App\Models\LabourRedeem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LabourRedeemExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public $type;
    public function __construct($type)
    {
        $this->type = $type;
    }
    public function collection()
    {
        return LabourRedeem::with(["labour"])->where("payment_status", $this->type)->Active()->get()->map(function ($booking) {
            return [
                'Name' => $booking->labour->name ?? "",
                'Amount' => $booking->amount,
                'Payment Status' => $booking->payment_status,
                'Accept Payment' => $booking->payment_status,

            ];

        });

    }

    public function headings(): array
    {
        return [
            "Name",
            "Amount",
            "Payment Status",
            "Accept Payment",

        ];
    }
}
