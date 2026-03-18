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
                <h4> Ledger Report</h4>
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
                    <li class="breadcrumb-item">Ledger Report</li>
                    <li class="breadcrumb-item active">Report</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-block row">
                    @php

    // Fetch distinct customer names from the customers table based on sale_orderdetails
    $customers = DB::table('sale_orderdetails')
        ->join('customers', 'sale_orderdetails.customer_name', '=', 'customers.id')
        ->select('customers.id', 'customers.customer_name', 'sale_orderdetails.PurchaseDate')
        ->distinct()
        ->get();

    // Get selected customer and date range from request
    $selectedCustomer = request()->get('customer_name');
    $fromDate = request()->get('from_date');
    $toDate = request()->get('to_date');

    // Fetch purchase payments filtered by selected customer and date range
    $ordersQuery = DB::table('purchase_payments')
        ->join('customers', 'purchase_payments.customer_name', '=', 'customers.id')
        ->select('purchase_payments.*', 'customers.customer_name');

    if (!empty($selectedCustomer)) {
        $ordersQuery->where('customers.id', $selectedCustomer);
    }

    if (!empty($fromDate) && !empty($toDate)) {
        $ordersQuery->whereBetween('purchase_payments.PurchaseDate', [$fromDate, $toDate]); // Fixed table reference
    }

    $orders = $ordersQuery->get();

    $fromDate = "2025-02-01";

    // Fetch opening balance (Total amount before fromDate)
    $sale_sum = DB::table('sale_orderdetails')
        ->where('customer_name', $selectedCustomer)
        ->where('PurchaseDate', '<', $fromDate) // Filter records before $fromDate
        ->get();

    // Fetch opening payments (Total payments before fromDate)
    $purchase_sum = DB::table('purchase_payments')
        ->where('customer_name', $selectedCustomer)
        ->where('PurchaseDate', '<', $fromDate) // Filter records before $fromDate
        ->get();


                @endphp




                    <div class="batch-table">
                        <div class="row">
                        <div class="col-md-12 pt-3 ps-3">

                            <form method="GET" id="filter-form" >
                                <div class="row align-items-end ps-2">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="from_date">From Date</label>
                                            <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="to_date">To Date</label>
                                            <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="customer_name">Customer</label>
                                            <select name="customer_name" id="customer_name" class="form-control">
                                                <option value=""> Customers</option>
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}" {{ request('customer_name') == $customer->id ? 'selected' : '' }}>
                                                        {{ $customer->customer_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">Search</button>
                                    </div>
                                </div>
                            </form>

                        <div class="">
                            <div class="card-body">
                                <div class="">

                                <table class="dt-responsive table table-bordered dataTable dtr-column" id="example2" aria-describedby="DataTables_Table_3_info">
                        <thead>
                            <tr>
                                <th>Sr.No.</th>
                                <th>Date</th>
                                <th>Order No</th>
                                <th>Perticular</th>
                                <th>Credit</th>
                                <th>Balance</th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th colspan="4"><strong>Opening Balance</strong></th>
                                <td colspan="2"><strong>{{ number_format(100) }}</strong></td>
                            </tr>

                            @php
                                $sr = 1;
                            @endphp
                                <tr>
                                    <td>{{ $sr++ }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sale_sum->PurchaseDate)->format('d-m-Y') }}</td>
                                    <td>Invoice #{{ $sale_sum->Invoicenumber }}</td>

                                    @foreach($purchase_sum as $purchase)
                                        <td>{{ $purchase->payment_method }} </td>
                                        <td>{{ number_format($purchase->totalvalue) }}</td>
                                    @endforeach

                                    <td>{{ number_format($transaction->totalvalue, 2) }}</td>
                                </tr>
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
