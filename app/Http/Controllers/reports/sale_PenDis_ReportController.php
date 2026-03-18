<?php

namespace App\Http\Controllers\reports;

use App\DataTables\sale_PenDis_ReportDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class sale_PenDis_ReportController extends Controller
{
    /**
     * Display the sale order report.
     *
     * @param sale_PenDis_ReportDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(sale_PenDis_ReportDataTable $dataTable)
    {
        // You can pass additional data to the view if necessary
        return $dataTable->render('admin.sale_PenDis_Report.index');
    }
  
    /**
     * Handle filtering for the sale order report.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function generateReports(sale_PenDis_ReportDataTable $dataTable, Request $request)
    {
        // Validate the request for dates
        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',  // Ensure to_date is after from_date
        ]);

        // Return the filtered dataTable
        return $dataTable->with([
            'from_date' => $request->input('from_date'),
            'to_date' => $request->input('to_date'),
        ])->render('admin.sale_PenDis_Report.index');
    }

    public function getData(sale_PenDis_ReportDataTable $dataTable)
    {
        return $dataTable->ajax();
    }
}

