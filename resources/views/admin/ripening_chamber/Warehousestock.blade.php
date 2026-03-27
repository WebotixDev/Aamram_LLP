@extends('layouts.simple.master')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css"/>

<style>
.dt-buttons { display: flex; gap: 5px; }
.dataTables_filter { text-align: right !important; }
.table thead th {
    background-color: #f4f6f9;
}
</style>
@endsection

@section('main_content')
<div class="container-fluid">
    <h4 class="mb-4">Warehouse Stock Report</h4>

    <div class="row mb-3">
        <div class="col-md-3">
            <label>Location</label>
            <select class="form-control" id="location">
                <option value="">Select Location</option>
                @foreach ($locations as $loc)
                    <option value="{{ $loc->id }}">{{ $loc->location }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label>Farm DC No</label>
            <input type="text" id="farm_dcNo" class="form-control" placeholder="Enter DC No">
        </div>

        <div class="col-md-2 mt-4">
            <button class="btn btn-primary" id="filterBtn">Filter</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="stock_table">
            <thead>
                  <tr>
                <th>Location</th>
                <th>Farm DC</th>
                <th>Service</th>
                <th>Size</th>
                <th>Stage</th>
                <th>Batch</th>
                <th>Challan Qty</th>
                <th>Received</th>
                <th>Missing</th>
                <th>Remaining</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
$(document).ready(function(){

    let table = $('#stock_table').DataTable({
        processing: true,
        responsive: true,
        dom: "Bfrtip",
        buttons: ['excel','csv','print'],
        data: [],
         columns: [
            { data: 'location_name' },
            { data: 'farm_dcNo' },
            { data: 'service_name' },
            { data: 'size_name' },
            { data: 'stage' },
            { data: 'batch_number' },
            { data: 'challan_qty' },
            { data: 'total_received' },
            { data: 'total_missing' },
            { data: 'remaining_qty' }
        ],

        createdRow: function(row, data){
            if(data.stock_qty < 10){
                $(row).css('background-color','#ffe5e5');
            }
        }
    });

    function loadStock(){
        let location_id = $('#location').val();
        let farm_dcNo = $('#farm_dcNo').val();

        if(!location_id){
            table.clear().draw();
            return;
        }

        $.ajax({
            url: "{{ route('admin.WarehouseStockReport.get-stock') }}",
            type: "GET",
            data: {
                location_id: location_id,
                farm_dcNo: farm_dcNo
            },
            success: function(res){
                if(res.status === 'success'){
                    table.clear().rows.add(res.data).draw();
                }
            }
        });
    }

    $('#filterBtn').click(function(){
        loadStock();
    });

});
</script>

@endsection
