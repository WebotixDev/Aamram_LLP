<div class="form theme-form">
    <!-- General Information Section -->
    @php



$lastBillNo = DB::table('outward_details')->max('Invoicenumber');

$nextBillNo = $lastBillNo ? $lastBillNo + 1 : 1;


@endphp
<style>
    #myTable {
    width: 100%;
    table-layout: fixed; /* Ensures proper column distribution */
    font-size: 12px;
    word-wrap: break-word; /* Prevents text from overflowing */
}

#myTable th, #myTable td {
    padding: 5px;
    text-align: center; /* Centers text for better alignment */
    vertical-align: middle;
    white-space: nowrap; /* Prevents excessive wrapping */
}

#myTable input {
    width: 100%; /* Makes input fields fit inside their cells */
    font-size: 12px;
    padding: 3px;
    text-align: center;
}

</style>
    <div class="row">
        <div class="form-group col-md-2">
        <label>Outward No <span class="required" style="color:red;">*</span></label>

                <input type="text" name="Invoicenumber" value="{{ $nextBillNo }}" class="required form-control" id="Invoicenumber" placeholder="Outward No" readonly>

        </div>

           <div class="form-group col-md-2">
    <label> Date <span class="required" style="color:red;">*</span></label>

    <input class="datepicker-here form-control" id="billdate" name="billdate" type="text"
        value="{{ isset($outwardsdetails->billdate) ? \Carbon\Carbon::parse($outwardsdetails->billdate)->format('d-m-Y') : (old('billdate') ?? date('d-m-Y')) }}"
        data-language="en"
        placeholder="Enter Date"
        data-date-format="dd-mm-yyyy" data-auto-close="true">

    @error('billdate')
        <span class="text-danger"><strong>{{ $message }}</strong></span>
    @enderror
</div>

    <div class="form-group col-md-4">
                <label>Customer Name <span class="required" style="color:red;">*</span></label>

            <select class="form-select select2" id="customer_name" name="customer_name" data-placeholder="Select " onchange="loadOrders()" required>
                    <option value="">Select Customer</option>
                        @php
                            $customers = DB::table('customers')->get();
                        @endphp
                            @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}"
                    @if(isset($outwardsdetails) && $customer->id == $outwardsdetails->customer_name)  selected     @endif>
                    {{ $customer->customer_name }}
                    </option>
                    @endforeach
            </select>

                    </div>


        <div class="form-group col-md-2">
        <label>Order No <span class="required" onclick="hideshow()" style="color:red;">*</span></label>
    <?php    if (isset($outwardsdetails)) { ?>
        <input type="text"  style="max-width:150px" id="order_no" name="order_no" value="<?= $outwardsdetails->order_no ?>" readonly required>
    <?php }else{ ?>
        <select class="form-select select2" id="order_no" name="order_no" onchange="hideshow()" data-placeholder="Select Types">
        <option value="">Select Order</option>
    </select>

        <?php } ?>
            </div>



    </div>
    <br><br>

    <div id="proformatable">
        <div class="row">
            <div class="col-md-12">
                <table id="myTable" style="width:100%; font-size:12px" class="table table-striped table-hover table-bordered dt-responsive">
                     <thead>
                        <tr>

                            <th width="15%">PRODUCTS<span class="required" style="color:red;">*</span></th>
                            <th width="30%">PRODUCT SIZE<span class="required" style="color:red;">*</span></th>
                            <th>STAGE<span class="required" style="color:red;">*</span></th>
                            <th>ORDER QTY<span class="required" style="color:red;">*</span></th>
                            <th>DISPATCHED QTY<span class="required" style="color:red;">*</span></th>
                            <th>REMAINING QTY<span class="required" style="color:red;">*</span></th>
                            <th>CURR DIS QTY<span class="required" style="color:red;">*</span></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php


                      $i = 0;
                    if (isset($outwardsdetails)) {

                    ?>
                            <tr id="row">

           @php

    // Fetch product name based on service ID
    $product = DB::table('products')->where('id', $outwardsdetails->services)->first();
    $product_name = $product ? $product->product_name : 'Unknown Product';

        $size = DB::table('product_details')->where('id', $outwardsdetails->size)->first();
    $product_size = $size ? $size->product_size : 'Unknown Product';
@endphp

                                <td>
                                    <input type="text"  id="services" name="services" value="{{ $product_name }}" readonly required>
                                </td>
                                <td>
                                    <input type="text"  id="size" name="size" value="{{$product_size}}" readonly required>
                                </td>
                                <td>
                                    <input type="text"  id="stage" name="stage" value="<?= $outwardsdetails->stage ?>" readonly required>
                                </td>
                                <td>
                                    <input type="text"  id="qty" name="qty" value="<?= $outwardsdetails->qty ?>"  readonly required>
                                </td>
                                <td>
                                    <input type="text" id="Quantity" name="Quantity" value="<?= $outwardsdetails->Quantity ?>" readonly  required>
                                </td>
                                <td>
                                   <input type="text"  id="rem_qty" name="rem_qty" value="{{ $outwardsdetails->rem_qty + $outwardsdetails->currdispatch_qty }}" readonly required>
                                </td>
                                <td>
                                    <input type="text"  id="currdispatch_qty" name="currdispatch_qty" value="{{ $outwardsdetails->currdispatch_qty }}" required oninput="validateQuantities()">
                                </td>
                            </tr>
                            </tbody>
                            <?php } else{ ?>

                    <tbody id="recordsTableBody">

                    </tbody>
            <?php } ?>
                </table>
            </div>
        </div>
    </div>

    <br>

                                                <div class="row">
        <div class="col">
        <div class="text-center pt-5">
                            <button type="submit" class="btn btn-primary">Save</button>

                        <a href="{{ route('admin.outward.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
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
    function hideshow() {
        const orderID = $('#order_no').val(); // Get selected order ID

        if (orderID) {
            // Perform AJAX request to fetch records related to the selected order
            $.ajax({
                url: "{{ route('admin.getOrderRecords') }}",
                type: "GET",
                data: {
                    orderID: orderID, // Pass order ID
                    _token: "{{ csrf_token() }}" // Send CSRF token
                },
                success: function(response) {
                    // Check if the response contains data
                    if (response.data && response.data.length > 0) {
                        let tableBody = '';
                        // Loop through the records and generate rows
                        response.data.forEach((info, index) => {
                            tableBody += `
                                <tr id="row_${index}">
                                    <td>
                                        <input type='text' id='services_${index}'  value="${info.services}" readonly>
                                        <input type='hidden' id='services_${index}' name='services[]' value="${info.servicesid}" readonly>

                                    </td>
                                    <td style="max-width:25px">
                                         <input type="hidden" id='size${index}' name='size[]' value="${info.size}" class="form-control" readonly>
                                           <input type='text' id='size_${index}' name='size_[]' value="${info.p_size}" readonly>
                                    </td>
                                    <td>
                                        <input type="text" name="stage[]" id="stage_${index}" value="${info.stage}" readonly/>
                                    </td>
                                     <td>
                                        <input type="text"  name="qty[]" value="${info.qty}" id="qty${index}" readonly />
                                    </td>
                                    <td>
                                        <input type="text" name="Quantity[]" value="${info.Quantity}" id="Quantity${index}" readonly />
                                    </td>

                                    <td>
                                         <input type="text"  name="rem_qty[]" value="${info.rem_qty}" id="rem_qty${index}" readonly/>
                                    </td>
                                        <td>
                                        <input type="text"  name="currdispatch_qty[]" value="" oninput="validateQuantitiesforindex(${index})" id="currdispatch_qty${index}" required/>                                    </td>
                                </tr>`;
                        });

                        // Update the table body with the fetched data
                        $('#recordsTableBody').html(tableBody);
                    } else {
                        // No records found, reset the table
                        resetTable();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", error);
                    alert("An error occurred while fetching the records.");
                }
            });
        } else {
            // No order is selected, reset the table
            resetTable();
        }
    }

    function resetTable() {
        $('#recordsTableBody').html('');
    }
</script>

<script>
    // This function is triggered when a customer is selected
function loadOrders() {
    const customerID = $('#customer_name').val(); // Get selected customer ID

    if (customerID) {
        // Perform AJAX request to fetch orders related to the selected customer
        $.ajax({
            url: "{{ route('admin.getOrder') }}", // Backend route to get orders for the customer
            type: "GET",
            data: {
                customerID: customerID, // Send customer ID
                _token: "{{ csrf_token() }}" // CSRF token for security
            },
            success: function(response) {
                // Check if there are orders
                if (response.orders && response.orders.length > 0) {
                    let orderOptions = '<option value="">Select Order</option>';
                    // Loop through the orders and add to the order dropdown
                    response.orders.forEach((order) => {
                        orderOptions += `<option value="${order.id}">${order.id}</option>`;
                    });

                    // Update the order dropdown with the fetched orders
                    $('#order_no').html(orderOptions);
                } else {
                    // No orders found, clear the order dropdown
                    $('#order_no').html('<option value="">No Orders Available</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", error);
                alert("An error occurred while fetching orders.");
            }
        });
    } else {
        // Clear the order dropdown if no customer is selected
        $('#order_no').html('<option value="">Select Order</option>');
    }
}


function validateQuantities() {
        // Get the values of rem_qty and currdispatch_qty
        var remQty = parseFloat(document.getElementById('rem_qty').value);
        var currDispatchQty = parseFloat(document.getElementById('currdispatch_qty').value);

        // Check if currdispatch_qty is greater than rem_qty
        if (currDispatchQty > remQty) {
            // If currdispatch_qty is greater than rem_qty, reset currdispatch_qty to rem_qty
            document.getElementById('currdispatch_qty').value = remQty;

            // Optionally, alert the user or display a message
            alert('Current dispatch quantity cannot be greater than remaining quantity.');
        }
    }


    function validateQuantitiesforindex(index) {
    // Get the specific row's rem_qty and currdispatch_qty values using the index
    var remQty = parseFloat(document.getElementById(`rem_qty${index}`).value);
    var currDispatchQty = parseFloat(document.getElementById(`currdispatch_qty${index}`).value);

    // Check if currdispatch_qty is greater than rem_qty
    if (currDispatchQty > remQty) {
        // If currdispatch_qty is greater than rem_qty, reset currdispatch_qty to rem_qty
        document.getElementById(`currdispatch_qty${index}`).value = remQty;

        // Optionally, alert the user or display a message
        alert('Current dispatch quantity cannot be greater than remaining quantity.');
    }
}

</script>
