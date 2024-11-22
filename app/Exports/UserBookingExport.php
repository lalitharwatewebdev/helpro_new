<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserBookingExport implements FromCollection, WithHeadings
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
        return Booking::with(["checkout", "labour"])->where("booking_status", $this->type)->Active()->get()->map(function ($booking) {
            return [
                'User' => $booking->user->name ?? "",
                'Total Amount' => $booking->total_amount,
                'Quantity' => $booking->quantity_required,
                'Start Date' => $booking->checkout->start_date,
                'End Date' => $booking->checkout->end_date,
                'Start Time' => $booking->checkout->start_time,
                'End Time' => $booking->checkout->end_time,
                'Address' => $booking->checkout->address->address,
                'State' => $booking->checkout->address->states->name,
                'City' => $booking->checkout->address->cities->name,
                'Notes' => $booking->checkout->note,

            ];

        });

    }

    public function headings(): array
    {
        return [
            "Title",
            "Total Amount",
            "Quantity",
            "Start Date",
            "End Date",
            "Start Time",
            "End Time",
            "Address",
            "State",
            "City",
            "Notes",

        ];
    }
}
