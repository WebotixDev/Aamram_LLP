<?php

namespace App\DataTables;

use App\Models\Sale_payment;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class Sale_PaymentDataTable extends DataTable
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
                        ->orWhereRaw('LOWER(purchase_payments.PurchaseDate) LIKE ?', ["%$search%"]);
                });
            }
        })
            ->setRowId('ID')
            ->editColumn('PurchaseDate', function ($row) {
                return \Carbon\Carbon::parse($row->PurchaseDate)->format('d-m-Y'); // Formatting Purchase Date
            })
            ->editColumn('amt_pay', function ($row) {
                return number_format($row->amt_pay, 2); // Formatting payment amount
            })
            ->editColumn('pending_amt', function ($row) {
                return number_format($row->pending_amt, 2); // Formatting pending amount
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
            // ->addColumn('details', function ($row) {
            //     // Get the related details (payment details)
            //     $details = $row->details->pluck('amount')->toArray();
            //     return implode(', ', $details); // Format the details as a comma-separated string
            // })

            ->addColumn('action', function ($row) {
                if (!in_array($row->user_id, ['web', 'chatbot','razorpay'])) {
                    $actionButtons = view('admin.inc.action', [
                        'edit' => 'admin.sale_payment.edit',
                        'delete' => 'admin.sale_payment.destroy',
                        'data' => $row
                    ])->render();
                } else {
                    $actionButtons = ''; // Hide buttons by setting an empty string
                }


                $printButton = '<a href="' . route('admin.sale_payment.print', $row->id) . '" target="_blank" style="color: #fff; text-decoration: none;">
<i class="fa fa-print" style="color: #0f5183; font-size: 18px;"></i>  </a>';
                            


      $buttons = '<div style="display: flex; align-items: center; gap: 3px;">' 
                . $actionButtons . $printButton . 
               '</div>';

    return $buttons;
            })

            ->rawColumns(['action', 'PurchaseDate', 'amt_pay', 'pending_amt' ,'sr_no']);
    }

    /**
     * Get the query source of dataTable.
     */
 public function query(Sale_payment $model): QueryBuilder
    {
        $fromDate = request()->get('from_date');
        $toDate = request()->get('to_date');
        $userId = request()->get('user_id');
    $season = session('selected_season');
    // $season = session('selected_season', date('Y'));

        $query = $model->newQuery()
        ->with('details') // Optional
        ->join('customers', 'customers.id', '=', 'purchase_payments.customer_name') // Adjust foreign key if necessary
        ->select(
            'purchase_payments.*',
            'customers.customer_name as customer_name_alias' // Change the alias to something unique
        )
         ->where('purchase_payments.season', $season); // Add this line


        if ($fromDate && $toDate) {
            $query->whereBetween('PurchaseDate', [$fromDate, $toDate]);
        }
 if (!empty($userId)) {
            if ($userId === 'others') {
                $query->whereNotIn('purchase_payments.user_id', ['web', 'chatbot']);
            } else {
                $query->where('purchase_payments.user_id', $userId);
            }
        }
                $query->orderBy('purchase_payments.PurchaseDate', 'desc');

        return $query;
    }


    /**
     * Optional method if you want to use the HTML builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('sale-payment-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1, 'desc') // Column index 1 is billdate
            ->selectStyleSingle()
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
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
             Column::make('sr_no')->title(__('Sr. No.'))->orderable(true)->searchable(true),
            Column::make('ReceiptNo')->title(__('Recipt Number'))->orderable(true)->searchable(true),
            Column::make('PurchaseDate')->title(__('Purchase Date'))->orderable(true)->searchable(true),
            Column::make('customer_name_alias')->title(__('Customer Name'))->orderable(true)->searchable(true),
            Column::make('amt_pay')->title(__('Amount Paid'))->orderable(true)->searchable(true),
            Column::make('mode')->title(__('Mode'))->orderable(true)->searchable(true),
            Column::computed('action')
                ->title(__('Action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Sale_Payment_' . date('YmdHis');
    }
}
