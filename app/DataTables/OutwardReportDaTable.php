<?php

namespace App\DataTables;

use Carbon\Carbon;
use App\Models\outward_details;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class OutwardReportDaTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
         ->filter(function ($query) {
            if (request()->has('search.value') && !empty(request()->input('search.value'))) {
                $search = strtolower(request()->input('search.value'));
                $query->where(function ($query) use ($search) {
                    $query->orWhereRaw('LOWER(customers.customer_name) LIKE ?', ["%$search%"])
                        ->orWhereRaw('LOWER(outward_details.billdate) LIKE ?', ["%$search%"])
                        ->orWhereRaw('LOWER(outward_details.Invoicenumber) LIKE ?', ["%$search%"])
                        ->orWhereRaw('LOWER(products.product_name) LIKE ?', ["%$search%"]);
                });
            }
        })
            ->editColumn('billdate', function ($row) {
                // Ensure the billdate is a Carbon instance before formatting
                return Carbon::parse($row->billdate)->format('d-m-Y');
            })
            ->addColumn('sr_no', function ($row) {
                static $serial = 0;
                $start = request()->get('start', 0);
                $color = 'blue !important'; // Default color for all cases

                // Check for specific user_id and change color accordingly
                if ($row->user_id === 'web') {
                    $color = 'rgb(247, 223, 9) !important';
                } elseif ($row->user_id === 'chatbot') {
                    $color = 'rgb(22, 236, 22) !important';
                }

                // Return both color and serial number in the same column
                $serialNumber = $start + ++$serial;
                return '<span style="width: 20px; height: 20px; background-color: ' . $color . '; border-radius: 50%; border: none; display: inline-block;"></span> ' . $serialNumber;
            })
            ->rawColumns(['sr_no']);

    }

    /**
     * Get the query source of dataTable.
     */
    public function query(outward_details $model): QueryBuilder
    {
        
                $userId = request()->get('user_id');
       $season = session('selected_season');
      // $season = session('selected_season', date('Y'));
      
        $query = $model->newQuery()
            ->leftJoin('customers', 'outward_details.customer_name', '=', 'customers.id')
            ->leftJoin('products', 'outward_details.services', '=', 'products.id') // Join products table
             ->where('outward_details.season', $season)
            ->select([
                'outward_details.*',
                'customers.customer_name',       // Fetch customer name
                'products.product_name as service_name', // Fetch service name from products
            ]);

        // Apply date filters if provided
        if (request()->has('from_date') && request()->has('to_date')) {
            $query->whereBetween('billdate', [
                request('from_date'),
                request('to_date'),
            ]);
        }
      if (!empty($userId)) {
            if ($userId === 'others') {
                $query->whereNotIn('outward_details.user_id', ['web', 'chatbot']);
            } else {
                $query->where('outward_details.user_id', $userId);
            }
        }
        return $query;
    }



    public function html(): HtmlBuilder
    {
        return $this->builder()
                ->setTableId('outward-report-table')
                ->addColumn(['data' => 'sr_no', 'title' => __('Sr. No.')])
                ->addColumn(['data' => 'billdate', 'title' => __('Outward Date'), 'orderable' => true, 'searchable' => true])
                ->addColumn(['data' => 'order_no', 'title' => __('Order No.'), 'orderable' => true, 'searchable' => true])
                ->addColumn(['data' => 'customer_name', 'title' => __('Customer Name'), 'orderable' => true, 'searchable' => true])
                ->addColumn(['data' => 'service_name', 'title' => __('Products'), 'orderable' => true, 'searchable' => true])
                ->addColumn(['data' => 'stage', 'title' => __('Stage'), 'orderable' => true, 'searchable' => true])
                ->addColumn(['data' => 'qty', 'title' => __('Quantity'), 'orderable' => true, 'searchable' => true])
                ->addColumn(['data' => 'rem_qty', 'title' => __('Remaining Quantity'), 'orderable' => true, 'searchable' => true])
                    ->addColumn(['data' => 'currdispatch_qty', 'title' => __('Dispath Quantity'), 'orderable' => true, 'searchable' => true])

                ->minifiedAjax()
                ->orderBy(1,'Asc')
            ->parameters([
                'dom' => 'Bfrtip',
                'buttons' => [
                    [
                        'extend' => 'excelHtml5',
                        'text' => 'Export To Excel',
                        'exportOptions' => [
                            'columns' => ':visible', // ensures all visible columns are exported
                        ],
                        'action' => 'function (e, dt, button, config) {
                            let self = this;
                            let oldStart = dt.settings()[0]._iDisplayStart;

                            dt.one("preXhr", function (e, s, data) {
                                data.start = 0;
                                data.length = -1; // fetch all records
                            });

                            dt.one("xhr", function (e, s, json) {
                                $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config);
                                dt.page.len(10).draw(false); // revert to original paging
                            });

                            dt.ajax.reload();
                        }'
                    ],
                    'reload',
                ],
                'language' => [
                    'emptyTable' => 'No Records Found',
                    'infoEmpty' => '',
                    'zeroRecords' => 'No Records Found',
                ],
                'lengthMenu' => [[100, 200], [100, 200]],

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
        return 'OutwardReport_' . date('YmdHis');
    }
}
