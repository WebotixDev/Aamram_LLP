<?php

namespace App\DataTables;

use App\Models\sale_order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class Sale_orderDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->filter(function ($query) {
                if (request()->has('search.value') && !empty(request()->input('search.value'))) {
                    $search = strtolower(request()->input('search.value'));
                    $query->where(function ($query) use ($search) {
                        $query->orWhereRaw('LOWER(sale_order.pid) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(products.product_name) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(product_details.product_size) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(sale_order.qty) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(sale_order.amount) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(sale_orderdetails.order_address) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(sale_orderdetails.Tamount) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(sale_orderdetails.billdate) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(customers.customer_name) LIKE ?', ["%$search%"]);
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
            ->editColumn('rate', fn($row) => number_format($row->rate, 2))
            ->editColumn('amount', fn($row) => number_format($row->amount, 2))


            ->editColumn('action', function ($row) {
    // Default: empty action HTML
    $actionHtml = '';

    // Only show edit/delete for certain users
    if (!in_array($row->user_id, ['web', 'chatbot'])) {
        $actionHtml = view('admin.inc.action', [
            'edit' => 'admin.sale_order.edit',
            'delete' => 'admin.sale_order.destroy',
            'data' => $row
        ])->render();
    }

    // Print button
    $printButton = '<a href="' . route('sale_order.print', ['id' => $row->pid]) . '" target="_blank" title="Print" style="margin-left:5px;">
        <i class="fa fa-print" style="color: #0f5183; font-size: 18px;"></i>
    </a>';

    // $printButton = '<a href="' . route('admin.sale_order.print', $row->pid) . '" target="_blank" title="Print">
//     <i class="fa fa-print" style="color: #0f5183; font-size: 18px;"></i>
// </a>';


            // $lbButton = '<button style=" background-color:rgb(84, 228, 62) ; float:left; "><a href="' . route('admin.sale_order.labour', $row->id) . '" target="_blank">
            //             LB
            //         </a></button>';

                    $salerazorpay = '<button style="margin-top: 10px; background-color:rgb(21, 208, 214)">
                    <a href="' . route('sale.razorpay', ['id' => $row->pid]) . '" target="_blank">
                        RP
                    </a>
                </button>';

    // Wrap all buttons in a single flex container
    $buttons = '<div style="display: flex; align-items: center; gap: 3px;">'
                . $actionHtml . $printButton . $salerazorpay .
               '</div>';

    return $buttons;
})


            ->addColumn('customer_name', function ($row) {
                return $row->pid . ' - ' . ($row->customer_display_name ?? 'N/A');
            })
            ->rawColumns(['action','sr_no','customer_name']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(sale_order $model)
    {
          $fromDate = request()->get('from_date');
        $toDate = request()->get('to_date');
        $userId = request()->get('user_id');
       $season = session('selected_season');
//       // $season = session('selected_season', date('Y'));

        $query = $model->newQuery()
            ->leftJoin('products', 'sale_order.services', '=', 'products.id')
            ->leftJoin('product_details', 'sale_order.size', '=', 'product_details.id')
            ->leftJoin('sale_orderdetails', 'sale_order.pid', '=', 'sale_orderdetails.id')
             ->join('customers', 'customers.id', '=', 'sale_orderdetails.customer_name')
             ->where('sale_orderdetails.season', $season)
            ->select([
                'sale_order.*',
                'sale_orderdetails.id as sid',
                'sale_orderdetails.Tamount',
                'sale_orderdetails.billdate',
                'sale_orderdetails.order_address',
                'products.product_name as product_name',
                'product_details.product_size as size_name',
               'customers.customer_name as customer_display_name' , // Alias for customer_name
            ]);

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

        // Ensure the query sorts by the latest billdate
        $query->orderBy('sale_orderdetails.billdate', 'desc');
                return $query;

    }

    /**
     * Build HTML Table for DataTable.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('sale-order-main-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'asc')
            ->parameters([
                'dom' => 'Bfrtip',
                'buttons' => [
                    [
                        'extend' => 'excelHtml5',
                        'text' => 'Export to Excel',
                        'exportOptions' => ['columns' => ':visible'],
                        'action' => 'function (e, dt, button, config) {
                            let self = this;
                            let oldStart = dt.settings()[0]._iDisplayStart;

                            dt.one("preXhr", function (e, s, data) {
                                data.start = 0;
                                data.length = -1;
                            });

                            dt.one("xhr", function (e, s, json) {
                                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
                                dt.page.len(10).draw(false);
                            });

                            dt.ajax.reload();
                        }'
                    ],
                    'reload',
                ],
                'lengthMenu' => [[100, 200], [100, 200]],
                'language' => [
                    'emptyTable' => 'No Records Found',
                    'zeroRecords' => 'No Records Found',
                ],
                'drawCallback' => 'function(settings) {
                    feather.replace();
                }',
                'scrollX' => true,
                'scrollCollapse' => true,
            ]);
    }

    /**
     * Define columns for DataTable.
     */
   protected function getColumns(): array
{
    return [
        ['data' => 'sr_no', 'title' => __('Sr. No.'), 'orderable' => true, 'searchable' => true],
        ['data' => 'billdate', 'title' => __('billdate'), 'orderable' => true, 'searchable' => true],
        ['data' => 'customer_name', 'title' => __('Customer'), 'orderable' => true, 'searchable' => true],
        ['data' => 'product_name', 'title' => __('Product'), 'orderable' => true, 'searchable' => true],
        ['data' => 'size_name', 'title' => __('Size'), 'orderable' => true, 'searchable' => true],
        ['data' => 'stage', 'title' => __('Stage'), 'orderable' => true, 'searchable' => true],
        ['data' => 'qty', 'title' => __('Qty'), 'orderable' => true, 'searchable' => true],
        ['data' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false],
        ['data' => 'amount', 'title' => __('Amount'), 'orderable' => true, 'searchable' => true],
        ['data' => 'Tamount', 'title' => __('Total'), 'orderable' => true, 'searchable' => true],
        ['data' => 'order_address', 'title' => __('Address'), 'orderable' => true, 'searchable' => true],
    ];
}


    /**
     * File name for export.
     */
    protected function filename(): string
    {
        return 'SaleOrder_' . date('YmdHis');
    }
}
