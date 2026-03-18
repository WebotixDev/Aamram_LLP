@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <style>
          #sale-payment-table th:nth-child(1)
{
    min-width: 60px;
    max-width: 60px;
}


 #sale-payment-table th:nth-child(2)
{
    min-width: 80px;
    max-width: 80px;
} 
 #sale-payment-table th:nth-child(3)
{
    min-width: 80px;
    max-width: 80px;
} 

 #sale-payment-table th:nth-child(4)
{
    min-width: 180px;
    max-width: 180px;
} 
 #sale-payment-table th:nth-child(5)
{
    min-width: 120px;
    max-width: 120px;
} 
 #sale-payment-table th:nth-child(6)
{
    min-width: 70px;
    max-width: 70px;
} 

 #sale-payment-table th:nth-child(7)
{
    min-width: 70px;
    max-width: 70px;
} 

.color-legend {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 8px;
        }
        .color-legend .legend-item {
            display: inline-block;
            margin-right: 15px;
            font-size: 16px;
        }
        .color-legend .legend-item span {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
                .legend-item.active {
    font-weight: bold;
    text-decoration: underline;
}
    </style>
@endsection

@section('main_content')
<div class="container-fluid basic_table">
    <div class="page-title">
        <div class="row">
            <div class="col-sm-6">
                <h4> Sale Payment
                        <a href="{{ route('admin.sale_payment.create') }}" class="btn btn-secondary">{{ __('Add') }}</a>
                    </h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item">Sale Payment </li>
                    <li class="breadcrumb-item active">Sale Payment </li>
                </ol>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-xxl-2 col-sm-12 box-col-12">
            <div class=" user-role">
                <div class="">

                    <!-- <ul class="bubbles role role-user">
                        <li class="bubble"></li>
                        <li class="bubble"></li>
                        <li class="bubble"></li>
                        <li class="bubble"></li>
                        <li class="bubble"></li>
                        <li class="bubble"></li>
                        <li class="bubble"></li>
                        <li class="bubble"></li>
                        <li class="bubble"></li>
                    </ul> -->
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">
                              <div class="card-block row ps-3 pt-3">
                    <div class="d-flex flex-wrap align-items-end">
                        <form method="GET" id="filter-form" class="d-flex flex-wrap gap-3">
                            <div>
                                <label for="from_date">From Date</label>
                                <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>
                            <div>
                                <label for="to_date">To Date</label>
                                <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary mt-4" id="filter-btn">Search</button>
                            </div>

                         <div class="color-legend pt-4">
                            <div class="legend-item legend-filter" data-user-id="inventory">
                                <span style="background-color: blue;"></span> Inventory
                            </div>
                            <div class="legend-item legend-filter" data-user-id="web">
                                <span style="background-color: rgb(247, 223, 9);"></span> Website
                            </div>
                            <div class="legend-item legend-filter" data-user-id="chatbot">
                                <span style="background-color: rgb(22, 236, 22);"></span> ChatBot
                            </div>
                        </div>
                        </form>
                    </div>


                    <div class="district-table">
                        <div class="table-responsive p-3">
                        {!! $dataTable->table() !!}

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
{!! $dataTable->scripts() !!}

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>



<script>
    let selectedUserId = null;

    $(document).ready(function () {
        function fetchTableData(from_date, to_date, user_id = null) {
            if ($.fn.dataTable.isDataTable('#sale-payment-table')) {
                $('#sale-payment-table').DataTable().clear().destroy();
            }
            $('#sale-payment-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.sale_payment.data") }}',
                    data: function (d) {
                        d.from_date = from_date;
                        d.to_date = to_date;
                        d.user_id = user_id; // pass user_id if needed by backend
                    }
                },
                columns: [
                     { data: 'sr_no', name: 'sr_no' },
                    { data: 'ReceiptNo', name: 'ReceiptNo',  orderable: true, searchable: false },
                    { data: 'PurchaseDate', name: 'PurchaseDate', orderable: true, searchable: false },
                    { data: 'customer_name_alias', name: 'customer_name', orderable: true, searchable: false },
                    { data: 'amt_pay', name: 'amt_pay', orderable: true, searchable: false },
                    { data: 'mode', name: 'mode', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                drawCallback: function (settings) {
                    feather.replace();  // If using Feather icons
                },
                dom: 'lBfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'Export to Excel',
                        exportOptions: {
                            columns: ':not(:last-child)'
                        }
                    }
                ],
                lengthMenu: [[100, 200], [100, 200]],
            });
        }

        // Initial Load
        fetchTableData($('#from_date').val(), $('#to_date').val());

        // Filter by date
        $('#filter-btn').click(function (e) {
            e.preventDefault();
            const from_date = $('#from_date').val();
            const to_date = $('#to_date').val();
            fetchTableData(from_date, to_date);
        });

        // Filter by user
        $('.legend-filter').click(function () {
            $('.legend-filter').removeClass('active');
            $(this).addClass('active');

            let userId = $(this).data('user-id');
            if (userId === 'inventory') {
                userId = 'others';
            }

            selectedUserId = userId;

            const from_date = $('#from_date').val();
            const to_date = $('#to_date').val();
            fetchTableData(from_date, to_date, selectedUserId);
        });
    });
    </script>

@endsection
