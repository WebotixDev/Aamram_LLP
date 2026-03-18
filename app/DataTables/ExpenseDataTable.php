<?php

namespace App\DataTables;

use App\Models\Expense;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class ExpenseDataTable extends DataTable
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
                        $query->whereRaw('LOWER(expenses.exp_no) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(expenses.date) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(expenses.exp_type) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(expenses.narration) LIKE ?', ["%$search%"]);
                    });
                }
            })
            ->editColumn('action', function ($row) {
                return view('admin.inc.action', [
                    'edit'   => 'admin.expense.edit',
                    'delete' => 'admin.expense.destroy',
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
    public function query(Expense $model): QueryBuilder
    {
        
            $season = session('selected_season');
    // $season = session('selected_season', date('Y'));
    
        return $model->newQuery()
            ->leftJoin('subjects', 'expense.exp_type', '=', 'subjects.id')
            ->leftJoin('expense_category', 'expense_category.id', '=', 'expense.expense_name') // Join with expense_category table
             ->where('expense.season', $season)
            ->select([
                'expense.*',
                'subjects.subject_name as exp_type_name', // Fetch the subject name
                'expense_category.name as expense_category_name' // Fetch the category name
            ]);
    }



    /**
     * Optional method if you want to use the HTML builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('expense-table')
            ->addColumn(['data' => 'sr_no', 'title' => __('Sr. No.')])
            ->columns([
                ['data' => 'sr_no', 'title' => __('Sr. No.')],
                ['data' => 'season', 'title' => __('Season'), 'orderable' => true, 'searchable' => true],
                ['data' => 'date', 'title' => __('Date'), 'orderable' => true, 'searchable' => true],
                ['data' => 'expense_category_name', 'title' => __('Expense Category'), 'orderable' => true, 'searchable' => true],

                ['data' => 'exp_type_name', 'title' => __('Expense Type'), 'orderable' => true, 'searchable' => true],
                ['data' => 'amt_pay', 'title' => __('Amount'), 'orderable' => true, 'searchable' => true],
                ['data' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false]
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
        return 'Expense_' . date('YmdHis');
    }
}
