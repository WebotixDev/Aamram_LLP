<?php

namespace App\DataTables;

use App\Models\Customer;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class CustomerDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowId('customer_id')
            ->filter(function ($query) {
                // Apply search filters dynamically
                if (request()->has('search.value') && !empty(request()->input('search.value'))) {
                    $search = strtolower(request()->input('search.value'));
                    $query->where(function ($query) use ($search) {
                        $query->whereRaw('LOWER(customers.customer_name) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(customers.company_name) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(customers.mobile_no) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(customers.vendor) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(customers.email_id) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(customers.city_name) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(customers.district_id) LIKE ?', ["%$search%"]);

                    });
                }
            })
            ->editColumn('state_name', function ($row) {
                return $row->state_name ?? 'N/A';
            })
            ->editColumn('country_name', function ($row) {
                return $row->country_name ?? 'N/A';
            })
            ->editColumn('district_name', function ($row) {
                return $row->district_name ?? 'N/A';
            })

            ->editColumn('action', function ($row) {
                return view('admin.inc.action', [
                    'edit'   => 'admin.customer.edit',
                    //'delete' => 'admin.customer.destroy',
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
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Customer $model): QueryBuilder
    {
        return $model->newQuery()
            ->select([
                'customers.*',
                'states.name as state_name',
                'countries.name as country_name',
            ])
            ->leftJoin('states', 'customers.state_id', '=', 'states.id')
            ->leftJoin('countries', 'customers.country_id', '=', 'countries.id');
    }


    /**
     * Optional method if you want to use the HTML builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('customer-table')
            ->addColumn(['data' => 'sr_no', 'title' => __('Sr. No.')])

            ->columns([
                ['data' => 'sr_no', 'title' => __('Sr. No.')],
                ['data' => 'customer_name', 'title' => __('Customer Name'), 'orderable' => true, 'searchable' => true],
                ['data' => 'company_name', 'title' => __('Company Name'), 'orderable' => true, 'searchable' => true],
                ['data' => 'mobile_no', 'title' => __('Mobile No'), 'orderable' => true, 'searchable' => true],
                ['data' => 'vendor', 'title' => __('Vendor'), 'orderable' => true, 'searchable' => true],
                ['data' => 'email_id', 'title' => __('Email'), 'orderable' => true, 'searchable' => true],
                ['data' => 'district_id', 'title' => __('District'), 'orderable' => true, 'searchable' => true],

                ['data' => 'city_name', 'title' => __('City'), 'orderable' => true, 'searchable' => true],
               // ['data' => 'state_name', 'title' => __('State'), 'orderable' => true, 'searchable' => true],
               // ['data' => 'country_name', 'title' => __('Country'), 'orderable' => true, 'searchable' => true],
               // ['data' => 'district_name', 'title' => __('District'), 'orderable' => true, 'searchable' => true],
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
                'lengthMenu' => [ [50, 100], [50, 100] ],  // Set the available records per page options here.

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
        return 'Customer_' . date('YmdHis');
    }
}
