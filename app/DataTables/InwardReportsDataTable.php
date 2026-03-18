<?php

namespace App\DataTables;

use Carbon\Carbon;
use App\Models\inward;
use App\Models\inward_details;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class InwardReportsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('PurchaseDate', function ($row) {
                return Carbon::parse($row->PurchaseDate)->format('d-m-Y');
            })
            ->addColumn('sr_no', function ($row) {
                static $serial = 0;
                $start = request()->get('start', 0);
                return $start + ++$serial;
            })
            // ->addColumn('details', function ($row) {
            //     // Load related details for the current row
            //     $details = $row->details; // Assuming 'details' is the relationship
            //     $html = '<table class="table table-bordered small"><thead>';
            //     $html .= '<tr><th>Products</th><th>Size</th><th>Stage</th><th>Rate</th><th>Quantity</th></tr></thead><tbody>';

            //     foreach ($details as $detail) {
            //         $productName = $detail->product->product_name ?? 'N/A'; // Fetch product name
            //         $sizeName = $detail->productDetail->product_size ?? 'N/A'; // Fetch size name

            //         $html .= '<tr>';
            //         $html .= "<td>{$productName}</td>";
            //         $html .= "<td>{$sizeName}</td>";
            //         $html .= "<td>{$detail->stage}</td>";
            //         $html .= "<td>{$detail->rate}</td>";
            //         $html .= "<td>{$detail->Quantity}</td>";
            //         $html .= '</tr>';
            //     }

            //     $html .= '</tbody></table>';
            //     return $html;
            // })

            ->rawColumns(['details']); // Allow rendering nested table HTML
    }

    /**
     * Get the query source of dataTable.
     */


public function query(inward_details $model): QueryBuilder
{
    $fromDate = request()->get('from_date');
    $toDate = request()->get('to_date');
       $season = session('selected_season');

    // $query = $model->newQuery()
    // ->with(['details.product', 'details.productDetail']);

    $query = $model->newQuery()
            ->leftJoin('products', 'purchase_product.services', '=', 'products.id')
            ->leftJoin('purchase_details', 'purchase_product.pid', '=', 'purchase_details.id')
            ->where('purchase_details.season', $season)
            ->select([
                'purchase_product.*',
                'purchase_details.id as sid',
                'purchase_details.billdate',
                'purchase_details.Tquantity',
                'products.product_name as product_name',
            ]);


    if ($fromDate && $toDate) {
        $query->whereBetween('purchase_details.billdate', [$fromDate, $toDate]);
    }

    return $query;
}


    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('inward-reports-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'des') // Order by serial number
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
            ['data' => 'sr_no', 'title' => __('Sr. No')],
            ['data' => 'billdate', 'title' => __('Inward Date')],
            ['data' => 'product_name', 'title' => __('Products'), 'orderable' => true, 'searchable' => true],
            ['data' => 'productsizes', 'title' => __('Size'), 'orderable' => true, 'searchable' => true],
            ['data' => 'rate', 'title' => __('Rate'), 'orderable' => true, 'searchable' => true],
           ['data' => 'Quantity', 'title' => __('Quantity'), 'orderable' => true, 'searchable' => true],
            //['data' => 'details', 'title' => __(' PRODUCT DETAILS'), 'orderable' => true, 'searchable' => true],
         ['data' => 'Tquantity', 'title' => __('Total Amount')],
        ];
    }

    /**
     * Get the filename for export
     */
    protected function filename(): string
    {
        return 'InwardReports_' . date('YmdHis');
    }
}
