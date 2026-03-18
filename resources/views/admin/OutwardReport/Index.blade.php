@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <style>
    #outward-report-table th:nth-child(1)  {
    min-width: 50px !important;
    max-width: 50px !important;
}

#outward-report-table th:nth-child(2)  {
    min-width: 100px !important;
    max-width: 100px !important;
}

#outward-report-table th:nth-child(3)  {
    min-width: 100px !important;
    max-width: 100px !important;
}

#outward-report-table th:nth-child(4)  {
    min-width: 100px !important;
    max-width: 100px !important;
}

#outward-report-table th:nth-child(5)  {
    min-width: 100px !important;
    max-width: 100px !important;
}

#outward-report-table th:nth-child(6)  {
    min-width: 70px !important;
    max-width: 70px !important;
}

#outward-report-table th:nth-child(7)  {
    min-width: 70px !important;
    max-width: 70px !important;
}
#outward-report-table th:nth-child(8)  {
    min-width: 70px !important;
    max-width: 70px !important;
}


.color-legend {
            margin-bottom: 20px;
            padding: 10px 0p;
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
            margin-top: 10px;
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
                <h4> Outward Report</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item">Outward</li>
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
                <div class="card-block row mt-2 ms-2">
                    <!-- Filter Form Start -->
                    <div class="d-flex flex-wrap align-items-end">

                        <form method="GET" action="{{ route('admin.generate-outward-report.index') }}" id="filter-form">
                            <div class="row form-row">
                                <div class="col-md-4">
                                    <label for="from_date">From Date</label>
                                    <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="to_date">To Date</label>
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary mt-4">Search</button>
                                </div>
                            </div>

                        </form>
                        <div class="color-legend">
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

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script>



    $('.legend-filter').click(function () {
    $('.legend-filter').removeClass('active');
    $(this).addClass('active');

    let userId = $(this).data('user-id');
    if (userId === 'inventory') {
        userId = 'others';
    }

    const from_date = $('#from_date').val();
    const to_date = $('#to_date').val();

    // Redirect to the same page with filters
    const url = new URL(window.location.href);
    url.searchParams.set('user_id', userId);
    if (from_date) url.searchParams.set('from_date', from_date);
    if (to_date) url.searchParams.set('to_date', to_date);
    window.location.href = url.toString();
});

    </script>
@endsection
