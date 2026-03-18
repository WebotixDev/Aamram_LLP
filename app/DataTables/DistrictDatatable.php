<?php

namespace App\DataTables;

use App\Models\District;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class DistrictDatatable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowId('district_id')
            ->filter(function ($query) {
                if (request()->has('search.value') && !empty(request()->input('search.value'))) {
                    $search = strtolower(request()->input('search.value'));
                    $query->where(function ($query) use ($search) {
                        $query->whereRaw('LOWER(districts.district_name) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(states.name) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(countries.name) LIKE ?', ["%$search%"]);
                    });
                }
            })
            ->editColumn('country_name', function ($row) {
                return $row->country_name ?? 'N/A';
            })
            ->editColumn('state_name', function ($row) {
                return $row->state_name ?? 'N/A';
            })
            ->editColumn('date', function ($row) {
                return \Carbon\Carbon::parse($row->date)->format('d-m-Y'); // Format the date
            })
            ->editColumn('action', function ($row) {
                return view('admin.inc.action', [
                    'edit'   => 'admin.district.edit',
                    'delete' => 'admin.district.destroy',
                    'data'   => $row,
              
                ]);
            })
            ->addColumn('sr_no', function ($row) {
                // Get the start index for pagination from the request
                $start = request()->get('start', 0);  // Default to 0 if 'start' is not available
                static $serial = 0;
            
                // Increment the serial number based on the start index
                return $start + ++$serial;
            })
            ->rawColumns(['action', 'date']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(District $model): QueryBuilder
    {
        return $model->newQuery()
            ->select([
                'districts.*', 
                'countries.name as country_name', 
                'states.name as state_name'
            ])
            ->leftJoin('countries', 'districts.country_id', '=', 'countries.id')
            ->leftJoin('states', 'districts.state_id', '=', 'states.id'); // Joins must match your schema
    }

    /**
     * Optional method if you want to use the HTML builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('district-table')
            ->addColumn(['data' => 'sr_no', 'title' => __('Sr. No.')])
            ->addColumn(['data' => 'district_name', 'title' => __('District Name'), 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'country_name', 'title' => __('Country'), 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'state_name', 'title' => __('State'), 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'date', 'title' => __('Date'), 'orderable' => true, 'searchable' => false])
            ->addColumn(['data' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false])
            ->minifiedAjax()
            ->orderBy(1)
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
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'District_' . date('YmdHis');
    }
}
