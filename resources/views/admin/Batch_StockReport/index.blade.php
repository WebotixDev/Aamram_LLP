@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

@endsection

@section('main_content')
<div class="container-fluid basic_table">
    <div class="page-title">
        <div class="row">
        <div class="col-sm-6">
                <h4>Batch-Wise Stock Report</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg>
                        </a>
                    </li>
                    <li class="breadcrumb-item">Batch Stock Report</li>
                    <li class="breadcrumb-item active">Report</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-block row">
                    @php
                        use Illuminate\Support\Facades\DB;

                        $batchIds = DB::table('purchase_product')
                            ->select('batch_id')
                            ->distinct()
                            ->get();

                        $selectedBatchId = request()->get('batch_id');

                        $records = DB::table('purchase_product')
                            ->select('services', DB::raw('COUNT(id) as product_count'), 'size')
                            ->where('complete_flag', 0)
                            ->where('batch_id', $selectedBatchId)
                            ->groupBy('services', 'size')
                            ->get();

                        $currentDate = date('Y-m-d');
                    @endphp

                    <div class="batch-table">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body pt-3 ps-3">
                                    <form method="GET" action="{{ url()->current() }}">
                                        <label for="batch_id">Select Batch ID:</label>
                                        <select name="batch_id" id="batch_id">
                                            <option value="">Select Batch ID </option>
                                            @foreach ($batchIds as $batch)
                                                <option value="{{ $batch->batch_id }}" {{ $selectedBatchId == $batch->batch_id ? 'selected' : '' }}>
                                                    {{ $batch->batch_id }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit">Search</button>
                                    </form>

                                    <div class="">
                                        <table class="dt-responsive table table-bordered dataTable dtr-column" id="example1">
                                            <thead>
                                                <tr>
                                                    <th>Sr. No.</th>
                                                    <th>Product</th>
                                                    <th>Raw</th>
                                                    <th>Semi Ripe</th>
                                                    <th>Ripe</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($records as $record)
                                                    @php
                                                        $service = $record->services;
                                                        $size = $record->size;

                                                        $product = DB::table('products')->where('id', $service)->first();
                                                        $productName = $product ? $product->product_name : 'Unknown';

                                                        $sizeDetails = DB::table('product_details')->where('id', $size)->first();
                                                        $sizeName = $sizeDetails ? $sizeDetails->product_size : 'Unknown';

                                                        $rawStock = \App\Helpers\Helpers::getstockbatch($service, $size, "Raw", $currentDate) ?? 0;
                                                        $semiRipeStock = \App\Helpers\Helpers::getstockbatch($service, $size, "Semi Ripe", $currentDate) ?? 0;
                                                        $ripeStock = \App\Helpers\Helpers::getstockbatch($service, $size, "Ripe", $currentDate) ?? 0;
                                                    @endphp

                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $productName }} - {{ $sizeName }}</td>
                                                        <td>{{ $rawStock  }}</td>
                                                        <td>{{ $semiRipeStock }}</td>
                                                        <td>{{ $ripeStock }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function() {
        $('#example1').DataTable({
            "responsive": true,
            "paging": true,
            "searching": true,
            "ordering": true,
            "order": [[1, 'asc']],
            "columnDefs": [{
                "orderable": false,
                "targets": [0]
            }],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export to Excel',
                    titleAttr: 'Export table to Excel',
                    className: 'btn btn-success'
                }
            ]
        });
    });
</script>
@endsection
