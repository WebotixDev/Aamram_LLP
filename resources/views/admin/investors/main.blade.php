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
                <h4>Investor</h4>
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
                    <li class="breadcrumb-item">Investor</li>
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
                            <a href="{{ route('admin.investors.create') }}" class="btn btn-secondary ms-2 mt-2">{{ __('Add') }}</a>

                        <div class="">
                            <div class="card-body">
                                <div class="">

                                    <table class="dt-responsive table table-bordered dataTable dtr-column" id="example2" aria-describedby="DataTables_Table_3_info">
                                        <thead>
                                            <tr>
                                                <th>Sr.No.</th>
                                                <th>Investor Name</th>
                                                <th>Credit Amount</th>
                                                <th>Debit Amount</th>
                                                <th>Balance</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $records = DB::table('investors_payment')
                                                    ->join('investors_name', 'investors_payment.investor_name', '=', 'investors_name.id')
                                                    ->select('investors_payment.*', 'investors_name.investors_name as real_investor_name')
                                                    ->orderBy('real_investor_name')
                                                    ->get()
                                                    ->groupBy('real_investor_name');
                                            @endphp

                                            @foreach ($records as $investorName => $payments)
                                                @php
                                                    $investorId = $payments->first()->investor_name; // the ID
                                                    $paidSum = DB::table('investors_payment')
                                                        ->where('investor_name', $investorId)
                                                        ->where('type', 'paid')
                                                        ->sum('amt_pay');

                                                    $recieveSum = DB::table('investors_payment')
                                                        ->where('investor_name', $investorId)
                                                        ->where('type', 'receive')
                                                        ->sum('amt_pay');

                                                    $balance = $recieveSum - $paidSum;
                                                    $srNo = 1;
                                                @endphp

                                <tr>
                                    <td>{{ $srNo++ }}</td>
                                    <td style="font-weight: bold; color: #8C3061;">{{ $investorName }}</td> {{-- dark bold --}}
                                    <td style="color: green; font-weight: bold;">{{ $recieveSum }}</td>    {{-- credit: green --}}
                                    <td style="color: red; font-weight: bold;">{{ $paidSum }}</td>          {{-- debit: red --}}
                                    <td style="color: #6f42c1; font-weight: bold;">{{ $balance }}</td>      {{-- balance: purple --}}
                                    <td>
                                        <a href="{{ route('admin.invest', ['investorId' => $investorId]) }}" class="btn btn-primary">{{ __('View') }}</a>
                                    </td>
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
            "order": [[1, 'asc']],
      "lengthMenu": [[ 50, 100, -1], [ 50, 100, "All"]], // Dropdown options for entries per page
            "columnDefs": [{
                "orderable": false,
                "targets": [0]
            }],

        });
    });
</script>
@endsection
