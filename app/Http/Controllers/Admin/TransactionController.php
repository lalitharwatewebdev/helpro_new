<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    public function index()
    {
        return view('content.tables.transactions');
    }

    public function export()
    {
        return Excel::download(new AreaExport, 'areaexport.xlsx');

    }
}
