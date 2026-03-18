<?php
namespace App\Http\Controllers\reports;
use App\Models\inward;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;




class Outstanding_ReportController extends Controller
{
    public function index()
    {
        return view('admin.Outstanding_Report.index');
    }

    // public function generateReport(InwardReportsDataTable $dataTable, Request $request)
    // {
    //     $request->validate([
    //         'from_date' => 'nullable|date',
    //         'to_date' => 'nullable|date|after_or_equal:from_date',
    //     ]);

    //     return $dataTable->with([
    //         'from_date' => $request->input('from_date'),
    //         'to_date' => $request->input('to_date'),
    //     ])->render('admin.reports.index');
    // }

    // public function getData(InwardReportsDataTable $dataTable)
    // {
    //     return $dataTable->ajax();
    // }
}

