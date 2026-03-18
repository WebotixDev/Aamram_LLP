<?php

namespace App\DataTables;

use App\Models\sale_order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class Product_DetailsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
        ->filter(function ($query) {
            if (request()->has('search.value') && !empty(request('search.value'))) {
                $search = strtolower(request('search.value'));
                $query->where(function ($query) use ($search) {
                    $query->orWhereRaw('LOWER(products.product_name) LIKE ?', ["%$search%"])
                        ->orWhereRaw('LOWER(product_details.product_size) LIKE ?', ["%$search%"])
                        ->orWhereRaw('LOWER(sale_order.stage) LIKE ?', ["%$search%"]);
                });
            }
        })

            ->addColumn('sr_no', function ($row) {
                static $serial = 0;
                $start = request()->get('start', 0);
                return $start + ++$serial;
            })
            ->editColumn('billdate', function ($row) {
                return \Carbon\Carbon::parse($row->billdate)->format('d-m-Y'); // format if needed
            })
            ->addColumn('service_name', function ($row) {
                return $row->pid . ' - ' . ($row->service_name ?? 'N/A');
            })
            ->rawColumns([]);
    }

    public function query(sale_order $model): QueryBuilder
    {

        $fromDate = request()->get('from_date');
        $toDate = request()->get('to_date');
        $product_id = request()->get('product_id');
          $season = session('selected_season');
          // $season = session('selected_season', date('Y'));

        $query = $model->newQuery()
    ->leftJoin('products', 'sale_order.services', '=', 'products.id')
    ->leftJoin('product_details', 'sale_order.size', '=', 'product_details.id')
    ->leftJoin('sale_orderdetails', 'sale_order.pid', '=', 'sale_orderdetails.id')
    ->where('sale_orderdetails.season', $season)
    ->select([
        'sale_order.*',
        'products.product_name as service_name',
        'product_details.product_size as size_name',
        'sale_orderdetails.billdate', // include this if you want to show billdate in listing
    ]);


            if (!empty($product_id)) {
                $query->where('sale_order.services', $product_id); // assuming it's storing customer_id
            }
if ($fromDate && $toDate) {
    $query->whereBetween('sale_orderdetails.billdate', [$fromDate, $toDate]);
}

$query->orderBy('sale_orderdetails.billdate', 'desc');

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('productDetailsTable')
           ->addColumn(['data' => 'sr_no', 'title' => __('Sr. No.')])
           ->addColumn(['data' => 'billdate', 'title' => __('Date')])
            ->addColumn(['data' => 'service_name', 'title' => __('Product'), 'orderable' => true])
            ->addColumn(['data' => 'size_name', 'title' => __('Size'), 'orderable' => true])
            ->addColumn(['data' => 'stage', 'title' => __('Stage'),'orderable' => true])
            ->addColumn(['data' => 'qty', 'title' => __('Qty')])
            ->addColumn(['data' => 'rate', 'title' => __('Rate')])
            ->addColumn(['data' => 'amount', 'title' => __('Amount')])
            ->minifiedAjax()
            ->orderBy(1, 'desc') // Column index 1 is billdate
            ->parameters([
                'language' => [
                    'emptyTable' => 'No Records Found',
                    'infoEmpty' => '',
                    'zeroRecords' => 'No Records Found',
                ],
                'drawCallback' => 'function(settings) {
                    if (settings._iRecordsDisplay === 0) {
                        $(settings.nTableWrapper).find(".dataTables_paginate").hide();
                    } else {
                        $(settings.nTableWrapper).find(".dataTables_paginate").show();
                    }
                    feather.replace();
                }',
            ]);
    }

    protected function filename(): string
    {
        return 'ProductDetails_' . date('YmdHis');
    }

}
