<?php

namespace App\DataTables;

use App\Models\Location;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class LocationDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))

            ->setRowId('id')

            // Global Search Filter
      ->filter(function ($query) {

    if (request()->has('search.value') && !empty(request()->input('search.value'))) {

        $search = strtolower(request()->input('search.value'));

        $query->where(function ($query) use ($search) {

            // Search supplier fields
            $query->whereRaw('LOWER(location) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(mobile_no) LIKE ?', ["%{$search}%"])
                ->orWhereRaw('LOWER(address) LIKE ?', ["%{$search}%"])

                // 🔥 Search product names
                ->orWhereExists(function ($subQuery) use ($search) {

                    $subQuery->select(DB::raw(1))
                        ->from('products')
                        ->whereRaw("FIND_IN_SET(products.id, supplier.products)")
                        ->whereRaw('LOWER(products.product_name) LIKE ?', ["%{$search}%"]);
                });
        });
    }
})


            // Serial Number Column
            ->addColumn('sr_no', function ($row) {
                $start = request()->get('start', 0);
                static $serial = 0;
                return $start + ++$serial;
            })

            // Action Buttons
            ->addColumn('action', function ($row) {
                return view('admin.inc.action', [
                    'edit'   => 'admin.Location.edit',
                    'delete' => 'admin.Location.destroy',
                    'data'   => $row
                ]);
            })

            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Location $model): QueryBuilder
    {
        return $model->newQuery()
            ->select('location.*')
            ->orderBy('id', 'desc'); // ID wise DESC
    }

    /**
     * Optional method if you want to use the HTML builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('Location-table')
            ->columns([
                ['data' => 'sr_no', 'title' => __('Sr. No.'), 'orderable' => false, 'searchable' => false],
                ['data' => 'location', 'title' => __('Location')],
                ['data' => 'purchase_manager', 'title' => __('Purchase Manager')],
                ['data' => 'mobile_no', 'title' => __('Mobile No')],
                ['data' => 'address', 'title' => __('Address')],
                ['data' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false],
            ])
            ->minifiedAjax()
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
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Location_' . date('YmdHis');
    }
}
