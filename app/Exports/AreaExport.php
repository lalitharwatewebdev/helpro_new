<?php

namespace App\Exports;

use App\Models\Areas;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AreaExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function collection()
    {
        return Areas::with("category")->Active()->get()->map(function ($area) {
            return [
                'Latitude' => $area->latitude,
                'Longtitude' => $area->longitude,
                'Radius' => $area->radius,
                'Price' => $area->price,
                'Area Name' => $area->area_name,
                'Category' => $area->category->title,

            ];

        });

    }

    public function headings(): array
    {
        return [
            "Latitude",
            "Longtitude",
            "Radius",
            "Price",
            "Area Name",
            "Category",

        ];
    }
}
