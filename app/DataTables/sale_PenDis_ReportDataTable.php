<?php

namespace App\DataTables;

use Carbon\Carbon;
use App\Models\sale_details;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;

class sale_PenDis_ReportDataTable extends DataTable
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
                    $query->orWhereRaw('LOWER(customers.customer_name) LIKE ?', ["%$search%"])
                        ->orWhereRaw('LOWER(sale_orderdetails.billdate) LIKE ?', ["%$search%"])
                        ->orWhereRaw('LOWER(sale_orderdetails.Invoicenumber) LIKE ?', ["%$search%"])
                        ->orWhereRaw('LOWER(sale_orderdetails.Tamount) LIKE ?', ["%$search%"]);


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
                $color = 'blue !important'; // Default color for all cases

                // Check for specific user_id and change color accordingly
                if ($row->user_id === 'web') {
                    $color = 'rgb(247, 223, 9) !important';
                } elseif ($row->user_id === 'chatbot') {
                    $color = 'rgb(22, 236, 22) !important';
                }

                // Return both color and serial number in the same column
                $serialNumber = $start + ++$serial;
                return '<span style="width: 20px; height: 20px; background-color: ' . $color . '; border-radius: 50%; border: none; display: inline-block;"></span> ' . $serialNumber;
            })
            // ->addColumn('customer_name', function ($row) {
            //     return $row->customer->customer_name ?? 'N/A'; // Fetch customer name
            // })
            ->addColumn('details', function ($row) {
                $details = $row->details;
                $html = '<table class="table table-bordered small"><thead>';
                $html .= '<tr><th>Products</th><th>Size</th><th>Stage</th><th>Rate</th><th>Quantity</th><th>Pen Dis</th></tr></thead><tbody>';

                foreach ($details as $detail) {
                    $productName = $detail->product->product_name ?? 'N/A';
                    $sizeName = $detail->productDetail->product_size ?? 'N/A';

                    // Fetch the total currdispatch_qty from the outward_details table for this product & size
                    $currdispatch_qty = DB::table('outward_details')
                        ->where('order_no', $row->id)
                        ->where('services', $detail->services) // Assuming services is product_id
                        ->where('size', $detail->size) // Assuming size is size_id
                        ->where('stage', $detail->stage) // Assuming size is size_id

                        ->sum('currdispatch_qty');

                    // Calculate pending dispatch quantity
                    $pendingDispatchQty = $detail->qty - $currdispatch_qty;

                    $html .= '<tr>';
                    $html .= "<td>{$productName}</td>";
                    $html .= "<td>{$sizeName}</td>";
                    $html .= "<td>{$detail->stage}</td>";
                    $html .= "<td>{$detail->rate}</td>";
                    $html .= "<td>{$detail->qty}</td>";
                    $html .= "<td>{$pendingDispatchQty}</td>"; // Add pending dispatch quantity
                    $html .= '</tr>';
                }

                $html .= '</tbody></table>';
                return $html;
            })
      ->addColumn('customer_name', function ($row) {
                return $row->id . ' - ' . ($row->customer_name ?? 'N/A');
            })
            ->rawColumns(['details','sr_no']);
    }

    /**
     * Get the query source of dataTable.
     */
    // public function query(sale_details $model): QueryBuilder
    // {
    //     $fromDate = request()->get('from_date');
    //     $toDate = request()->get('to_date');

    //     $query = $model->newQuery()
        
    //      ->select(
    //         'sale_orderdetails.id',
    //         'sale_orderdetails.billdate',
    //         'sale_orderdetails.Invoicenumber',
    //         'sale_orderdetails.Tamount',
    //         'customers.customer_name',
    //         DB::raw("SUM(sale_order.qty) as total_qty"),
    //         DB::raw("SUM(sale_order.Quantity) as total_Quantity"),
    //         DB::raw("SUM(sale_order.qty * sale_order.rate) as total_amount"),
    //         DB::raw("COALESCE(SUM(outward_details.currdispatch_qty), 0) as total_dispatch_qty")
    //     )
    //     ->join('customers', 'customers.id', '=', 'sale_orderdetails.customer_name')
    //     ->join('sale_order', 'sale_order.pid', '=', 'sale_orderdetails.id')
    //     ->leftJoin('outward_details', 'outward_details.order_no', '=', 'sale_orderdetails.id')
    //     ->with(['details', 'customer'])
    //   // ->whereRaw('sale_order.qty != sale_order.Quantity')

    //     ->groupBy(
    //         'sale_orderdetails.id',
    //         'sale_orderdetails.billdate',
    //         'sale_orderdetails.Invoicenumber',
    //         'sale_orderdetails.Tamount',
    //         'customers.customer_name'
    //     )
    //     ->havingRaw('SUM(sale_order.qty) != COALESCE(SUM(outward_details.currdispatch_qty), 0)');
          
          
    //         // ->select(
    //         //     'sale_orderdetails.id',
    //         //     'sale_orderdetails.billdate',
    //         //     'sale_orderdetails.Invoicenumber',
    //         //     'sale_orderdetails.Tamount',
    //         //     'customers.customer_name',
    //         //     DB::raw("SUM(sale_order.qty) as total_qty"),
    //         //     DB::raw("SUM(sale_order.Quantity) as total_Quantity"),
    //         //     DB::raw("SUM(sale_order.qty * sale_order.rate) as total_amount")
    //         // )
    //         // ->join('customers', 'customers.id', '=', 'sale_orderdetails.customer_name') // Adjust the foreign key if needed
    //         // ->join('sale_order', 'sale_order.pid', '=', 'sale_orderdetails.id') // Join with sale_order table
    //         // ->with(['details', 'customer'])
    //         // ->whereRaw('sale_order.qty != sale_order.Quantity')
    //         // ->groupBy(
    //         //     'sale_orderdetails.id',
    //         //     'sale_orderdetails.billdate',
    //         //     'sale_orderdetails.Invoicenumber',
    //         //     'sale_orderdetails.Tamount',
    //         //     'customers.customer_name'
    //         // );

    //     // Apply date filter if present
    //     if ($fromDate && $toDate) {
    //         $query->whereBetween('sale_orderdetails.billdate', [$fromDate, $toDate]);
    //     }

    //     return $query;
    // }



  public function query(sale_details $model): QueryBuilder
    {
        $fromDate = request()->get('from_date');
        $toDate = request()->get('to_date');
        $userId = request()->get('user_id');
    $season = session('selected_season');
      // $season = session('selected_season', date('Y'));
      
        $query = $model->newQuery()
        ->select(
            'sale_orderdetails.id',
            'sale_orderdetails.billdate',
            'sale_orderdetails.Invoicenumber',
            'sale_orderdetails.Tamount',
            'customers.customer_name',
            DB::raw("SUM(sale_order.qty) as total_qty"),
            DB::raw("SUM(sale_order.Quantity) as total_Quantity"),
            DB::raw("SUM(sale_order.qty * sale_order.rate) as total_amount"),
            DB::raw("COALESCE(dispatch_data.total_dispatch_qty, 0) as total_dispatch_qty")
        )
        ->join('customers', 'customers.id', '=', 'sale_orderdetails.customer_name')
        ->join('sale_order', 'sale_order.pid', '=', 'sale_orderdetails.id')
         ->where('sale_orderdetails.season', $season)

        // Subquery to prevent double join problems
        ->leftJoin(DB::raw('(
            SELECT order_no, SUM(currdispatch_qty) as total_dispatch_qty
            FROM outward_details
            GROUP BY order_no
        ) as dispatch_data'), 'dispatch_data.order_no', '=', 'sale_orderdetails.id')

        ->with(['details', 'customer'])

        ->groupBy(
            'sale_orderdetails.id',
            'sale_orderdetails.billdate',
            'sale_orderdetails.Invoicenumber',
            'sale_orderdetails.Tamount',
            'customers.customer_name',
            'dispatch_data.total_dispatch_qty'
        )
        ->havingRaw('ROUND(SUM(sale_order.qty), 2) != ROUND(COALESCE(dispatch_data.total_dispatch_qty, 0), 2)');

        // Apply date filter if present
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
            ->setTableId('sale_PenDis_Report-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'asc')
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
          //  ['data' => 'Invoicenumber', 'title' => __('Invoice No'), 'orderable' => true, 'searchable' => true],
            ['data' => 'customer_name', 'title' => __('Customer Name'), 'orderable' => true, 'searchable' => true],
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
