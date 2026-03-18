<?php

namespace App\Http\Controllers;

use App\DataTables\Sale_orderReportDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Sale_orderReportController extends Controller
{
    /**
     * Display the sale order report.
     *
     * @param Sale_orderReportDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(Sale_orderReportDataTable $dataTable)
    {
        // You can pass additional data to the view if necessary
        return $dataTable->render('admin.Sale_OrderReport.index');
    }

    /**
     * Handle filtering for the sale order report.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function generateReports(Sale_orderReportDataTable $dataTable, Request $request)
    {
        // Validate the request for dates


        // Return the filtered dataTable
        return $dataTable->with([
            'from_date' => $request->input('from_date'),
            'to_date' => $request->input('to_date'),
        ])->render('admin.Sale_OrderReport.index');
    }

    public function getData(Sale_orderReportDataTable $dataTable)
    {
        return $dataTable->ajax();
    }
}

