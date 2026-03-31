<div class="form theme-form">
    @php
        // -----------------------
        // Determine invoice display
        // -----------------------
        $invoiceValue = '';
        $invoiceNumber = '';
        if (isset($invoice)) {
            if (is_object($invoice)) {
                // edit mode
                $invoiceValue = $invoice->Invoicenumber ?? '';
                $invoiceNumber = $invoice->invoice_no ?? '';
            } elseif (is_array($invoice)) {
                $invoiceValue = $invoice['formatted'] ?? '';
                $invoiceNumber = $invoice['number'] ?? '';
            }
        }

    @endphp
    <style>
        #productTable {
            width: 100%;
            border-collapse: collapse;
        }

        #productTable th,
        #productTable td {
            border: 1px solid #000;
            /* table borders */
            padding: 6px;
            text-align: left;
        }

        /* Input styling inside table */
        #productTable input {
            width: 100%;
            padding: 5px;
            border: 1px solid #ccc;
            /* input border */
            border-radius: 4px;
            box-sizing: border-box;
        }

        /* Optional: focus effect */
        #productTable input:focus {
            border-color: #007bff;
            outline: none;
        }

        /* Header style */
        #productTable th {
            background-color: #f2f2f2;
        }

        /* Scroll container */
        .table-wrapper {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 400px;
        }
    </style>
    <div class="row">

        <div class="form-group col-md-3">
            <label>Receive Location <span class="required" style="color:red;">*</span></label>
            <select class="form-select select2" id="receive_location_id" name="receive_location_id"
                data-placeholder="Select Location" required>

                <option value="">Select Location</option>

                @foreach (DB::table('location')->get() as $location)
                    <option value="{{ $location->id }}" @if (isset($Warehouse_inward->receive_location_id) && $Warehouse_inward->receive_location_id == $location->id) selected @endif>
                        {{ $location->location }}
                    </option>
                @endforeach

            </select>

<input type="hidden" name="original_location_id" id="original_location_id"
    value="{{ $Warehouse_inward->receive_location_id ?? '' }}">

<input type="hidden" name="original_invoice" id="original_invoice"
    value="{{ $Warehouse_inward->Invoicenumber ?? '' }}">

<input type="hidden" name="original_invoice_no" id="original_invoice_no"
    value="{{ $Warehouse_inward->invoice_no ?? '' }}">
        </div>

        <div class="form-group col-md-3">
            <label for="Invoicenumber">Warehouse Inward No <span class="required">*</span></label>
            <input type="text" name="Invoicenumber" value="{{ old('Invoicenumber', $invoiceValue) }}"
                class="form-control" readonly>
            <input type="hidden" name="invoice_no" value="{{ old('invoice_no', $invoiceNumber) }}">
        </div>



        <div class="form-group col-md-3">
            <label>Warehouse Inward Date <span class="required" style="color:red;">*</span></label>

            <input class="datepicker-here form-control" id="billdate" name="billdate" type="text"
                value="{{ isset($Warehouse_inward->billdate) ? \Carbon\Carbon::parse($Warehouse_inward->billdate)->format('d-m-Y') : old('billdate') ?? date('d-m-Y') }}"
                data-language="en" placeholder="Enter Date" data-date-format="dd-mm-yyyy" data-auto-close="true">
            @error('billdate')
                <span class="text-danger"><strong>{{ $message }}</strong></span>
            @enderror
        </div>





 <div class="form-group col-md-3">
    <label>Delivery Challan Number <span class="required" style="color:red;">*</span></label>
    <input type="text" id="farm_dcNo" name="farm_dcNo" onblur="hideshow()"
        value="{{ old('farm_dcNo', $Warehouse_inward->farm_dcNo ?? 'FARMDC-') }}"
        class="form-control">
</div>
    </div>
    <hr>

    <br><br>

    <div id="proformatable">
        <div class="row">
            <div class="col-md-12">
                <div style="overflow-x:auto; overflow-y:auto; max-height:400px;">
                    <table id="productTable" class="table">
                        <thead>
                            <tr>
                                <th width='20%'>PRODUCTS<span class="required" style="color:red;">*</span></th>
                                <th width='20%'>PRODUCT SIZE<span class="required" style="color:red;">*</span></th>
                                <th width='10%'>STAGE</th>
                                <th width='12%'>BATCH NO</th>
                                <th width='10%'>QUANTITY<span class="required" style="color:red;">*</span></th>
                                <th width='10%'>REM_QTY</th>
                                <th width='10%'>RECEIVED QTY<span class="required" style="color:red;">*</span></th>
                                <th width='10%'>MISSING QTY<span class="required" style="color:red;">*</span></th>

                            </tr>
                        </thead>
                        <tbody id="recordsTableBody">
                            @if (isset($Warehouse_inward_details))
                                @foreach ($Warehouse_inward_details as $index => $detail)
                                    @php
                                        $product = DB::table('products')->where('id', $detail->services)->first();
                                        $product_name = $product ? $product->product_name : 'Unknown Product';
                                        $size = DB::table('product_details')->where('id', $detail->size)->first();
                                        $product_size = $size ? $size->product_size : 'Unknown Size';
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="text" value="{{ $product_name }}" readonly>
                                            <input type="hidden" name="services[]" value="{{ $detail->services }}">
                                        </td>
                                        <td>
                                            <input type="text" value="{{ $product_size }}" readonly>
                                            <input type="hidden" name="size[]" value="{{ $detail->size }}">
                                        </td>
                                        <td>
                                            <input type="text" name="stage[]" value="{{ $detail->stage }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="batch_number[]"
                                                value="{{ $detail->batch_number }}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="Quantity[]" value="{{ $detail->Quantity }}"
                                                readonly>
                                        </td>
                                        <td><input type="text" name="rem_qty[]" value="{{ $detail->rem_qty }}"
                                                readonly></td>
                                        <td>
                                            <input type="text" name="received_qty[]"
                                                value="{{ $detail->received_qty }}" required>
                                        </td>
                                        <td>
                                            <input type="text" name="missing_qty[]"
                                                value="{{ $detail->missing_qty }}" required>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col">
            <div class="text-center">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button> <a
                    href="{{ route('admin.warehouse_inward.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            </div>
        </div>
    </div>

</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script src="{{ asset('assets/js/select2/select2-custom.js') }}"></script>
<script src="{{ asset('assets/js/custom-validation/validation.js') }}"></script>
<script src="{{ asset('assets/js/datepicker/date-picker/datepicker.js') }}"></script>
<script src="{{ asset('assets/js/datepicker/date-picker/datepicker.en.js') }}"></script>
<script src="{{ asset('assets/js/datepicker/date-picker/datepicker.custom.js') }}"></script>
<script src="{{ asset('assets/js/dropzone/dropzone.js') }}"></script>
<script src="{{ asset('assets/js/dropzone/dropzone-script.js') }}"></script>



<script>
$(document).ready(function() {
    $(".select2").select2();

  $("#receive_location_id").on("change", function() {
    let location_id = $(this).val();
    getInvoiceBatch(location_id);

    // Trigger table update only if DC number is valid
    let dcNo = $('#farm_dcNo').val().trim();
    if (dcNo && dcNo !== 'FARMDC-') {
        hideshow();
    }
});

    // Trigger hideshow automatically if edit mode
    if ($('#farm_dcNo').val() != '') {
        hideshow();
    }

    // Reset alerts on DC number input change
    $('#farm_dcNo').on('input', function() {
        alreadyAlerted = false;
        invalidLocationAlerted = false;
    });

    // Clear default FARMDC- on focus
    $('#farm_dcNo').on('focus', function() {
        if ($(this).val() === 'FARMDC-') {
            $(this).val('');
        }
    });

    // Trigger hideshow on Enter key
    $('#farm_dcNo').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            hideshow();
        }
    });
});

    function getInvoiceBatch(location_id) {
        let originalLocation = $("#original_location_id").val();

        if (location_id == originalLocation) {
            // Restore original values
            $("input[name='Invoicenumber']").val($("#original_invoice").val());
            $("input[name='invoice_no']").val($("#original_invoice_no").val());

            return;
        }

        // Fetch new invoice/batch for different location
        $.ajax({
            url: "{{ route('admin.warehouse_inward.get-invoice') }}",

            type: "GET",
            data: {
                location_id: location_id,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                $("input[name='Invoicenumber']").val(response.invoice.formatted);
                $("input[name='invoice_no']").val(response.invoice.number);

            }
        });
    }


    function validateQuantitiesforindex(index) {
        var remQty = parseFloat(document.getElementsByName('rem_qty[]')[index].value) || 0;
        var receivedQty = parseFloat(document.getElementsByName('received_qty[]')[index].value) || 0;
        var missingQty = parseFloat(document.getElementsByName('missing_qty[]')[index].value) || 0;

        var totalQty = receivedQty + missingQty;

        if (totalQty > remQty) {
            // Adjust missingQty if total exceeds remaining
            missingQty = remQty - receivedQty;
            document.getElementsByName('missing_qty[]')[index].value = missingQty >= 0 ? missingQty : 0;
            alert('Sum of received and missing quantity cannot exceed remaining quantity.');
        }
    }
</script>

<script>
    //     function hideshow() {
    //         const farm_dcNo = $('#farm_dcNo').val(); // Get selected order ID

    //         if (farm_dcNo) {
    //             // Perform AJAX request to fetch records related to the selected order
    //             $.ajax({
    //                 url: "{{ route('admin.Farm-DC-getOrderRecords') }}",
    //                 type: "GET",
    //                 data: {
    //                     farm_dcNo: farm_dcNo, // Pass order ID
    //                     inward_id: "{{ $Warehouse_inward->id ?? '' }}", // ✅ ADD THIS LINE
    //                     _token: "{{ csrf_token() }}" // Send CSRF token
    //                 },
    //                 success: function(response) {

    //                    if (response.status === 'empty') {
    //                     $('#recordsTableBody').html('');
    //                     alert('This challan already fully added to warehouse');
    //                     return;
    //                 }
    //                                 // Check if the response contains data
    //                     if (response.data && response.data.length > 0) {
    //                         let tableBody = '';
    //                         // Loop through the records and generate rows
    //                         response.data.forEach((info, index) => {
    //                             tableBody += `
    //                                 <tr id="row_${index}">
    //                                     <td>
    //                                         <input type='text' id='services_${index}'  value="${info.services}" readonly>
    //                                         <input type='hidden' id='services_${index}' name='services[]' value="${info.servicesid}" readonly>

    //                                     </td>
    //                                     <td style="max-width:25px">
    //                                          <input type="hidden" id='size${index}' name='size[]' value="${info.size}" class="form-control" readonly>
    //                                            <input type='text' id='size_${index}' name='size_[]' value="${info.p_size}" readonly>
    //                                     </td>
    //                                     <td>
    //                                         <input type="text" name="stage[]" id="stage_${index}" value="${info.stage}" readonly/>
    //                                     </td>
    //                                      <td>
    //                                         <input type="text"  name="batch_number[]" value="${info.batch_number}" id="qty${index}" readonly />
    //                                     </td>
    //                                     <td>
    //                                         <input type="text" name="Quantity[]" value="${info.Quantity}" id="Quantity${index}" readonly />
    //                                     </td>

    //                                     <td>
    //                                          <input type="text"  name="rem_qty[]" value="${info.rem_qty}" id="rem_qty${index}" readonly/>
    //                                     </td>
    //                                         <td>
    //                                         <input type="text"  name="received_qty[]" value="${info.received_qty ?? ''}" oninput="validateQuantitiesforindex(${index})" id="received_qty${index}" required/>
    //                                   </td>
    //                                        <td>
    //                                         <input type="text"  name="missing_qty[]" value="" oninput="validateQuantitiesforindex(${index})" id="missing_qty${index}" required/>
    //                                   </td>
    //                                 </tr>`;
    //                         });

    //                         // Update the table body with the fetched data
    //                         $('#recordsTableBody').html(tableBody);
    //                     } else {
    //                         // No records found, reset the table
    //                         resetTable();
    //                     }
    //                 },
    //                 error: function(xhr, status, error) {
    //                     console.error("AJAX Error: ", error);
    //                     alert("An error occurred while fetching the records.");
    //                 }
    //             });
    //         } else {
    //             // No order is selected, reset the table
    //             resetTable();
    //         }
    //     }

    //     function resetTable() {
    //         $('#recordsTableBody').html('');
    //     }


    //     $('#farm_dcNo').on('keyup', function() {
    //     hideshow();
    // });
let alreadyAlerted = false;
let invalidLocationAlerted = false;

function hideshow() {
    const farm_dcNo = $('#farm_dcNo').val();
    const location_id = $('#receive_location_id').val();

    if (!farm_dcNo || !location_id) {
        $('#recordsTableBody').html('');
        alreadyAlerted = false;
        invalidLocationAlerted = false;
        return;
    }

    $.ajax({
        url: "{{ route('admin.Farm-DC-getOrderRecords') }}",
        type: "GET",
        data: {
            farm_dcNo: farm_dcNo,
            inward_id: "{{ $Warehouse_inward->id ?? '' }}",
            location_id: location_id,
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            if (response.status === 'invalid_location') {
                $('#recordsTableBody').html('');
                if (!invalidLocationAlerted) {
                    alert(response.message);
                    invalidLocationAlerted = true;
                }
                alreadyAlerted = false;
                return;
            }

            if (response.status === 'empty') {
                $('#recordsTableBody').html('');
                if (!alreadyAlerted) {
                    alert(response.message);
                    alreadyAlerted = true;
                }
                invalidLocationAlerted = false;
                return;
            }

            alreadyAlerted = false;
            invalidLocationAlerted = false;

            if (response.data && response.data.length > 0) {
                let tableBody = '';
                response.data.forEach((info, index) => {
                    tableBody += `
                        <tr id="row_${index}">
                            <td>
                                <input type='text' value="${info.services}" readonly>
                                <input type='hidden' name='services[]' value="${info.servicesid}" readonly>
                            </td>
                            <td>
                                <input type="hidden" name='size[]' value="${info.size}" readonly>
                                <input type='text' value="${info.p_size}" readonly>
                            </td>
                            <td>
                                <input type="text" name="stage[]" value="${info.stage}" readonly/>
                            </td>
                            <td>
                                <input type="text" name="batch_number[]" value="${info.batch_number}" readonly />
                            </td>
                            <td>
                                <input type="text" name="Quantity[]" value="${info.Quantity}" readonly />
                            </td>
                            <td>
                                <input type="text" name="rem_qty[]" value="${info.rem_qty}" readonly/>
                            </td>
                            <td>
                                <input type="text" name="received_qty[]" value="${info.received_qty ?? ''}" oninput="validateQuantitiesforindex(${index})" required/>
                            </td>
                            <td>
                                <input type="text" name="missing_qty[]" value="${info.missing_qty ?? 0}" oninput="validateQuantitiesforindex(${index})" required/>
                            </td>
                        </tr>`;
                });
                $('#recordsTableBody').html(tableBody);
            } else {
                resetTable();
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", error);
        }
    });
}

function resetTable() {
    $('#recordsTableBody').html('');
    alreadyAlerted = false;
    invalidLocationAlerted = false;
}



</script>
