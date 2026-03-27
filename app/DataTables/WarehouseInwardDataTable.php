<?php

namespace App\DataTables;

use Carbon\Carbon;
use App\Models\Farm_Delivery_challan;
use App\Models\Warehouse_inward;

use App\Models\farm_inward_details;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class WarehouseInwardDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable( $query))
            ->editColumn('billdate', function ($row) {
                return Carbon::parse($row->billdate)->format('d-m-Y');
            })
    ->filterColumn('transporter_invoice', function ($query, $keyword) {
    $query->where(function ($q) use ($keyword) {
        $q->where('transporter_name', 'like', "%{$keyword}%")
          ->orWhere('InvoiceNumber', 'like', "%{$keyword}%");
    });
})

                ->filterColumn('details', function ($query, $keyword) {
                    $query->whereHas('details', function ($q) use ($keyword) {
                        $q->where('size_name', 'like', "%{$keyword}%")
                        ->orWhere('Quantity', 'like', "%{$keyword}%")
                        ->orWhereHas('product', function ($p) use ($keyword) {
                            $p->where('product_name', 'like', "%{$keyword}%");
                        });
                    });
                })
            ->addColumn('sr_no', function ($row) {
                static $serial = 0;
                $start = request()->get('start', 0);
                return $start + ++$serial;
            })
            ->addColumn('details', function ($row) {
                // Load related details for the current row
                $details = $row->details; // Assuming 'details' is the relationship
                $html = '<table class="table table-bordered small"><thead>';
                $html .= '<tr><th>Products</th><th>Size</th><th>Stage</th><th>Batch No</th><th>Quantity</th><th>Receive Qty</th></tr></thead><tbody>';

                foreach ($details as $detail) {
                    $productName = $detail->product->product_name ?? 'N/A'; // Fetch product name
                    //$sizeName = $detail->productDetail->size ?? 'N/A'; // Fetch size name

                    $html .= '<tr>';
                    $html .= "<td>{$productName}</td>";
                    $html .= "<td>{$detail->size_name}</td>";
                    $html .= "<td>{$detail->stage}</td>";
                    $html .= "<td>{$detail->batch_number}</td>";
                    $html .= "<td>{$detail->Quantity}</td>";
                    $html .= "<td>{$detail->received_qty}</td>";
                    $html .= '</tr>';
                }

                $html .= '</tbody></table>';
                return $html;
            })
                ->editColumn('action', function ($row) {

                    $actionButtons = view('admin.inc.action', [
                        'edit' => 'admin.warehouse_inward.edit',
                        'delete' => 'admin.warehouse_inward.destroy',
                        'data' => $row
                    ])->render();

                    $printButton = '<a href="' . route('admin.FarmDCBill.print', ['id' => $row->id]) . '"
                        target="_blank" title="Print" style="margin-left:5px;">
                        <i class="fa fa-print" style="color:#0f5183; font-size:18px;"></i>
                    </a>';

                    return $actionButtons . $printButton;
                })

            ->addColumn('transporter_invoice', function ($row) {
                return $row->receive_location_name. ' - ' . $row->Invoicenumber;
            })

            ->rawColumns(['action', 'details']); // Ensure 'action' and 'details' are handled as raw HTML
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Warehouse_inward $model): QueryBuilder
    {
        $fromDate = request()->get('from_date');
        $toDate = request()->get('to_date');

        $season = session('selected_season');

        $query = $model->newQuery()
            ->select('warehouse_inward.*')
            ->with(['details'])
            ->where('season', $season)
            ->orderBy('id', 'desc'); // ID DESC here

        if ($fromDate && $toDate) {
            $query->whereBetween('billdate', [$fromDate, $toDate]);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('farm_inward-main-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'asc') // Order by serial number
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
            ['data' => 'transporter_invoice', 'title' => __('Location-Invoice No')],
            ['data' => 'billdate', 'title' => __('Date'), 'orderable' => true, 'searchable' => true],
            ['data' => 'details', 'title' => __('Product Details'), 'orderable' => true, 'searchable' => true],
            ['data' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'InwardDetails_' . date('YmdHis');
    }
}
