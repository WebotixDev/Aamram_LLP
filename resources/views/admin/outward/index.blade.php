@extends('layouts.simple.master')

@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
    <style>
       #dataTable th:nth-child(1)  {
    min-width: 20px !important;
    max-width: 20px !important;
}

#dataTable th:nth-child(2)  {
    min-width: 70px !important;
    max-width: 70px !important;
}

#dataTable th:nth-child(3)  {
    min-width: 90px !important;
    max-width: 90px !important;
}

#dataTable th:nth-child(4)  {
    min-width: 70px !important;
    max-width: 70px !important;
}

#dataTable th:nth-child(5)  {
    min-width: 150px !important;
    max-width: 150px !important;
}

#dataTable th:nth-child(6)  {
    min-width: 124px !important;
    max-width: 124px !important;
}

#dataTable th:nth-child(7)  {
    min-width: 215px !important;
    max-width: 215px !important;
}
#dataTable th:nth-child(8)  {
    min-width: 100px !important;
    max-width: 100px !important;
}
#dataTable th:nth-child(9)  {
    min-width: 50px !important;
    max-width: 50px !important;
}
#dataTable th:nth-child(10)  {
    min-width: 75px !important;
    max-width: 75px !important;
}

#dataTable th:nth-child(11)  {
    min-width: 60px !important;
    max-width: 60px !important;
}
#dataTable th:nth-child(12)  {
    min-width: 40px !important;
    max-width: 40px !important;
}
#dataTable th:nth-child(13)  {
    min-width: 320px !important;
    max-width: 330px !important;
}
.dataTables_wrapper table.dataTable th, .dataTables_wrapper table.dataTable td {
    padding: 3px !important;
}


table.dataTable input, table.dataTable select {
    border: 1px solid #efefef;
    height: 25px !important;

}

.btn {
    padding: .375rem 0.90rem !important;
    margin-right: 5px !important;

}
.color-legend {
            /* margin-bottom: 20px;
            padding: 10px; */
            /* background-color: #f4f4f4; */
            border-radius: 8px;
        }
        .color-legend .legend-item {
            display: inline-block;
            margin-right: 15px;
            font-size: 16px;
            margin-bottom: 10px;

        }
        .color-legend .legend-item span {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .define{
                margin-left: 30px;
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
                <h4>Outward     <a href="{{ route('admin.outward.create') }}" class="btn btn-secondary">{{ __('Add') }}</a></h4>
            </div>



            <div class="col-sm-6">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg></a></li>
                    <li class="breadcrumb-item">Outward</li>
                    <li class="breadcrumb-item active">Show</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-sm-12">
            <div class="card">
                <div class="card-block row ps-3 pt-3">
                    <div class="d-flex flex-wrap align-items-end">
                        <form method="GET" id="filter-form" class="d-flex flex-wrap gap-3">
                            <div>
                                <label for="from_date">From Date</label>
                                <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>
                            <div>
                                <label for="to_date">To Date</label>
                                <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary mt-4" id="filter-btn">Search</button>
                            </div>
                            <div>
                                <button id="receipt" class="btn btn-secondary mt-4">Farm</button>
                            </div>
                        </form>
                        <div>
                            <button id="qr" class="btn btn-secondary mb-2">SQR</button>
                        </div>
                        <div>
                            <button id="largeqr" class="btn btn-secondary mb-2">LQR</button>
                        </div>
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
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function () {
        // Select/Deselect all checkboxes
        $('#select-all').click(function () {
            $('.row-checkbox').prop('checked', this.checked);
        });

        // Handle QR Bulk button click
        $('#qr').click(function () {
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();
            let selectedRows = [];

            $('.row-checkbox:checked').each(function () {
                selectedRows.push($(this).val());
            });

            if (selectedRows.length === 0) {
                alert('Please select at least one record.');
                return;
            }

            // Build URL with selected IDs
            let url = `{{ route('admin.qr-bill-print') }}?selected_rows=${selectedRows.join(',')}`;
            window.open(url, '_blank');
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
        $('#largeqr').click(function () {
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();
            let selectedRows = [];

            $('.row-checkbox:checked').each(function () {
                selectedRows.push($(this).val());
            });

            if (selectedRows.length === 0) {
                alert('Please select at least one record.');
                return;
            }

            // Build URL with selected IDs
            let url = `{{ route('admin.qr-bill-print-large') }}?selected_rows=${selectedRows.join(',')}`;
            window.open(url, '_blank');
        });
    });
</script>
    <!--<script>-->
    <!--$(document).ready(function () {-->
    <!--    $('#select-all').click(function () {-->
    <!--        $('.row-checkbox').prop('checked', this.checked);-->
    <!--    });-->

    <!--    $('#labour').click(function () {-->
    <!--        let from_date = $('#from_date').val();-->
    <!--        let to_date = $('#to_date').val();-->
    <!--        let selectedRows = [];-->

    <!--        $('.row-checkbox:checked').each(function () {-->
    <!--            selectedRows.push($(this).val());-->
    <!--        });-->

    <!--        if (selectedRows.length === 0) {-->
    <!--            alert('Please select at least one record.');-->
    <!--            return;-->
    <!--        }-->

    <!--        let url = `{{ route('admin.Bulk_labour_outwards') }}?from_date=${from_date}&to_date=${to_date}&selected_rows=${selectedRows.join(',')}`;-->
    <!--        window.open(url, '_blank');-->
    <!--    });-->
    <!--});-->


    <!--</script>-->


<script>
    $(document).ready(function () {
        $('#select-all').click(function () {
            $('.row-checkbox').prop('checked', this.checked);
        });

        $('#receipt').click(function () {
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();
            let selectedRows = [];

            $('.row-checkbox:checked').each(function () {
                selectedRows.push($(this).val());
            });

            if (selectedRows.length === 0) {
                alert('Please select at least one record.');
                return;
            }

            let url = `{{ route('admin.Farm_bulkReceipt') }}?from_date=${from_date}&to_date=${to_date}&selected_rows=${selectedRows.join(',')}`;
            window.open(url, '_blank');
        });
    });




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
