<?php

namespace App\DataTables;

use App\Models\inward_details;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class Batch_StockReportDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Results from query() method.
     */
    public function dataTable($query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('sr_no', function ($row) {
                static $serial = 0;
                $start = request()->get('start', 0);
                return $start + ++$serial;
            })
            ->addColumn('product', function ($row) {
                return $row->product->product_name . ' - ' . $row->productDetail->product_size;
            })
            ->addColumn('raw_stock', function ($row) {
                return \App\Helpers\Helpers::getstockbatch($row->services, $row->size, "Raw", \Carbon\Carbon::now()->format('Y-m-d')) ?? 0;
            })
            ->addColumn('semi_ripe_stock', function ($row) {
                return \App\Helpers\Helpers::getstockbatch($row->services, $row->size, "Semi Ripe", \Carbon\Carbon::now()->format('Y-m-d')) ?? 0;
            })
            ->addColumn('ripe_stock', function ($row) {
                return \App\Helpers\Helpers::getstockbatch($row->services, $row->size, "Ripe", \Carbon\Carbon::now()->format('Y-m-d')) ?? 0;
            })
            ->rawColumns(['product']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        // Use the Eloquent model directly instead of DB facade
        $selectedBatchId = request()->get('batch_id', null);

        // Use the Eloquent model with relationships
        $query = inward_details::with(['product', 'productDetail'])
            ->where('complete_flag', 0);

        // Filter by batch_id if provided
        if ($selectedBatchId) {
            $query->where('batch_id', $selectedBatchId);
        }

        return $query;
    }

    /**
     * Get the DataTable columns definition.
     */
    protected function getColumns(): array
    {
        return [
            ['data' => 'sr_no', 'title' => __('Sr. No.')],
            ['data' => 'product', 'title' => __('Product')],
            ['data' => 'raw_stock', 'title' => __('Raw')],
            ['data' => 'semi_ripe_stock', 'title' => __('Semi Ripe')],
            ['data' => 'ripe_stock', 'title' => __('Ripe')],
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'BatchStockReport_' . date('YmdHis');
    }
}
