<?php
namespace App\DataTables;
use Carbon\Carbon;

use App\Models\Investor;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class InvestorDataTable extends DataTable
{
    protected $investorId;

    // Accept investorId as a parameter
    public function __construct($investorId = null)
    {

        $this->investorId = $investorId;
    }

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
                        $query->whereRaw('LOWER(investors_payment.investors_name) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(investors_payment.date) LIKE ?', ["%$search%"])
                            ->orWhereRaw('LOWER(investors_payment.narration) LIKE ?', ["%$search%"]);
                    });
                }
            })
            ->editColumn('action', function ($row) {
                return view('admin.inc.action', [
                    'edit'   => 'admin.investors.edit',
                    'delete' => 'admin.investors.destroy',
                    'data'   => $row
                ]);
            })
            ->editColumn('date', function ($row) {
                return Carbon::parse($row->date)->format('d-m-Y');
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
    public function query(Investor $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->select([
                'investors_payment.*',
                'investors_name.investors_name',
            ])
            ->leftJoin('investors_name', 'investors_name.id', '=', 'investors_payment.investor_name');

        // If an investorId is provided, filter by it
            $query->where('investors_payment.investor_name', $this->investorId); // Filter by investorId


        return $query;
    }

    /**
     * Optional method if you want to use the HTML builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('investors-table')
            ->addColumn(['data' => 'sr_no', 'title' => __('Sr. No.')])
            ->columns([
                ['data' => 'sr_no', 'title' => __('Sr. No.')],
                ['data' => 'investors_name', 'title' => __('Investor Name'), 'orderable' => true, 'searchable' => true],
                ['data' => 'date', 'title' => __('Date'), 'orderable' => true, 'searchable' => true],
                ['data' => 'amt_pay', 'title' => __('Amount'), 'orderable' => true, 'searchable' => true],
                ['data' => 'type', 'title' => __('Type'), 'orderable' => true, 'searchable' => true],
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
        return 'Investors_' . date('YmdHis');
    }
}
