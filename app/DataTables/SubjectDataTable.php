<?php

namespace App\DataTables;

use App\Models\Subject;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class SubjectDataTable extends DataTable
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
                    $query->whereRaw('LOWER(subjects.Invoicenumber) LIKE ?', ["%$search%"])
                        ->orWhereRaw('LOWER(subjects.billdate) LIKE ?', ["%$search%"])
                        ->orWhereRaw('LOWER(subjects.subject_name) LIKE ?', ["%$search%"]);
                });
            }
        })
        ->editColumn('billdate', function ($row) {
            // Format the billdate column to only show the date
            return \Carbon\Carbon::parse($row->billdate)->format('Y-m-d');
        })
        ->editColumn('action', function ($row) {
            return view('admin.inc.action', [
                'edit'   => 'admin.subject.edit',
                'delete' => 'admin.subject.destroy',
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
    public function query(Subject $model): QueryBuilder
    {
        return $model->newQuery()
            ->select([
                'subjects.*',
                'expense_category.name', // Assuming the column you're interested in is named 'expense_name' in 'expense_category' table
            ])
            ->leftJoin('expense_category', 'expense_category.id', '=', 'subjects.expense_name'); // Assuming there is a foreign key `expense_category_id` in the `subjects` table
    }


    /**
     * Optional method if you want to use the HTML builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('subject-table')
            ->addColumn(['data' => 'sr_no', 'title' => __('Sr. No.')])
            ->columns([
                ['data' => 'sr_no', 'title' => __('Sr. No.')],
                ['data' => 'name', 'title' => __('Expense Category'), 'orderable' => true, 'searchable' => true],

                ['data' => 'subject_name', 'title' => __('Sub-Category'), 'orderable' => true, 'searchable' => true],
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
        return 'Subject_' . date('YmdHis');
    }
}
