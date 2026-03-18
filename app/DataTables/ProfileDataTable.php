<?php

namespace App\DataTables;

use App\Models\Account;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class ProfileDataTable extends DataTable
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
                        $query->whereRaw('LOWER(accounts.bank_name) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(accounts.ACNo) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(accounts.Branch) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(accounts.IFSC) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(accounts.accounttype) LIKE ?', ["%$search%"]);
                    });
                }
            })
            ->editColumn('action', function ($row) {
                return view('admin.inc.action', [
                    'edit'   => 'admin.profile.edit',
                    'delete' => 'admin.profile.destroy',
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
    public function query(Account $model): QueryBuilder
    {
        return $model->newQuery()
            ->select([
                'accounts.*',
            ]);
    }

    /**
     * Optional method if you want to use the HTML builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('profile-table')
            ->addColumn(['data' => 'sr_no', 'title' => __('Sr. No.')])
            ->columns([
                ['data' => 'sr_no', 'title' => __('Sr. No.')],
                ['data' => 'bank_name', 'title' => __('Bank Name'), 'orderable' => true, 'searchable' => true],
                ['data' => 'ACNo', 'title' => __('Account Number'), 'orderable' => true, 'searchable' => true],
                ['data' => 'Branch', 'title' => __('Branch'), 'orderable' => true, 'searchable' => true],
                ['data' => 'IFSC', 'title' => __('IFSC Code'), 'orderable' => true, 'searchable' => true],
                ['data' => 'accounttype', 'title' => __('Account Type'), 'orderable' => true, 'searchable' => true],
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
        return 'Profile_' . date('YmdHis');
    }
}
