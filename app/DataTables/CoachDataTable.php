<?php
namespace App\DataTables;

use App\Models\Ground;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class CoachDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->editColumn('created_at', function ($row) {
                return $row->created_at->diffForHumans();
            })
            ->editColumn('action', function ($row) {
                return view('admin.inc.action', [
                    'edit'   => 'admin.ground.edit',
                    'delete' => 'admin.ground.destroy',
                    'data'   => $row
                ]);
            })
            ->editColumn('ground_status', function ($row) {
                return $row->ground_status == 1 ? 'Active' : 'Inactive';
            })
            ->rawColumns(['created_at', 'action', 'ground_status']);
    }

    public function query(Ground $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('ground-table')
            ->addColumn(['data' => 'ground_name', 'title' => __('Ground Name'), 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'ground_address', 'title' => __('Address'), 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'ground_descr', 'title' => __('Description'), 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'ground_mobile', 'title' => __('Contact no'), 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'ground_charge', 'title' => __('charges Per Day'), 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'ground_status', 'title' => __('Status'), 'orderable' => true, 'searchable' => false])
            ->addColumn(['data' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false])
            ->minifiedAjax()  // Ensure you have an AJAX route handling this
            ->orderBy(0)  // Make sure this points to the correct column index for your desired default sorting
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

    
}
