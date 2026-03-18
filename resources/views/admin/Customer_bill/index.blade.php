@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">

    <style>
  #customer-order-main-table th:nth-child(1)
{
    min-width: 80px;
    max-width: 80px;
}
#customer-order-main-table th:nth-child(2)
 {
    min-width: 80px;
    max-width: 80px;
}
#customer-order-main-table th:nth-child(3)
 {
    min-width: 60px;
    max-width: 60px;
}
#customer-order-main-table th:nth-child(4) {
    min-width: 80px;
    max-width: 80px;
}
#customer-order-main-table th:nth-child(5){
    min-width: 80px;
    max-width: 80px;
}
#customer-order-main-table th:nth-child(6) {
    min-width: 80px;
    max-width: 80px;
}
#customer-order-main-table th:nth-child(7) {
    min-width: 80px;
    max-width: 80px;
}
.dataTables_wrapper table.dataTable th, .dataTables_wrapper table.dataTable td{
    padding: 3px !important;
    vertical-align: top;
}

.color-legend {
            margin-bottom: 20px;
            padding: 10px;
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
                <h4> Customer Wise Bill </h4>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item">Customer Wise Bill</li>
                    <li class="breadcrumb-item active">Customer Wise Bill</li>
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
                            <div class="col-md-3">
                                <label>Customer Name <span class="required" style="color:red;"></span></label>

                                <select class="form-select select2" id="customer_name" name="customer_name" data-placeholder="Select Customer">
                                    <option value="">Select Customer</option>
                                    @php
                                        $customers = DB::table('customers')->get();
                                    @endphp
                                    @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ request('customer_name') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->customer_name . '-' . $customer->mobile_no }}
                                    </option>

                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary mt-4" id="filter-btn">Search</button>
                            </div>
                            <div class="col-md-2" style="padding-inline-start: 35px">
                                <button id="labour" class="btn btn-secondary mt-4"> Customer </button>
                            </div>
                            <div class="col-sm-4 color-legend pt-4">
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
    let selectedUserId = null;

    function fetchTableData(from_date = null, to_date = null, user_id = null , customer_id = null) {
        if ($.fn.DataTable.isDataTable('#customer-order-main-table')) {
            $('#customer-order-main-table').DataTable().destroy();
        }
        $('#select-all').click(function () {
            $('.row-checkbox').prop('checked', this.checked);
        });

        $('#customer-order-main-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.Customer_bill.data") }}',
                data: {
                    from_date: from_date,
                    to_date: to_date,
                    user_id: user_id,
                    customer_id: customer_id // ← NEW

                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox',orderable: false, searchable: false },

                { data: 'sr_no', name: 'sr_no' },
                { data: 'billdate', name: 'billdate' },
                 { data: 'wholesaler', name: 'wholesaler' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'details', name: 'details' },
                { data: 'Tamount', name: 'Tamount' },
                // { data: 'action', name: 'action', orderable: false, searchable: false }
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
            lengthMenu: [[100], [100]]
        });
    }

    $(document).ready(function () {
        // Initial fetch
        const from_date = $('#from_date').val();
        const to_date = $('#to_date').val();
        fetchTableData(from_date, to_date, selectedUserId);

            $('#filter-btn').click(function (e) {
            e.preventDefault();
            const from_date = $('#from_date').val();
            const to_date = $('#to_date').val();
            const customer_id = $('#customer_name').val(); // ← NEW
            fetchTableData(from_date, to_date, selectedUserId, customer_id);
        });




        $('.legend-filter').click(function () {
            $('.legend-filter').removeClass('active');
            $(this).addClass('active');

            let userId = $(this).data('user-id');
            if (userId === 'inventory') {
                userId = 'others';
            }

            selectedUserId = userId;

            const from_date = $('#from_date').val();
            const to_date = $('#to_date').val();
            const customer_id = $('#customer_name').val();
            fetchTableData(from_date, to_date, selectedUserId, customer_id);

        });
    });
</script>


<script>
    $(document).ready(function () {
        // Select/Deselect all checkboxes
        $('#select-all').click(function () {
            $('.row-checkbox').prop('checked', this.checked);
        });

        // Handle QR Bulk button click

        $('#labour').click(function () {

            event.preventDefault(); // <-- Prevent form submission or default behavior

            // Collect all selected row checkbox values (i.e. row IDs)
            let selectedRows = [];

        $('.row-checkbox:checked').each(function () {
            selectedRows.push($(this).val());
        });


        if (selectedRows.length === 0) {
            alert('Please select at least one record.');
            return;
        }

            // Serialize selected IDs for the query string

            const url = `{{ route('admin.Customer_Sale_Bill') }}?selected_rows=${selectedRows.join(',')}`;
            window.open(url, '_blank');
            });
    });


 </script>
 <script>
    $(document).ready(function () {
        // Initialize Select2
        $('#customer_name').select2();

        // Focus search field when dropdown opens
        $('#customer_name').on('select2:open', function () {
            setTimeout(function () {
                document.querySelector('.select2-container--open .select2-search__field').focus();
            }, 0);
        });
    });
</script>

@endsection
