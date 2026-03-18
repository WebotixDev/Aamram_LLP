<?php
namespace App\DataTables;

use Carbon\Carbon;
use App\Models\sale_details;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class Sale_orderDataTable extends DataTable
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
                $html = '<table class="table table-bordered small"><thead class="remove">';
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

            // ->addColumn('color', function ($row) {
            //     // Assign a default color based on user_id or other conditions
            //     $color = 'blue'; // Set default color for all cases

            //     // Check for specific user_id and change color accordingly
            //     if ($row->user_id === 'web') {
            //         $color = 'orange';
            //     } elseif ($row->user_id === 'chatbot') {
            //         $color = 'green';
            //     }

            //     return '<button style="width: 20px; height: 20px; background-color: ' . $color . '; border-radius: 50%; border: none;"></button>';
            // })




            ->editColumn('action', function ($row) {
                // $actionHtml = view('admin.inc.action', [
                //     'edit' => 'admin.sale_order.edit',
                //     'delete' => 'admin.sale_order.destroy',
                //     'data' => $row
                // ])->render();

                if (!in_array($row->user_id, ['web', 'chatbot'])) {
                    $actionHtml = view('admin.inc.action', [
                        'edit' => 'admin.sale_order.edit',
                        'delete' => 'admin.sale_order.destroy',
                        'data' => $row
                    ])->render();
                } else {
                    $actionHtml = ''; // Hide buttons by setting an empty string
                }

                $printButton = '<button style="background-color:#0f5183; float:left;" ><a href="' . route('admin.sale_order.print', $row->id) . '" target="_blank">
                SB
            </a></button>';

            // $lbButton = '<button style=" background-color:rgb(84, 228, 62) ; float:left; "><a href="' . route('admin.sale_order.labour', $row->id) . '" target="_blank">
            //             LB
            //         </a></button>';

                //     $salerazorpay = '<button style="margin-top: 10px; background-color:rgb(21, 208, 214)">
                //     <a href="' . route('sale.razorpay', ['id' => $row->id]) . '" target="_blank">
                //         RP
                //     </a>
                // </button>';


return $actionHtml . ' ' . $printButton ;


            })

            ->addColumn('customer_name', function ($row) {
                return $row->id . ' - ' . ($row->customer_display_name ?? 'N/A');
            })


            ->rawColumns(['action', 'details' ,'sr_no']);
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
      // $season = session('selected_season', date('Y'));

        $query = $model->newQuery()
            ->select(
                'sale_orderdetails.*',
                'customers.customer_name as customer_display_name'  // Alias for customer_name
            )
            ->join('customers', 'customers.id', '=', 'sale_orderdetails.customer_name')
           ->where('sale_orderdetails.season', $season)
            ->with(['details', 'customer']);

        if ($fromDate && $toDate) {
            $query->whereBetween('billdate', [$fromDate, $toDate]);
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
     * Optional method if you want to use the html builder.
     */

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('sale-order-main-table')
            ->columns($this->getColumns())
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

    /**
     * Define DataTable columns.
     */
    public function getColumns(): array
    {
        return [

            ['data' => 'sr_no', 'title' => __('Sr. No'), 'orderable' => true, 'searchable' => true],
            ['data' => 'billdate', 'title' => __('Bill Date'), 'orderable' => true, 'searchable' => true],
           // ['data' => 'Invoicenumber', 'title' => __('Invoice No'), 'orderable' => true, 'searchable' => true],
           ['data' => 'customer_name', 'title' => __('Customer Name'), 'orderable' => true, 'searchable' => true],

            ['data' => 'details', 'title' => __('Product Details'), 'orderable' => true, 'searchable' => false],
            ['data' => 'Tamount', 'title' => __('Total Amount'), 'orderable' => true, 'searchable' => true],
            ['data' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false],

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
