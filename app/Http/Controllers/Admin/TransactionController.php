<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TransactionExport;
use App\Http\Controllers\Controller;
use Excel;

class TransactionController extends Controller
{
    public function index()
    {
        return view('content.tables.transactions');
    }

    public function export()
    {
        return Excel::download(new TransactionExport, 'transactionexport.xlsx');

    }
}
