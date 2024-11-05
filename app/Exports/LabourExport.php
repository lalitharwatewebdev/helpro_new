<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LabourExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($start_date,$end_date,$type){
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->type = $type;
    }
    
    public function collection()
    {
        return User::with("states","cities")->where("type","labour")->whereBetween("created_at",[$this->start_date,$this->end_date])
        ->where("labour_status",$this->type)
        ->get()->map(function($user){
           return [
                "Name" => $user->name,
                "Email" => $user->email,
                "Phone" => $user->phone,
                "State" => $user->states ? $user->states->name : null, 
                "City" => $user->cities ? $user->cities->name : null,
                "Aadhaar_number" => $user->aadhaar_number,
                "pan_card_number" => $user->pan_card_number,
                "bank_name" => $user->bank_name,
                "IFSC_code" => $user->IFSC_code,
                "address" => $user->address,
                "lat_long" => $user->lat_long,
                "qualification" => $user->qualification,
                "availability" => $user->availability
               ]; 
        });
    }
    
    public function headings():array{
        return [
            "Name" ,
                "Email",
                "Phone" ,
                "State" ,
                "City",
                "Aadhaar_number" ,
                "pan_card_number",
                "bank_name",
                "IFSC_code",
                "address" ,
                "lat_long" ,
                "qualification",
                "availability",
        
        ];
    }
}
