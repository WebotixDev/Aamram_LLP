<?php
namespace App\Http\Controllers\reports;
use App\Models\inward;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;




class LedgerReportController extends Controller
{
    public function index()
    {
        return view('admin.ledger_report.index');
    }


}

