<?php

namespace App\DataTables;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Product_details;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class ProductDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('d-m-Y');
            })
            ->addColumn('sr_no', function ($row) {
                static $serial = 0;
                $start = request()->get('start', 0);
                return $start + ++$serial;
            })
            ->addColumn('details', function ($row) {
                $details = $row->details; // Assuming 'details' is the relationship
                $html = '<table class="table table-bordered small"><thead>';
                $html .= '<tr><th>Product Size</th><th>Sale Price</th><th>Purchase Price</th></tr></thead><tbody>';

        foreach ($details->where('disable','!=',1) as $detail) {
                        $html .= '<tr>';
                    $html .= "<td>{$detail->product_size}</td>";
                    $html .= "<td>{$detail->dist_price}</td>";
                    $html .= "<td>{$detail->purch_price}</td>";
                    $html .= '</tr>';
                }

                $html .= '</tbody></table>';
                return $html;
            })
            ->addColumn('img', function ($row) {
                $imageUrl = !empty($row->img) ? asset('public/' . $row->img) : asset('public/uploads/product_img/default-sign-path.jpg');

                return '<img src="' . $imageUrl . '" style="width:80px !important; height:80px !important; object-fit: cover; border-radius: 5px;"/>';
            })

            ->editColumn('action', function ($row) {
                return view('admin.inc.action', [
                    'edit' => 'admin.product.edit',
                    'delete' => 'admin.product.destroy',
                    'data' => $row
                ]);
            })
            ->rawColumns(['action', 'details', 'img']); // Ensure 'img' column is not escaped
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Product $model): QueryBuilder
    {
        $query = $model->newQuery()->with('details'); // Eager load details relationship
        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('product-main-table')
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
            ['data' => 'product_name', 'title' => __('Product Name'), 'orderable' => true, 'searchable' => true],
            ['data' => 'details', 'title' => __('Product Details'), 'orderable' => true, 'searchable' => true],

            ['data' => 'img', 'title' => __('Image'), 'orderable' => true, 'searchable' => true],
            //['data' => 'updated_at', 'title' => __('Created At'), 'orderable' => true, 'searchable' => true],
            ['data' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ProductDetails_' . date('YmdHis');
    }
}
