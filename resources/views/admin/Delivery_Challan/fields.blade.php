{{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
<!-- DataTables CSS -->
{{-- <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet"> --}}
<link href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" rel="stylesheet">
<div class="form theme-form">
    <!-- General Information Section -->
    @php

    $count = DB::table('delivery_challan')->count();

    $nextBillNo = $count > 0 ? $count + 1 : 1;

    @endphp
          <div class="row">

            <div class="form-group col-md-4">
                <label>Delivery No <span class="required" style="color:red;">*</span></label>

                        <input type="text" id="Invoicenumber" name="Invoicenumber" value="<?php echo isset($saleOrders) ? $saleOrders['Invoicenumber'] : $nextBillNo; ?>" class="required form-control" readonly>
                    </div>


            <div class="form-group col-md-4">
                <label>Delivery Date <span class="required" style="color:red;">*</span></label>

                <input class="datepicker-here form-control" id="billdate" name="billdate" type="text"
                    value="{{ isset($Delivery_Challan->billdate) ? \Carbon\Carbon::parse($Delivery_Challan->billdate)->format('d-m-Y') : (old('billdate') ?? date('d-m-Y')) }}"
                    data-language="en"
                    placeholder="Enter Date"
                    data-date-format="dd-mm-yyyy" data-auto-close="true">

                @error('billdate')
                    <span class="text-danger"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="form-group col-md-4">
                <label>Transpoter <span class="required" style="color:red;">*</span></label>
                <select class="form-select select2" id="transporter" name="transporter" data-placeholder="Select " required>
                    <option value="">Select Transpoter</option>
                    @foreach(DB::table('transporter')->get() as $expense)
                        <option value="{{ $expense->transporter }}"
                            @if(isset($Delivery_Challan->transporter) && $Delivery_Challan->transporter == $expense->transporter)
                                selected
                            @endif
                        >
                            {{ $expense->transporter }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>


         <div class="row">
                        <div class="col">
                        <div class="text-center pt-5">
                                            <button type="submit" class="btn btn-primary">Save</button>

                                        <a href="{{ route('admin.Delivery_Challan.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                                        </div>
                        </div>
                    </div>

        <div class="batch-table">
            <div class="row">
                <div class="col-md-12">
                    <div class="">
                        <div class="card-body">
                            <table class="table table-bordered table-striped" id="example2">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all "></th> <!-- Select All Checkbox -->
                                        <th>Sr.No.</th>
                                        <th>Date</th>
                                        <th>Order.No.</th>
                                        <th>Customer Name</th>
                                        <th>Address</th>
                                        <th>Product</th>
                                        <th>Size</th>
                                        <th>Stage</th>
                                        {{-- <th>Qty</th> --}}
                                        <th>Dis Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    
                                    
                $season = session('selected_season');
                
                                        if(isset($selected_ids) && $selected_ids) {
                                      $records = DB::table('outward_details')
                                    ->leftJoin('products', 'outward_details.services', '=', 'products.id')
                                    ->leftJoin('product_details', 'outward_details.size', '=', 'product_details.id')
                                    ->leftJoin('customers', 'outward_details.customer_name', '=', 'customers.id')
                                  ->leftJoin('sale_orderdetails', 'outward_details.order_no', '=', 'sale_orderdetails.id')
                                    ->where('outward_details.season', $season) // Add this line
                                    ->where(function ($query) use ($selected_ids) {
                                        $query->where('outward_details.flag', 0);
            
                                        if (!empty($selected_ids)) {
                                            $query->orWhereIn('outward_details.id', $selected_ids);
                                        }
                                    })
                                    ->select(
                                        'outward_details.*',
                                        'products.product_name',
                                       'sale_orderdetails.order_address',
                                        'product_details.product_size as product_size',
                                        'customers.customer_name'
                                    )
                                    ->get();
                  } else {
                                            $records = DB::table('outward_details')
                                                ->where('outward_details.flag', 0)
                                                ->leftJoin('products', 'outward_details.services', '=', 'products.id')
                                                ->leftJoin('product_details', 'outward_details.size', '=', 'product_details.id')
                                                ->leftJoin('customers', 'outward_details.customer_name', '=', 'customers.id')
                                                ->leftJoin('sale_orderdetails', 'outward_details.order_no', '=', 'sale_orderdetails.id')
                                                 ->where('outward_details.season', $season) // Add this line

                                                ->select(
                                                    'outward_details.*',
                                                    'products.product_name',
                                                   'sale_orderdetails.order_address',
                                                    'product_details.product_size as product_size',
                                                    'customers.customer_name'
                                                )
                                                ->get();
                                        }
                                    @endphp

                                    @foreach ($records as $index => $record)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="row-checkbox" name="selected_ids[]" value="{{ $record->id }}"
                                                    @if(isset($selected_ids) && in_array($record->id, $selected_ids)) checked @endif>
                                            </td>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $record->billdate }}</td>
                                            <td>{{ $record->order_no }}</td>
                                            <td>{{ $record->customer_name }}</td>
                                            <td>{{ $record->order_address }}</td>
                                            <td>{{ $record->product_name }}</td>
                                            <td>{{ $record->product_size }}</td>
                                            <td>{{ $record->stage }}</td>
                                            <td>{{ $record->currdispatch_qty }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden input field to store the selected IDs -->
        <input type="hidden" name="selected_idstot" id="selected_idstot" value="">


           
          </div>



<!-- jQuery first -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>




<script>

window.onload = function(e){
    console.log("window.onload");
    updateLedger();
    restoreCheckedValues();
}
var selectedIds = new Set(); // Store checked checkbox values

// Function to update selected checkboxes
function updateLedger() {
    $('input.row-checkbox:checked').each(function () {
        selectedIds.add(this.value);
    });

    $('input.row-checkbox:not(:checked)').each(function () {
        selectedIds.delete(this.value);
    });

    console.log("Selected IDs:", Array.from(selectedIds).join(",")); // Debugging
    $("#selected_idstot").val(Array.from(selectedIds).join(","));
}

// Function to restore checked checkboxes after DataTable reloads
function restoreCheckedValues() {
    $('input.row-checkbox').each(function () {
        this.checked = selectedIds.has(this.value);
    });
}

// Initialize DataTable
// Initialize DataTable without pagination
var table = $('#example2').DataTable({
    paging: false, // Disable pagination
    searching: true, // Keep search enabled
    info: false // Hide "Showing X entries" info
});


// Handle DataTable reload on search
$('#searchButton').on('click', function () {
    updateLedger(); // Save selected values before search
    table.ajax.reload(function () {
        restoreCheckedValues(); // Restore selections after DataTable reloads
    }, false);
});

// Ensure updateLedger runs only once when a checkbox is clicked
$(document).on('change', 'input.row-checkbox', function () {
    updateLedger();
});

</script>
