<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoryExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function collection()
    {
        return Category::Active()->get()->map(function ($catgeory) {
            return [
                'Title' => $catgeory->title,
                'Percentage For Less Than' => $catgeory->percentage_for_less_than,
                'Percentage For More Than' => $catgeory->percentage_for_more_than,

            ];

        });

    }

    public function headings(): array
    {
        return [
            "Title",
            "Percentage For Less Than",
            "Percentage For More Than",

        ];
    }
}
