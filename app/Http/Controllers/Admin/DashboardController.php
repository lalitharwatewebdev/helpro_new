<?php

namespace App\Http\Controllers\Admin;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{

    public function home()
    {
        return view('content.dashboard');
    }


    public function downloadPdf()
    {
        $data = [
            [
                'quantity' => 1,
                'description' => '1 Year Subscription',
                'price' => '129.00'
            ]
        ];
        // return view('content.pdf.invoice', compact('data'));
        $pdf = Pdf::loadView('content.pdf.invoice', ['data' => $data]);
        // return $pdf->download('test.pdf');
        return $pdf->stream('test.pdf');
    }
}
