@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

@endsection

@section('main_content')
<div class="container-fluid basic_table">
    <div class="page-title">
        <div class="row">
        <div class="col-sm-6">
                <h4> Outstanding Report</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="breadcrumb-item">Outstanding Report</li>
                    <li class="breadcrumb-item active">Report</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-block row">

                    <div class="batch-table">
                        <div class="row">
                        <div class="col-md-12">
                        <div class="">
                            <div class="card-body">
                                <div class="">

                                <table class="dt-responsive table table-bordered dataTable dtr-column" id="example2" aria-describedby="DataTables_Table_3_info">
                        <thead>
                            <tr>
                                <th>Sr.No.</th>
                                <th>Customer Name</th>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Pending Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php


$records = DB::table('customers')
    ->leftJoin(
        DB::raw('(
            SELECT customer_name, wholesaler, SUM(Tamount) as total_amount, SUM(amt_pay) as sale_payments
            FROM sale_orderdetails
            GROUP BY customer_name, wholesaler
        ) as sales'),
        'customers.id',
        '=',
        'sales.customer_name'
    )
    // Wholesaler info
    ->leftJoin('customers as wholesalers', 'sales.wholesaler', '=', 'wholesalers.id')
    ->leftJoin(
        DB::raw('(
            SELECT customer_name, SUM(amt_pay) as total_amt_pay, SUM(cheque_amt) as total_cheque_amt
            FROM purchase_payments
            GROUP BY customer_name
        ) as purchases'),
        'customers.id',
        '=',
        'purchases.customer_name'
    )
    ->select(
        DB::raw("CONCAT(
            customers.customer_name, ' (', customers.mobile_no, ')',
            CASE
                WHEN wholesalers.customer_name IS NOT NULL AND wholesalers.customer_name != ''
                THEN CONCAT(' - <b style=\"color:red\">', wholesalers.customer_name, ' (', wholesalers.mobile_no, ')</b>')
                ELSE ''
            END
        ) as customer_display_name"),
        DB::raw('COALESCE(sales.total_amount, 0) as total_amount'),
        DB::raw('COALESCE(purchases.total_amt_pay, 0) + COALESCE(purchases.total_cheque_amt, 0) as total_payments'),
        DB::raw('COALESCE(sales.total_amount, 0) - (COALESCE(purchases.total_amt_pay, 0) + COALESCE(purchases.total_cheque_amt, 0)) as pending_amount')
    )
    ->whereRaw('COALESCE(sales.total_amount, 0) > 0')
    ->whereRaw('(COALESCE(sales.total_amount, 0) - (COALESCE(purchases.total_amt_pay, 0) + COALESCE(purchases.total_cheque_amt, 0))) > 0')
    ->orderBy('customer_display_name', 'asc')
    ->get();
    
@endphp




                            @foreach ($records as $index => $record) <!-- Use $index to ensure correct order -->
                                <tr>
                                    <td>{{ $index + 1 }}</td> <!-- Serial number based on index -->
                                    <td>{!! $record->customer_display_name !!}</td>
                                    <td>{{ number_format($record->total_amount, 2) }}</td>
                                    <td>{{ number_format($record->total_payments, 2) }}</td>
                                    <td>{{ number_format($record->pending_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/datatables.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function() {
        $('#example2').DataTable({
            "responsive": true,
            "paging": true,
            "searching": true,
            "ordering": true,
            "order": [[1, 'asc']],
      "lengthMenu": [[ 50, 100, -1], [ 50, 100, "All"]], // Dropdown options for entries per page
            "columnDefs": [{
                "orderable": false,
                "targets": [0]
            }],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    titleAttr: 'Export table to Excel',
                    className: 'btn btn-success'
                }
            ]
        });
    });
</script>
@endsection
