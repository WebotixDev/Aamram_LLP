@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">

    <style>
      #productDetailsTable th:nth-child(1)  {
    min-width: 70px !important;
    max-width: 70px !important;
}
   #productDetailsTable th:nth-child(2)  {
    min-width: 100px !important;
    max-width: 100px !important;
}

#productDetailsTable th:nth-child(3)  {
    min-width: 155px !important;
    max-width: 155px !important;
}

#productDetailsTable th:nth-child(4)  {
    min-width: 320px !important;
    max-width: 320px !important;
}

#productDetailsTable th:nth-child(5)  {
    min-width: 100px !important;
    max-width: 100px !important;
}

#productDetailsTable th:nth-child(6)  {
    min-width:80px !important;
    max-width: 80px !important;
}

#productDetailsTable th:nth-child(7)  {
    min-width: 90px !important;
    max-width: 90px !important;
}

#productDetailsTable th:nth-child(8)  {
    min-width: 95px !important;
    max-width: 95px !important;
}
#productDetailsTable th:nth-child(9)  {
    min-width: 80px !important;
    max-width: 80px !important;
}

.dataTables_wrapper table.dataTable th, .dataTables_wrapper table.dataTable td{
    padding: 3px !important;
    vertical-align: top;
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
                <h4> Product Sale Report </h4>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item">Product Sale Report</li>
                    <li class="breadcrumb-item active"> Product Sale Report </li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-block row ps-3 pt-3">

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

                            <div class="form-group col-md-3">
                                <label>Product Name <span class="required" style="color:red;"></span></label>

                                <select class="form-select select2" id="product_name" name="product_name" data-placeholder="Select Product">
                                    <option value="">Select Product</option>
                                    @php
                                        $product = DB::table('products')->get();
                                    @endphp
                                    @foreach ($product as $products)
                                    <option value="{{ $products->id }}" {{ request('product_name') == $products->id ? 'selected' : '' }}>
                                        {{ $products->product_name }}
                                    </option>

                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary mt-4" id="filter-btn">Search</button>
                            </div>
                        </div>

                    </form>

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
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

<script src="{{ asset('assets/js/datatables.min.js') }}"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.2.0/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/vfs_fonts/2.0.1/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

<script>

    function fetchTableData(from_date = null, to_date = null, product_id = null) {
        if ($.fn.DataTable.isDataTable('#productDetailsTable')) {
            $('#productDetailsTable').DataTable().destroy();
        }

        $('#productDetailsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.sale_product.data") }}',
                data: {
                    from_date: from_date,
                    to_date: to_date,
                    product_id: product_id // ← NEW

                }
            },
            columns: [
                { data: 'sr_no', name: 'sr_no' },
                { data: 'billdate', name: 'billdate'},
                { data: 'service_name', name: 'service_name' },
                { data: 'size_name', name: 'size_name' },
                { data: 'stage', name: 'stage' },
                { data: 'qty', name: 'qty' },
                { data: 'rate', name: 'rate' },
                { data: 'amount', name: 'amount' },
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
            lengthMenu: [[500], [500]]
        });
    }

    $(document).ready(function () {
        // Initial fetch
        const from_date = $('#from_date').val();
        const to_date = $('#to_date').val();
        const product_id = $('#product_name').val(); // ← NEW

        fetchTableData(from_date, to_date , product_id);

        $('#filter-btn').click(function (e) {
            e.preventDefault();
            const from_date = $('#from_date').val();
            const to_date = $('#to_date').val();
            const product_id = $('#product_name').val(); // ← NEW

            fetchTableData(from_date, to_date, product_id);
        });


    });
</script>
@endsection
