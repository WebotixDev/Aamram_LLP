@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <style> 
    #inward-main-table th:nth-child(1)  {
    min-width: 90px !important;
    max-width: 90px !important;
}

#inward-main-table th:nth-child(2)  {
    min-width: 130px !important;
    max-width: 130px !important;
}

#inward-main-table th:nth-child(3)  {
    min-width: 50px !important;
    max-width: 50px !important;
}

#inward-main-table th:nth-child(4)  {
    min-width: 90px !important;
    max-width: 90px !important;
}

#inward-main-table th:nth-child(5)  {
    min-width: 90px !important;
    max-width: 90px !important;
}

#inward-main-table th:nth-child(7)  {
    min-width: 90px !important;
    max-width: 90px !important;
}
.dataTables_wrapper table.dataTable th, .dataTables_wrapper table.dataTable td{
    padding: 3px !important;
    vertical-align: top;
}
    </style>
@endsection

@section('main_content')
<div class="container-fluid basic_table">
    <div class="page-title" style="padding:0px;">
        <div class="row">
            <div class="col-sm-6" >
                <h4>Stock 
                        <a href="{{ route('admin.inward.create') }}" class="btn btn-secondary">{{ __('Add') }}</a>
                    </h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item">Stock</li>
                    <li class="breadcrumb-item active">Stock</li>
                </ol>
            </div>
             <table id="custom-table">
    <tr>
        <?php 
        $products = DB::table('products')->get(); // Fetch all products
        $sumproduct=0;
        $sumproductpurchase=0;
        foreach ($products as $pro_master) { 
         
            $sumproductpurchase = DB::table('purchase_details')
            ->where('product', $pro_master->id)
            ->sum('qty');  // Sum of Quantity field

            $sumproduct = DB::table('sale_order')
            ->where('services', $pro_master->id)
            ->sum('qty');  // Sum of Quantity field

            $final= $sumproductpurchase-$sumproduct

        ?>

                <div class="col-xl-3 col-sm-3" style="padding-top:5px;">
                    <div class="card o-hidden small-widget">
                        <div class="card-body total-project border-b-primary border-2">
                            <span class="f-dark f-w-500 f-18"> {{ $pro_master->product_name }} : {{ $final }}</span>

                            <ul class="bubbles">
                                @for ($i = 0; $i < 9; $i++)
                                    <li class="bubble"></li>
                                @endfor
                            </ul>
                        </div>
                    </div>
                </div>
           
       <?php } ?>
    </tr>
</table>

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

<script>
$(document).ready(function() {

if ($.fn.dataTable.isDataTable('#inward-main-table')) {
    $('#inward-main-table').DataTable().clear().destroy();
}
$('#inward-main-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: '{{ route("admin.inward.data") }}',
    columns: [
        { data: 'sr_no', name: 'sr_no' },
       // { data: 'Invoicenumber', name: 'Invoicenumber' },
        { data: 'PurchaseDate', name: 'PurchaseDate' },
        // { data: 'product', name: 'product' },
        // { data: 'product_size', name: 'product_size' },
        // { data: 'stock', name: 'stock' },

        { data: 'details', name: 'details' },
       // { data: 'Tquantity', name: 'Tquantity' },

        { data: 'action', name: 'action', orderable: false, searchable: false }
    ],
    drawCallback: function(settings) {
        feather.replace();  // If you are using Feather icons
    }
});
});



</script>
@endsection
