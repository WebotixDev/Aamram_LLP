<?php

namespace App\DataTables;

use Carbon\Carbon;
use App\Models\outward_details;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;

class OutwardDataTable extends DataTable
{
    /**
     * Build the DataTable class.
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
                            ->orWhereRaw('LOWER(products.product_name) LIKE ?', ["%$search%"])
                             ->orWhereRaw('LOWER(sale_orderdetails.order_address) LIKE ?', ["%$search%"]);

                    });
                }
            })
            ->editColumn('billdate', function ($row) {
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
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="row-checkbox" name="selected_rows[]" value="' . $row->id . '">';
            })
            ->addColumn('Show', function ($row) {
                return '<a href="' . route('admin.generate-qr', $row->id) . '" class="btn btn-sm btn-primary">QR</a>';
            })
            ->editColumn('action', function ($row) {
                if (!in_array($row->user_id, ['web', 'chatbot'])) {
                    return view('admin.inc.action', [
                        'edit'   => 'admin.outward.edit',
                        'delete' => 'admin.outward.destroy',
                        'data'   => $row
                    ]);
                }
                return '';
            })
            ->rawColumns(['checkbox', 'action', 'Show', 'sr_no']);
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
            ->leftJoin('products', 'outward_details.services', '=', 'products.id')
            ->leftJoin('product_details', 'outward_details.size', '=', 'product_details.id')
            ->leftJoin('sale_orderdetails', 'outward_details.order_no', '=', 'sale_orderdetails.id')
           ->where('outward_details.season', $season)

             ->where('flag', '!=', 1) // Exclude records where flag = 1
            ->select([
                'outward_details.*',
               'sale_orderdetails.order_address',
                'customers.customer_name',
                'products.product_name as service_name',
                'product_details.product_size as size_name',

            ]);

                  if (request()->has('from_date') && request()->has('to_date')) {
                $query->whereBetween('outward_details.billdate', [
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
    /**
     * HTML Builder for DataTable.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('dataTable')
            ->addColumn(['data' => 'checkbox', 'title' => '<input type="checkbox" id="select-all">', 'orderable' => false, 'searchable' => false])
            ->addColumn(['data' => 'sr_no', 'title' => __('Sr. No.')])
            ->addColumn(['data' => 'billdate', 'title' => __('Date'), 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'order_no', 'title' => __('Order No'), 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'customer_name', 'title' => __('Customer Name'), 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'service_name', 'title' => __('Products'), 'orderable' => true, 'searchable' => true])
             ->addColumn(['data' => 'size_name', 'title' => __('Products'), 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'stage', 'title' => __('Stage'), 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'qty', 'title' => __('Qty'), 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'currdispatch_qty', 'title' => __('Dis Qty'), 'orderable' => true, 'searchable' => true])
            ->addColumn(['data' => 'action', 'title' => __('Action'), 'orderable' => false, 'searchable' => false])
            ->addColumn(['data' => 'Show', 'title' => __('QR'), 'orderable' => false, 'searchable' => false])
           ->addColumn(['data' => 'order_address', 'title' => __('Address'), 'orderable' => true, 'searchable' => true])

            ->minifiedAjax()
            ->orderBy(1, 'asc')
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
                                'lengthMenu' => [[100, 200], [100, 200]],

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
     * Filename for export.
     */
    protected function filename(): string
    {
        return 'OutwardDetails_' . date('YmdHis');
    }
}
