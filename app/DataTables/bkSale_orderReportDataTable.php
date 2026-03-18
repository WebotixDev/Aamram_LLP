<?php

namespace App\DataTables;

use Carbon\Carbon;
use App\Models\sale_details;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;

class Sale_orderReportDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
          ->filter(function ($query) {
                if (request()->has('search.value') && !empty(request()->input('search.value'))) {
                    $search = strtolower(request()->input('search.value'));
                    $query->where(function ($query) use ($search) {
                        $query
                        ->orWhereRaw('LOWER(wholesaler.customer_name) LIKE ?', ["%$search%"]) // Ensure wholesaler is searchable
                        ->orWhereRaw('LOWER(customers.customer_name) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(sale_orderdetails.billdate) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(sale_orderdetails.Invoicenumber) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(sale_orderdetails.Tamount) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(customers.address) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(customers.mobile_no) LIKE ?', ["%$search%"]);

                            $query->orWhereHas('details.product', function ($q) use ($search) {
                                $q->whereRaw('LOWER(product_name) LIKE ?', ["%$search%"]);
                            });

                            $query->orWhereHas('details.productDetail', function ($q) use ($search) {
                                $q->whereRaw('LOWER(product_size) LIKE ?', ["%$search%"]);
                            });
                    });
                }
            })
            ->editColumn('billdate', function ($row) {
                return Carbon::parse($row->billdate)->format('d-m-Y');
            })
            ->addColumn('sr_no', function ($row) {
                static $serial = 0;
                $start = request()->get('start', 0);
                $color = 'blue !important'; // Default color

                if ($row->user_id === 'web') {
                    $color = 'rgb(247, 223, 9) !important';
                } elseif ($row->user_id === 'chatbot') {
                    $color = 'rgb(22, 236, 22) !important';
                }

                $serialNumber = $start + ++$serial;
                return '<span style="width: 20px; height: 20px; background-color: ' . $color . '; border-radius: 50%; border: none; display: inline-block;"></span> ' . $serialNumber;
            })
            ->addColumn('wholesaler', function ($row) {
                return $row->wholesaler_name ?? ''; // Use already joined data
            })
            ->addColumn('details', function ($row) {
                $details = $row->details;
                $html = '<table class="table table-bordered small"><thead>';
                $html .= '<tr><th>Products</th><th>Size</th><th>Stage</th><th>Rate</th><th>Quantity</th></tr></thead><tbody>';

                foreach ($details as $detail) {
                    $productName = $detail->product->product_name ?? 'N/A';
                    $sizeName = $detail->productDetail->product_size ?? 'N/A';

                    $html .= '<tr>';
                    $html .= "<td>{$productName}</td>";
                    $html .= "<td>{$sizeName}</td>";
                    $html .= "<td>{$detail->stage}</td>";
                    $html .= "<td>{$detail->rate}</td>";
                    $html .= "<td>{$detail->qty}</td>";
                    $html .= '</tr>';
                }

                $html .= '</tbody></table>';
                return $html;
            })
             ->addColumn('customer_name', function ($row) {
                return $row->id . ' - ' . ($row->customer_display_name ?? 'N/A');
            })

            ->rawColumns(['details', 'sr_no', 'wholesaler']);
    }

    /**
     * Get the query source of dataTable.
     */
   public function query(sale_details $model): QueryBuilder
    {
        $fromDate = request()->get('from_date');
        $toDate = request()->get('to_date');
        $userId = request()->get('user_id');
       $season = session('selected_season');
        $query = $model->newQuery()
            ->select(
                'sale_orderdetails.*',
                'customers.customer_name as customer_display_name',
                'customers.mobile_no',
                'customers.address',
                'wholesaler.customer_name as wholesaler_name' // Fetch wholesaler name
            )
            ->join('customers', 'customers.id', '=', 'sale_orderdetails.customer_name') // Join for customer
            ->leftJoin('customers as wholesaler', 'wholesaler.id', '=', 'sale_orderdetails.wholesaler') // Join for wholesaler
            ->where('sale_orderdetails.season', $season)
            ->with(['details', 'customer']);

        // Apply date filters if they exist
        if ($fromDate && $toDate) {
            $query->whereBetween('sale_orderdetails.billdate', [$fromDate, $toDate]);
        }
        if (!empty($userId)) {
            if ($userId === 'others') {
                $query->whereNotIn('sale_orderdetails.user_id', ['web', 'chatbot']);
            } else {
                $query->where('sale_orderdetails.user_id', $userId);
            }
        }
        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('sale-order-report-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'des')
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

    /**
     * Define DataTable columns.
     */
    public function getColumns(): array
    {
        return [
            ['data' => 'sr_no', 'title' => __('Sr. No'), 'orderable' => false, 'searchable' => false],
            ['data' => 'billdate', 'title' => __('Bill Date'), 'orderable' => true, 'searchable' => true],
            ['data' => 'wholesaler', 'title' => __('Wholesaler'), 'orderable' => true, 'searchable' => true], // Ensure wholesaler is searchable
            ['data' => 'customer_display_name', 'title' => __('Customer Name'), 'orderable' => true, 'searchable' => true],
            ['data' => 'mobile_no', 'title' => __('Customer Mob'), 'orderable' => true, 'searchable' => true],
            ['data' => 'address', 'title' => __('Address'), 'orderable' => true, 'searchable' => true],
            ['data' => 'details', 'title' => __('Product Details'), 'orderable' => false, 'searchable' => false],
            ['data' => 'Tamount', 'title' => __('Total Amount'), 'orderable' => true, 'searchable' => true],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'SaleOrderDetails_' . date('YmdHis');
    }
}
