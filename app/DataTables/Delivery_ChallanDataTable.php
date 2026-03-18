<?php

namespace App\DataTables;

use Carbon\Carbon;
use App\Models\Delivery_Challan;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class Delivery_ChallanDataTable extends DataTable
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
                    // Search transporter
                    $query->orWhereRaw('LOWER(delivery_challan.transporter) LIKE ?', ["%$search%"]);

                    // Search in product name
                    $query->orWhereHas('details.product', function ($q) use ($search) {
                        $q->whereRaw('LOWER(product_name) LIKE ?', ["%$search%"]);
                    });

                    // Search in product size
                    $query->orWhereHas('details.productDetail', function ($q) use ($search) {
                        $q->whereRaw('LOWER(product_size) LIKE ?', ["%$search%"]);
                    });

                    // Search in customer name (if details has customer relation)
                    $query->orWhereHas('details.customer', function ($q) use ($search) {
                        $q->whereRaw('LOWER(customer_name) LIKE ?', ["%$search%"]);
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
                return $start + ++$serial;
            })
            ->addColumn('details', function ($row) {
                $details = $row->details;
                $html = '<table class="table table-bordered small"><thead>';
                $html .= '<tr><th>Order No</th><th>Customer Name</th><th>Services</th><th>Size</th><th>Stage</th><th>Current Dispatch</th></tr></thead><tbody>';

                foreach ($details as $detail) {
                    $productName = $detail->product->product_name ?? 'N/A'; // Fetch product name
                    $psizeName = $detail->productDetail->product_size ?? 'N/A'; // Fetch product name
                    $customerName = $detail->customer->customer_name ?? 'N/A'; // Fetch product name

                    // echo $psizeName;
                    // die;
                    $html .= '<tr>';
                    $html .= "<td>{$detail->order_no}</td>";
                    $html .= "<td>{$customerName}</td>";
                    $html .= "<td>{$productName}</td>";
                    $html .= "<td>{$psizeName}</td>";
                    $html .= "<td>{$detail->stage}</td>";
                    $html .= "<td>{$detail->currdispatch_qty}</td>";
                    $html .= '</tr>';
                }

                $html .= '</tbody></table>';
                return $html;
            })
            ->editColumn('action', function ($row) {
                $actionHtml=  view('admin.inc.action', [
                    'edit' => 'admin.Delivery_Challan.edit',
                    'delete' => 'admin.Delivery_Challan.destroy',
                    'data' => $row
                ])->render();

                $printButton = '<button style="background-color:#0f5183; float:left;" ><a href="' . route('admin.devlivery_challan.print', $row->id) . '" target="_blank">
                DC
            </a></button>';

            $customerButton = '<button style="background-color:rgb(84, 228, 62) ;  float:right;" ><a href="' . route('admin.customer-chllan.print', $row->id) . '" target="_blank">
            CI
        </a></button>';

              return $actionHtml . ' ' . $printButton .' '.$customerButton;
            })
            ->rawColumns(['action', 'details']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Delivery_Challan $model): QueryBuilder
    {
        $fromDate = request()->get('from_date');
        $toDate = request()->get('to_date');


    $season = session('selected_season');
    // $season = session('selected_season', date('Y'));

    $query = $model->newQuery()
        ->select('delivery_challan.*')
        ->with(['details'])
        ->where('season', $season); // Add this line
        // $query = $model->newQuery()->with('details');

        if ($fromDate && $toDate) {
            $query->whereBetween('billdate', [$fromDate, $toDate]);
        }
        $query->orderBy('billdate', 'desc');

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('delivery_challan-main-table')
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
            ['data' => 'Invoicenumber', 'title' => __('Challan No'), 'orderable' => true, 'searchable' => true],
            ['data' => 'billdate', 'title' => __('Challan Date'), 'orderable' => true, 'searchable' => true],
            ['data' => 'transporter', 'title' => __('Transpoter'), 'orderable' => true, 'searchable' => true],
            ['data' => 'details', 'title' => __('Challan Details'), 'orderable' => false, 'searchable' => false],
            ['data' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'DeliveryChallan_' . date('YmdHis');
    }
}
