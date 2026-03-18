@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <style>
  #delivery_challan-main-table th:nth-child(1)
{
    min-width: 80px;
    max-width: 80px;
}
#delivery_challan-main-table th:nth-child(2)
 {
    min-width: 80px;
    max-width: 80px;
}
#delivery_challan-main-table th:nth-child(3)
 {
    min-width: 60px;
    max-width: 60px;
}
#delivery_challan-main-table th:nth-child(4) {
    min-width: 80px;
    max-width: 80px;
}
#delivery_challan-main-table th:nth-child(5){
    min-width: 80px;
    max-width: 80px;
}
#delivery_challan-main-table th:nth-child(6) {
    min-width: 80px;
    max-width: 80px;
}
#delivery_challan-main-table th:nth-child(7) {
    min-width: 80px;
    max-width: 80px;
}
.dataTables_wrapper table.dataTable th, .dataTables_wrapper table.dataTable td{
    padding: 3px !important;
    /* vertical-align: top; */
}

     </style>
@endsection

@section('main_content')
<div class="container-fluid basic_table">
    <div class="page-title">
        <div class="row">
            <div class="col-sm-6">
                <h4>Delivery Challan <a href="{{ route('admin.Delivery_Challan.create') }}" class="btn btn-secondary">{{ __('Add') }}</a></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item">Delivery </li>
                    <li class="breadcrumb-item active">Challan </li>
                </ol>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- <div class="col-xxl-2 col-sm-12 box-col-12">
            <div class="card user-role">
                <div class="card-body border-b-secondary border-2">
                    <div class="upcoming-box">
                        <a href="{{ route('admin.customer.create') }}" class="btn btn-secondary">{{ __('Add Customer') }}</a>
                    </div>
                    <ul class="bubbles role role-user">
                        <li class="bubble"></li>
                        <li class="bubble"></li>
                        <li class="bubble"></li>
                        <li class="bubble"></li>
                        <li class="bubble"></li>
                        <li class="bubble"></li>
                        <li class="bubble"></li>
                        <li class="bubble"></li>
                        <li class="bubble"></li>
                    </ul>
                </div>
            </div>
        </div> -->
        <div class="col-sm-12">
            <div class="card">
                <div class="card-block row">
                    <div class="profile-table">
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


@endsection
