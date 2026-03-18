@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <style> 
    #product-main-table th:nth-child(1)  {
    min-width: 60px !important;
    max-width: 600px !important;
}

#product-main-table th:nth-child(2)  {
    min-width: 100px !important;
    max-width: 100px !important;
}

#product-main-table th:nth-child(3)  {
    min-width: 90px !important;
    max-width: 90px !important;
}

#product-main-table th:nth-child(4)  {
    min-width: 80px !important;
    max-width: 80px !important;
}

#product-main-table th:nth-child(5)  {
    min-width: 60px !important;
    max-width: 60px !important;
}

#product-main-table th:nth-child(6)  {
    min-width: 70px !important;
    max-width: 70px !important;
}




    </style>
@endsection

@section('main_content')
<div class="container-fluid basic_table">
    <div class="page-title">
        <div class="row">
            <div class="col-sm-6">
                <h4> Product   <a href="{{ route('admin.product.create') }}" class="btn btn-secondary">{{ __('Add') }}</a></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item">Product</li>
                    <li class="breadcrumb-item active">Product</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- <div class="col-xxl-2 col-sm-12 box-col-12">
            <div class="card user-role">
                <div class="card-body border-b-secondary border-2">
                    <div class="upcoming-box">
                        <a href="{{ route('admin.product.create') }}" class="btn btn-secondary">{{ __('Add Product') }}</a>
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
@endsection
