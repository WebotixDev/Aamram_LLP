@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <style>
    #farm_inward-main-table th:nth-child(1)  {
    min-width: 100px !important;
    max-width: 100px !important;
}

#farm_inward-main-table th:nth-child(2)  {
    min-width: 100px !important;
    max-width: 100px !important;
}

#farm_inward-main-table th:nth-child(3)  {
    min-width: 100px !important;
    max-width: 100px !important;
}

#farm_inward-main-table th:nth-child(4)  {
    min-width: 100px !important;
    max-width: 100px !important;
}

#farm_inward-main-table th:nth-child(5)  {
    min-width: 100px !important;
    max-width: 100px !important;
}

#farm_inward-main-table th:nth-child(7)  {
    min-width: 100px !important;
    max-width: 100px !important;
}
.dataTables_wrapper table.dataTable th, .dataTables_wrapper table.dataTable td{
    padding: 3px !important;
    vertical-align: top;
}

#custom-table {
        width: 100%;
        border-collapse: collapse;
    }
    #custom-table td {
        border: 1px solid #000;
        /* padding: 10px; */
        text-align: center;
        background-color: #f8f8f8;
        font-weight: bold;
        width:50px;
    }
    </style>
@endsection

@section('main_content')
<div class="container-fluid basic_table">
    <div class="page-title" style="padding:0px;">
        <div class="row">
            <div class="col-sm-6">
                <h4> Farm Stock
                        <a href="{{ route('admin.farm_inward.create') }}" class="btn btn-secondary">{{ __('Add') }}</a>
                    </h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item">Farm Stock</li>
                    <li class="breadcrumb-item active"></li>
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
                <div class="card-block row">
                    <div class="farm-table">
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
$(document).ready(function() {

if ($.fn.dataTable.isDataTable('#farm_farm_inward-main-table')) {
    $('#farm_farm_inward-main-table').DataTable().clear().destroy();
}
$('#farm_farm_inward-main-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route("admin.inward.data") }}',
    columns: [
        { data: 'sr_no', name: 'sr_no' },
       // { data: 'Invoicenumber', name: 'Invoicenumber' },
        { data: 'PurchaseDate', name: 'PurchaseDate' },
        { data: 'details', name: 'details' },
        { data: 'Tquantity', name: 'Tquantity' },

        { data: 'action', name: 'action', orderable: false, searchable: false }
    ],
    drawCallback: function(settings) {
        feather.replace();  // If you are using Feather icons
    }
});
});



</script>
@endsection
