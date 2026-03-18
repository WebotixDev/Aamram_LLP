<?php

namespace App\Http\Controllers\inward;

use App\Models\farm_inward;
use App\Models\farm_inward_details;
use App\Models\Product;
use App\Models\Product_details;
use Illuminate\Http\Request;
use App\DataTables\Farm_ReportDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\farm_inwardRepository;
use Illuminate\Support\Facades\DB;


class Farm_inwardReportcontroller extends Controller
{
    /**
     * Display the sale order report.
     *
     * @param Farm_ReportDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(Farm_ReportDataTable $dataTable)
    {
        // You can pass additional data to the view if necessary
        return $dataTable->render('admin.Farm_Report.index');
    }

    /**
     * Handle filtering for the sale order report.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function generateReports(Farm_ReportDataTable $dataTable, Request $request)
    {
        // Validate the request for dates


        // Return the filtered dataTable
        return $dataTable->with([
            'from_date' => $request->input('from_date'),
            'to_date' => $request->input('to_date'),
        ])->render('admin.Farm_Report.index');
    }

    public function getData(Farm_ReportDataTable $dataTable)
    {
        return $dataTable->ajax();
    }
}

