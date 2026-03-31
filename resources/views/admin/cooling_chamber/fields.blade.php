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
            <label>Location <span class="required" style="color:red;">*</span></label>
            <select class="form-select select2" id="receive_location_id" name="receive_location_id"
                data-placeholder="Select Location" required>

                <option value="">Select Location</option>

                @foreach (DB::table('location')->get() as $location)
                    <option value="{{ $location->id }}" @if (isset($cooling_chamber->receive_location_id) && $cooling_chamber->receive_location_id == $location->id) selected @endif>
                        {{ $location->location }}
                    </option>
                @endforeach

            </select>
            <input type="hidden" name="original_location_id" id="original_location_id" value="{{ $cooling_chamber->receive_location_id ?? '' }}">
            <input type="hidden" name="original_invoice" id="original_invoice" value="{{ $cooling_chamber->Invoicenumber ?? '' }}">
            <input type="hidden" name="original_invoice_no"  id="original_invoice_no" value="{{ $cooling_chamber->invoice_no ?? '' }}">
        </div>

        <div class="form-group col-md-3">
            <label for="Invoicenumber">Cooling Chamber No <span class="required">*</span></label>
            <input type="text" name="Invoicenumber" value="{{ old('Invoicenumber', $invoiceValue) }}"
                class="form-control" readonly>
            <input type="hidden" name="invoice_no" value="{{ old('invoice_no', $invoiceNumber) }}">
        </div>



        <div class="form-group col-md-3">
            <label>Cooling Chamber Date <span class="required" style="color:red;">*</span></label>

            <input class="datepicker-here form-control" id="billdate" name="billdate" type="text"
                value="{{ isset($cooling_chamber->billdate) ? \Carbon\Carbon::parse($cooling_chamber->billdate)->format('d-m-Y') : old('billdate') ?? date('d-m-Y') }}"
                data-language="en" placeholder="Enter Date" data-date-format="dd-mm-yyyy" data-auto-close="true">
            @error('billdate')
                <span class="text-danger"><strong>{{ $message }}</strong></span>
            @enderror
        </div>





 <div class="form-group col-md-3">
    <label>Ripening Chamber Number <span class="required" style="color:red;">*</span></label>
    {{-- <input type="text" id="ripening_chamber_No" name="ripening_chamber_No" onblur="hideshow()"
        value="{{ old('ripening_chamber_No', $cooling_chamber->ripening_chamber_No ?? 'RIPECHAM-') }}"
        class="form-control"> --}}

        <select id="ripening_chamber_No" name="ripening_chamber_No" class="form-control select2" onblur="hideshow()">
    <option value="">Select Ripening Chamber</option>
</select>
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
                                <th width='10%'>CHAMBER QTY<span class="required" style="color:red;">*</span></th>

                            </tr>
                        </thead>
                        <tbody id="recordsTableBody">
                            @if (isset($cooling_chamber_details))
                                @foreach ($cooling_chamber_details as $index => $detail)
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
                                            <input type="text" name="chamber_qty[]"
                                                value="{{ $detail->chamber_qty }}" required>
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
                    href="{{ route('admin.cooling_chamber.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
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

    });
</script>

<script>
  $(document).ready(function() {

    $(".select2").select2();

    let location_id = $("#receive_location_id").val();

    // ✅ Load chambers on page load (edit mode)
    if (location_id) {
        getRipeningChambers(location_id);
    }

    // ✅ Location change
    $("#receive_location_id").on("change", function() {
        let location_id = $(this).val();

        getInvoiceBatch(location_id);
        getRipeningChambers(location_id);
    });

    // ✅ Dropdown change (IMPORTANT)
    $("#ripening_chamber_No").on("change", function() {
        hideshow();
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
            url: "{{ route('admin.cooling_chamber.get-invoice') }}",

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
    var receivedQty = parseFloat(document.getElementsByName('chamber_qty[]')[index].value) || 0;

    if (receivedQty > remQty) {
        alert('Chamber quantity cannot exceed remaining quantity.');
        document.getElementsByName('chamber_qty[]')[index].value = remQty;
    }
}
</script>

<script>

    let alreadyAlerted = false; // flag

    function hideshow() {
        const ripening_chamber_No = $('#ripening_chamber_No').val();

        if (!ripening_chamber_No) {
            resetTable();
            return;
        }

        $.ajax({
            url: "{{ route('admin.cooling_chamber-getOrderRecords') }}",
            type: "GET",
            data: {
                ripening_chamber_No: ripening_chamber_No,
                inward_id: "{{ $cooling_chamber->id ?? '' }}",
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {

                if (response.status === 'empty') {
                    $('#recordsTableBody').html('');
                    if (!alreadyAlerted) {
                        alert('This is already fully added to ripening');
                        alreadyAlerted = true; // ✅ prevent repeated alerts
                    }
                    return;
                }

                alreadyAlerted = false; // reset if new data comes
                // populate table
                if (response.data && response.data.length > 0) {
                    let tableBody = '';
                    response.data.forEach((info, index) => {
                        tableBody += `
                        <tr id="row_${index}">
                            <td>
                                <input type='text' id='services_${index}'  value="${info.services}" readonly>
                                <input type='hidden' id='services_${index}' name='services[]' value="${info.servicesid}" readonly>
                            </td>
                            <td>
                                <input type="hidden" id='size${index}' name='size[]' value="${info.size}" readonly>
                                <input type='text' id='size_${index}' value="${info.p_size}" readonly>
                            </td>
                            <td>
                                <input type="text" name="stage[]" id="stage_${index}" value="${info.stage}" readonly/>
                            </td>
                            <td>
                                <input type="text"  name="batch_number[]" value="${info.batch_number}" readonly />
                            </td>
                            <td>
                                <input type="text" name="Quantity[]" value="${info.Quantity}" readonly />
                            </td>
                            <td>
                                <input type="text"  name="rem_qty[]" value="${info.rem_qty}" readonly/>
                            </td>
                            <td>
                                <input type="text"  name="chamber_qty[]" value="${info.chamber_qty ?? ''}" oninput="validateQuantitiesforindex(${index})" required/>
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


    $('#ripening_chamber_No').on('keypress', function(e) {
    if (e.which === 13) { // Enter key
        e.preventDefault(); // रोकता है form submit
        hideshow(); // call your function
    }
});
let selectedValue = "{{ $cooling_chamber->ripening_chamber_No ?? '' }}";

function getRipeningChambers(location_id) {

    if (!location_id) {
        $("#ripening_chamber_No").html('<option value="">Select Ripening Chamber</option>');
        return;
    }

    $.ajax({
        url: "{{ route('admin.cooling_chamber.getRipeningChambers') }}",
        type: "GET",
        data: { location_id: location_id },
        success: function(response) {

            let options = '<option value="">Select Ripening Chamber</option>';

            if (response.data.length > 0) {
                response.data.forEach(function(item) {

                    let selected = (item.Invoicenumber == selectedValue) ? 'selected' : '';

                    options += `<option value="${item.Invoicenumber}" ${selected}>
                                    ${item.Invoicenumber}
                                </option>`;
                });
            }

            $("#ripening_chamber_No")
                .html(options)
                .trigger('change'); // ✅ triggers hideshow automatically
        }
    });
}
</script>
