<?php

namespace App\Http\Controllers;

use App\DataTables\Product_DetailsDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Sale_orderProductController extends Controller
{
    /**
     * Display the sale order report.
     *
     * @param Sale_orderReportDataTable $dataTable
     * @return \Illuminate\View\View
     */
    public function index(Product_DetailsDataTable $dataTable)
    {
        // You can pass additional data to the view if necessary
        return $dataTable->render('admin.Sale_product.index');
    }

    /**
     * Handle filtering for the sale order report.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
  

    public function getData(Product_DetailsDataTable $dataTable)
    {

        return $dataTable->ajax();
    }
}

