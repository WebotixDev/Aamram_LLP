<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\DataTables\OutwardReportDaTable;
use Illuminate\Http\Request;

class OutwardReportController extends Controller
{
    /**
     * Display the outward report data table.
     *
     * @param OutwardReportDaTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(OutwardReportDaTable $dataTable)
    {
        // Return the view with the DataTable
        return $dataTable->render('admin.OutwardReport.Index');
    }

    /**
     * Generate the outward report with applied filters.
     *
     * @param OutwardReportDaTable $dataTable
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generateReports(OutwardReportDaTable $dataTable, Request $request)
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
        ])->render('admin.OutwardReport.Index');
    }
}
