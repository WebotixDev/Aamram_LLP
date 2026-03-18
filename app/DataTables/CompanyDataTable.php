<?php

namespace App\DataTables;

use App\Models\Company;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class CompanyDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->filter(function ($query) {
                // Apply search filters dynamically
                if (request()->has('search.value') && !empty(request()->input('search.value'))) {
                    $search = strtolower(request()->input('search.value'));
                    $query->where(function ($query) use ($search) {
                        $query->whereRaw('LOWER(company.name) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(company.client_name) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(company.code) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(company.email) LIKE ?', ["%$search%"]);
                    });
                }
            })
            ->editColumn('action', function ($row) {
                return view('admin.inc.action', [
                    'edit'   => 'admin.company.edit',
                    'delete' => 'admin.company.destroy',
                    'data'   => $row
                ]);
            })
            ->addColumn('sr_no', function ($row) {
                // Get the start index for pagination from the request
                $start = request()->get('start', 0); // Default to 0 if 'start' is not available
                static $serial = 0;

                // Increment the serial number based on the start index
                return $start + ++$serial;
            })
            ->rawColumns(['action']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Company $model): QueryBuilder
    {
        return $model->newQuery()
            ->select([
                'company.*',
            ]);
    }

    /**
     * Optional method if you want to use the HTML builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('company-table')
            ->addColumn(['data' => 'sr_no', 'title' => __('Sr. No.')])
            ->columns([
                ['data' => 'sr_no', 'title' => __('Sr. No.')],
                ['data' => 'name', 'title' => __('Company Name'), 'orderable' => true, 'searchable' => true],
                ['data' => 'mobile', 'title' => __('Mobile.No'), 'orderable' => true, 'searchable' => true],
               // ['data' => 'code', 'title' => __('Code'), 'orderable' => true, 'searchable' => true],
                ['data' => 'email', 'title' => __('Email'), 'orderable' => true, 'searchable' => true],
                ['data' => 'website', 'title' => __('Website'), 'orderable' => true, 'searchable' => true],
              //  ['data' => 'logo', 'title' => __('logo'), 'orderable' => true, 'searchable' => true],
                ['data' => 'address', 'title' => __('Address'), 'orderable' => true, 'searchable' => true],



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
        return 'Company_' . date('YmdHis');
    }
}
