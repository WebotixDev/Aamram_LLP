@extends('layouts.simple.master')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css"/>
<style>
    .dt-buttons {
        display: flex;
        gap: 5px;
    }
    .dataTables_filter {
        text-align: right !important;
    }
    .table thead th {
        background-color: #f4f6f9;
        color: #333;
        vertical-align: middle;
    }
    .dataTables_wrapper .row.mb-2 {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
@endsection

@section('main_content')
<div class="container-fluid">
    <h4 class="mb-4">Farm Stock Report</h4>

    <div class="row mb-4">
        <div class="col-md-4">
            <label class="form-label">Location</label>
            <select class="form-control" id="location">
                <option value="">Select Location</option>
                @foreach ($locations as $loc)
                    <option value="{{ $loc->id }}">{{ $loc->location }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Batch</label>
            <select class="form-control" id="batch_number">
                <option value="">Select Batch</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover nowrap" id="stock_table" style="width:100%">
            <thead>
                <tr>
                    <th>Batch</th>
                    <th>Service</th>
                    <th>Size</th>
                    <th>Stage</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
$(document).ready(function(){

    let table = $('#stock_table').DataTable({
        processing: true,
        responsive: true,
        searching: true,
        ordering: true,
        paging: true,
        autoWidth: false,
        dom: "<'row mb-2'<'col-sm-6 d-flex'B><'col-sm-6 d-flex justify-content-end'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-2'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'excelHtml5', className: 'btn btn-success btn-sm', text: 'Excel' },
            { extend: 'csvHtml5', className: 'btn btn-info btn-sm', text: 'CSV' },
            { extend: 'print', className: 'btn btn-primary btn-sm', text: 'Print' }
        ],
        data: [],
        columns: [
            { data: 'batch_number' },
            { data: 'service_name' },
            { data: 'size_name' },
            { data: 'stage' },
            { data: 'stock_qty' }
        ]
    });

    // Load batches when location changes
    $('#location').change(function(){
        let location_id = $(this).val();
        if(!location_id){
            $('#batch_number').html('<option value="">Select Batch</option>');
            table.clear().draw();
            return;
        }

        $.ajax({
            url: "{{ route('admin.get.batch.by.locationForStock') }}",
            type: "GET",
            data: { location_id: location_id },
            success: function(res){
                let html = '<option value="">Select Batch</option>';
                $.each(res.data, function(i, batch){
                    html += `<option value="${batch.batch_number}">${batch.batch_number}</option>`;
                });
                $('#batch_number').html(html);
            }
        });

        loadStock(location_id, '');
    });

    // Batch filter
    $('#batch_number').change(function(){
        let location_id = $('#location').val();
        let batch_number = $(this).val();
        loadStock(location_id, batch_number);
    });

    // Load stock data
function loadStock(location_id, batch_number){
    if(!location_id) return;

    $.ajax({
        url: "{{ route('admin.FARMReport.get-stock') }}",
        type: "GET",
        data: { location_id: location_id, batch_number: batch_number },
        success: function(res){
            if(res.status === 'success'){
                // Filter out rows where stock_qty is 0
                let filteredData = res.data.filter(row => row.stock_qty > 0);

                table.clear().rows.add(filteredData).draw();
            } else {
                table.clear().draw();
            }
        }
    });
}
});
</script>
@endsection
