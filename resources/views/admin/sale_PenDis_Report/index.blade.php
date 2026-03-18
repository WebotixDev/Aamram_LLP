@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <style>
  #sale_PenDis_Report-table th:nth-child(1)
{
    min-width: 70px;
    max-width: 70px;
}
#sale_PenDis_Report-table th:nth-child(2)
 {
    min-width: 80px;
    max-width: 80px;
}
#sale_PenDis_Report-table th:nth-child(3)
 {
    min-width: 60px;
    max-width: 60px;
}
#sale_PenDis_Report-table th:nth-child(4) {
    min-width: 100px;
    max-width: 100px;
}
#sale_PenDis_Report-table th:nth-child(5){
    min-width: 60px;
    max-width: 60px;
}
#sale_PenDis_Report-table th:nth-child(6) {
    min-width: 80px;
    max-width: 80px;
}
#sale_PenDis_Report-table th:nth-child(7) {
    min-width: 80px;
    max-width: 80px;
}
#sale_PenDis_Report-table th:nth-child(8) {
    min-width: 80px;
    max-width: 80px;
}
#sale_PenDis_Report-table th:nth-child(9) {
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
    </style>
@endsection

@section('main_content')
<div class="container-fluid basic_table">
    <div class="page-title">
        <div class="row">
            <div class="col-sm-6">
                <h4>Pending Dispatch</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item">Sale_PenDis </li>
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

<script>


$(document).ready(function () {
    function fetchTableData(from_date, to_date) {
        if ($.fn.dataTable.isDataTable('#sale_PenDis_Report-table')) {
            $('#sale_PenDis_Report-table').DataTable().clear().destroy();
        }

        $('#sale_PenDis_Report-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.sale_PenDis_Report-.data") }}',
                data: function (d) {
                    d.from_date = from_date;
                    d.to_date = to_date;
                }
            },
            columns: [
                { data: 'sr_no', name: 'sr_no' },
            { data: 'billdate', name: 'billdate' },
            //{ data: 'Invoicenumber', name: 'Invoicenumber' },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'details', name: 'details' },
            { data: 'Tamount', name: 'Tamount' },
            ],
            drawCallback: function (settings) {
                feather.replace();  // If using Feather icons
            }
        });
    }

    // Initial Load
    fetchTableData($('#from_date').val(), $('#to_date').val());

    // Apply filter on button click
    $('#filter-btn').click(function (e) {
        e.preventDefault();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();
        fetchTableData(from_date, to_date);
    });
});
</script>
@endsection
