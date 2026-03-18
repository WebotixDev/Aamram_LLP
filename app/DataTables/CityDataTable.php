<?php

namespace App\DataTables;

use App\Models\City;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class CityDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowId('city_id')
            ->filter(function ($query) {
                if (request()->has('search.value') && !empty(request()->input('search.value'))) {
                    $search = strtolower(request()->input('search.value'));
                    $query->where(function ($query) use ($search) {
                        $query->whereRaw('LOWER(cities.name) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(districts.district_name) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(states.name) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(countries.name) LIKE ?', ["%$search%"]);
                    });
                }
            })
            
            ->editColumn('country_name', function ($row) {
                return $row->country->name ?? 'N/A';
            })
            ->editColumn('state_name', function ($row) {
                return $row->state->name ?? 'N/A';
            })
            ->editColumn('district_name', function ($row) {
                return $row->district->district_name ?? 'N/A';
            })
            ->editColumn('date', function ($row) {
                return \Carbon\Carbon::parse($row->date)->format('d-m-Y');
            })
            ->editColumn('action', function ($row) {
                return view('admin.inc.action', [
                    'edit'   => 'admin.city.edit',
                    'delete' => 'admin.city.destroy',
                    'data'   => $row
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
    public function query(City $model): QueryBuilder
    {
        return $model->newQuery()
            ->select([
                'cities.*',
                'districts.district_name as district_name',
                'states.name as state_name',
                'countries.name as country_name'
            ])
            ->leftJoin('districts', 'cities.district_id', '=', 'districts.id') // Ensure districts are joined
            ->leftJoin('states', 'cities.state_id', '=', 'states.id')         // Ensure states are joined
            ->leftJoin('countries', 'cities.country_id', '=', 'countries.id'); // Ensure countries are joined
    }
    
    /**
     * Optional method if you want to use the HTML builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('city-table')

            ->columns([
                ['data' => 'sr_no', 'title' => __('Sr. No.')],
                ['data' => 'name', 'title' => __('City'), 'orderable' => true, 'searchable' => true],
                ['data' => 'district_name', 'title' => __('District'), 'orderable' => false, 'searchable' => false],
                ['data' => 'state_name', 'title' => __('State'), 'orderable' => false, 'searchable' => false],
                ['data' => 'country_name', 'title' => __('Country'), 'orderable' => false, 'searchable' => false],
                ['data' => 'date', 'title' => __('Date'), 'orderable' => true, 'searchable' => false],
                ['data' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false]
            ])
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
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'City_' . date('YmdHis');
    }
}
