<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $state, $city;
    
    public function __construct($start_date,$end_date){
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }
    
    public function collection()
    {
     return User::with('states', 'cities')->where("type","user")->
     whereBetween("created_at",[$this->start_date,$this->end_date])->
     get()->map(function($user_detail) {
            return [
                'Name' => $user_detail->name,
                'Email' => $user_detail->email,
                'Phone' => $user_detail->phone,
                'Lat/Long' => $user_detail->lat_long,
               
            ];
      
    });

    }
    
     public function headings():array{
        return [
            "Name",
            "Email",
            "Phone",
            "Lat_Long",
        
        ];
    }
}
