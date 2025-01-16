<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFController extends Controller
{
    //generate pdf
    public function generatePDF()
    {
        $users = User::get();
        $transactions = Invoice::orderBy('created_at', 'desc')->get();
        $data = [
            'title' => 'TRANSACTIONS',
            'date' => date('m/d/Y'),
            'users' => $users,
            'transactions' => $transactions,
        ];

        $pdf = PDF::loadView('admin.report', $data);
        return $pdf->download('transaction-list.pdf');
    }
}
