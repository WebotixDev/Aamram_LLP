@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">

    <style>
 /* #sale-order-report-table th:nth-child(1)
{
    min-width: 70px;
    max-width: 70px;
}
#sale-order-report-table th:nth-child(2)
 {
    min-width: 80px;
    max-width: 80px;
}
#sale-order-report-table th:nth-child(3)
 {
    min-width: 60px;
    max-width: 60px;
}
#sale-order-report-table th:nth-child(4) {
    min-width: 100px;
    max-width: 100px;
}
#sale-order-report-table th:nth-child(5){
    min-width: 80px;
    max-width: 80px;
}
#sale-order-report-table th:nth-child(6) {
    min-width: 50px;
    max-width: 50px;
}
#sale-order-report-table th:nth-child(7) {
    min-width: 80px;
    max-width: 80px;
}
#sale-order-report-table th:nth-child(8) {
    min-width: 80px;
    max-width: 80px;
}
#sale-order-report-table th:nth-child(9) {
    min-width: 50px;
    max-width: 50px;
}
.dataTables_wrapper table.dataTable th, .dataTables_wrapper table.dataTable td{
    padding: 3px !important;
    vertical-align: top;
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

button.dt-button:first-child, div.dt-button:first-child, a.dt-button:first-child, input.dt-button:first-child {
    margin-left: 7px !important;
}

.legend-item.active {
    font-weight: bold;
    text-decoration: underline;
} */


 #sale-order-report-table th:nth-child(1)
{
    min-width: 50px;
    max-width: 50px;
}
#sale-order-report-table th:nth-child(2)
 {
    min-width: 80px;
    max-width: 80px;
}
#sale-order-report-table th:nth-child(3)
 {
    min-width: 140px;
    max-width: 140px;
}
#sale-order-report-table th:nth-child(4) {
    min-width: 110px !important;
    max-width: 110px !important;
}
#sale-order-report-table th:nth-child(5) {
    min-width: 110px;
    max-width: 110px;
}
#sale-order-report-table th:nth-child(6){
    min-width: 190px;
    max-width: 190px;
}
#sale-order-report-table th:nth-child(7) {
    min-width: 60px;
    max-width: 60px;
}
#sale-order-report-table th:nth-child(8) {
    min-width: 60px;
    max-width: 60px;
}
#sale-order-report-table th:nth-child(9) {
    min-width: 80px;
    max-width: 80px;
}
#sale-order-report-table th:nth-child(10) {
    min-width: 80px;
    max-width: 80px;
}
#sale-order-report-table th:nth-child(11) {
    min-width: 50px;
    max-width: 50px;
}
.dataTables_wrapper table.dataTable th, .dataTables_wrapper table.dataTable td{
    padding: 3px !important;
    vertical-align: top;
}
.dataTables_wrapper table.dataTable th, .dataTables_wrapper table.dataTable td{
    padding: 3px !important;
    vertical-align: top;
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

button.dt-button:first-child, div.dt-button:first-child, a.dt-button:first-child, input.dt-button:first-child {
    margin-left: 7px !important;
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
                <h4> Sale Order Report</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item">Sale_Order</li>
                    <li class="breadcrumb-item active">Report</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-2 col-sm-12 box-col-12">
            <div class="user-role">
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
                <div class="card-block row mt-5 ms-2">
                    <!-- Filter Form Start -->

                    <form method="GET" id="filter-form">
                        <div class="row form-row">
                            <div class="col-md-2">
                                <label for="from_date">From Date</label>
                                <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="to_date">To Date</label>
                                <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary mt-4" id="filter-btn">Search</button>
                            </div>
                            <div class="col-md-2" style="padding-inline-start: 35px">
                                <button id="labour" class="btn btn-secondary mt-4"> Labour </button>
                            </div>
                            <div class="col-sm-4 color-legend pt-4">
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

                        </div>

                    </form>
                    <!-- Filter Form End -->

                    <div class="district-table mt-3">
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



<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.2.0/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vfs_fonts/2.0.1/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>


<script>
    let selectedUserId = null;

    function fetchTableData(from_date = null, to_date = null, user_id = null) {
        if ($.fn.DataTable.isDataTable('#sale-order-report-table')) {
            $('#sale-order-report-table').DataTable().destroy();
        }

        $('#sale-order-report-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.sale_order-report-.data") }}',
                data: {
                    from_date: from_date,
                    to_date: to_date,
                    user_id: user_id
                }
            },
            // columns: [
            //     { data: 'sr_no', name: 'sr_no' },
            //     { data: 'billdate', name: 'billdate' },
            //     { data: 'wholesaler', name: 'Wholesaler' },
            //     { data: 'customer_name', name: 'customer_name' },
            //      { data: 'mobile_no', name: 'mobile_no' },
            //       { data: 'address', name: 'address' },
            //     { data: 'details', name: 'details' },
            //     { data: 'Tamount', name: 'Tamount' },
            // ],
            
              columns: [
                { data: 'sr_no', name: 'sr_no', orderable: false, searchable: false },
                { data: 'billdate', name: 'billdate' },
                { data: 'customer_name', name: 'customer_display_name' },
                { data: 'order_address', name: 'order_address' },
                { data: 'product_name', name: 'product_name' },
                { data: 'size_name', name: 'size_name' },
                { data: 'stage', name: 'stage' },
                { data: 'qty', name: 'qty' },
                // { data: 'rate', name: 'rate' },
                { data: 'amount', name: 'amount' },
                { data: 'Tamount', name: 'Tamount' },
               { data: 'wholesaler_name', name: 'Wholesaler' },
            ],
            drawCallback: function () {
                feather.replace();
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
            lengthMenu: [[100], [100]]
        });
    }

    $(document).ready(function () {
        // Initial fetch
        const from_date = $('#from_date').val();
        const to_date = $('#to_date').val();
        fetchTableData(from_date, to_date, selectedUserId);

        $('#filter-btn').click(function (e) {
            e.preventDefault();
            const from_date = $('#from_date').val();
            const to_date = $('#to_date').val();
            fetchTableData(from_date, to_date, selectedUserId);
        });



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
