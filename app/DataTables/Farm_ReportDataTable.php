<?php

namespace App\DataTables;

use Carbon\Carbon;
use App\Models\farm_inward;
use App\Models\farm_inward_details;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class Farm_ReportDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable( $query))
            ->editColumn('PurchaseDate', function ($row) {
                return Carbon::parse($row->PurchaseDate)->format('d-m-Y');
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
                $html .= '<tr><th>Products</th><th>Rate</th><th>Quantity</th></tr></thead><tbody>';
            
                foreach ($details as $detail) {
                    $productName = $detail->product->product_name ?? 'N/A'; // Fetch product name
                    //$sizeName = $detail->productDetail->size ?? 'N/A'; // Fetch size name
               
                    $html .= '<tr>';
                    $html .= "<td>{$productName}</td>";
//$html .= "<td>{$detail->size}</td>";
                    $html .= "<td>{$detail->rate}</td>";
                    $html .= "<td>{$detail->Quantity}</td>";
                    $html .= '</tr>';
                }
            
                $html .= '</tbody></table>';
                return $html;
            })
            ->editColumn('action', function ($row) {
                return view('admin.inc.action', [
                    'edit' => 'admin.farm_inward.edit',
                    'delete' => 'admin.farm_inward.destroy',
                    'data' => $row
                ]);
            })
            ->rawColumns(['action', 'details']); // Ensure 'action' and 'details' are handled as raw HTML
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(farm_inward $model): QueryBuilder
    {
        $fromDate = request()->get('from_date');
        $toDate = request()->get('to_date');

        $season = session('selected_season');
            // $season = session('selected_season', date('Y'));
        
            $query = $model->newQuery()
                ->select('farm_inward.*')
                ->with(['details'])
                ->where('season', $season); // Add this line
        // $query = $model->newQuery()->with('details'); // Eager load details relationship

        if ($fromDate && $toDate) {
            $query->whereBetween('PurchaseDate', [$fromDate, $toDate]);
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('farm_inward-report-table')
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
             ['data' => 'supplier', 'title' => __('Supplier'), 'orderable' => true, 'searchable' => true],
            ['data' => 'PurchaseDate', 'title' => __('Harvest Date'), 'orderable' => true, 'searchable' => true],
            ['data' => 'details', 'title' => __('Product Details'), 'orderable' => true, 'searchable' => true],
            ['data' => 'Tquantity', 'title' => __('Total Rate'), 'orderable' => true, 'searchable' => true],

          //  ['data' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false],
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
