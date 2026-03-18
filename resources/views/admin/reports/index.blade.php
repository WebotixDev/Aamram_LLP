@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <style>
     #inward-reports-table th:nth-child(1)  {
    min-width: 75px !important;
    max-width: 75px !important;
}

#inward-reports-table th:nth-child(2)  {
    min-width: 60px !important;
    max-width: 65px !important;
}

#inward-reports-table th:nth-child(3)  {
    min-width: 75px !important;
    max-width: 75px !important;
}

#inward-reports-table th:nth-child(4)  {
    min-width: 165px !important;
    max-width: 165px !important;
}

#inward-reports-table th:nth-child(5)  {
    min-width: 70px !important;
    max-width: 70px !important;
}

#inward-reports-table th:nth-child(6)  {
    min-width: 75px !important;
    max-width: 75px !important;
}

#inward-reports-table th:nth-child(7)  {
    min-width: 65px !important;
    max-width: 65px !important;
}


.dataTables_wrapper table.dataTable th, .dataTables_wrapper table.dataTable td{
    padding: 3px !important;
    vertical-align: top;
}
    </style>
@endsection

@section('main_content')
<div class="container-fluid basic_table">
    <div class="page-title">
        <div class="row">
            <div class="col-sm-6">
                <h4> Inward Report</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item">Inward</li>
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

                        <form method="GET" action="{{ route('admin.reports.index') }}" id="filter-form">
                            <div class="row form-row">
                                <div class="col-md-2">
                                    <label for="from_date">From Date</label>
                                    <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="to_date">To Date</label>
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary mt-4" id="filter-btn">Search</button>
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
<script>
$(document).ready(function () {
    function fetchTableData(from_date, to_date) {
        if ($.fn.dataTable.isDataTable('#inward-reports-table')) {
            $('#inward-reports-table').DataTable().clear().destroy();
        }

        $('#inward-reports-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.inward.reports.data") }}',
                data: function (d) {
                    d.from_date = from_date;
                    d.to_date = to_date;
                }
            },
            columns: [
                { data: 'sr_no', name: 'sr_no' },
            { data: 'billdate', name: 'billdate' },
             { data: 'product_name', name: 'product_name' },
            { data: 'productsizes', name: 'productsizes' },
            { data: 'rate', name: 'rate' },
             { data: 'Quantity', name: 'Quantity' },

            // { data: 'details', name: 'details', orderable: false, searchable: false },
           { data: 'Tquantity', name: 'Tquantity',orderable: true, searchable: true },

            ],
            lengthMenu: [[100], [100]],
            drawCallback: function (settings) {
                feather.replace();  // If using Feather icons
            },
             buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                }
            ]
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
