@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('main_content')
<div class="container-fluid basic_table">
    <div class="page-title">
        <div class="row">
            <div class="col-sm-6">
                <h4> City  <a href="{{ route('admin.city.create') }}" class="btn btn-secondary">{{ __('Add') }}</a></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item">City</li>
                    <li class="breadcrumb-item active">City</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- <div class="col-xxl-2 col-sm-12 box-col-12">
            <div class="card user-role">
                <div class="card-body border-b-secondary border-2">
                    <div class="upcoming-box">
                        <a href="{{ route('admin.city.create') }}" class="btn btn-secondary">{{ __('Add City') }}</a>
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
                    <div class="city-table">
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
