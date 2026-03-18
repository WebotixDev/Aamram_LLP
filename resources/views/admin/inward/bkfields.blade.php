<div class="form theme-form">
    @php



    // Check if $invoice exists (edit mode)
    if (isset($invoice) && $invoice->Invoicenumber) {
    $nextBillNo = $invoice->Invoicenumber; // Use the existing invoice number
    } else {
    // Generate the next invoice number for create mode
    $lastBillNo = DB::table('purchase_details')->max('Invoicenumber');
    $nextBillNo = $lastBillNo ? $lastBillNo + 1 : 1;
    }


    @endphp



    <div class="row">
        <div class="form-group col-md-4">
            <label for="Invoicenumber">Stock No <span class="required">*</span></label>
            <input type="text" id="Invoicenumber" name="Invoicenumber" value="{{ old('Invoicenumber', $nextBillNo)}}"
                class="required form-control" readonly>
        </div>



        <div class="form-group col-md-4">
            <label>Stock Date <span class="required" style="color:red;">*</span></label>

            <input class="datepicker-here form-control" id="billdate" name="billdate" type="text"
                value="{{ isset($purchase_details->billdate) ? \Carbon\Carbon::parse($purchase_details->billdate)->format('d-m-Y') : (old('billdate') ?? date('d-m-Y')) }}"
                data-language="en" placeholder="Enter Date" data-date-format="dd-mm-yyyy">

            @error('billdate')
            <span class="text-danger"><strong>{{ $message }}</strong></span>
            @enderror
        </div>


        <div class="form-group col-md-4">
            <label>Select Products</label>
            <select class="select2" id="servicess" name="product" onchange="fetchSizes(this.id);">
                <option value="">Select Products</option>
                @foreach (DB::table('products')->get() as $product)
                <option value="{{ $product->id }}" @if(isset($purchase_details) && $product->id ==
                    $purchase_details->product)
                    selected
                    @endif>
                    {{ $product->product_name }}
                </option>
                @endforeach
            </select>
        </div>


    </div>


    <div class="row pt-3">
        <div class="form-group col-md-4">
            <label> Sizes</label>
            <input type="text" class="required form-control" id="sizes" name="product_size"
                value="{{ isset($purchase_details->product_size) ? $purchase_details->product_size : (old('product_size') ?? 'Peti') }}"
                required>
        </div>

        <div class="form-group col-md-4">
            <label>Harvest <span class="required" style="color:red;">*</span></label>
            <input type="text" class="required form-control" id="stock" name="stock"
                value="<?php echo $purchase_details->stock?>" required>
        </div>

       <div class="form-group col-md-4">
    <label> Quantity <span class="required" style="color:red;">*</span></label>
    <input type="text" class="required form-control" id="qty" name="qty"
        value="<?php echo $purchase_details->qty?>" required onkeyup="validateQty()">
    <small id="qty-error" style="color:red; display:none;">Quantity cannot be greater than stock!</small>
</div>

    </div>

    <br><br>

    <div id="proformatable pt-5">
        <div class="row">
            <div class="col-md-12">
                <table id="productTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <!-- <th>Types<span class="required" style="color:red;">*</span></th> -->
                            <th>PRODUCT SIZE<span class="required" style="color:red;">*</span></th>
                            <th>STAGE<span class="required" style="color:red;">*</span></th>
                            <th>STOCK PURCHASE RATE</th>
                            <th>QUANTITY<span class="required" style="color:red;">*</span></th>
                            <th width='1%'>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 0; ?>
                        <?php if (isset($purchase_product_list) && count($purchase_product_list) > 0) : ?>
                        <?php foreach ($purchase_product_list as $purchase_product_list1) : ?>
                        <?php $i++; ?>
                        <tr id="row_<?= $i ?>">

                            <td>
                                <select class="select2" name="productsizes<?= $i ?>" id="productsizes<?= $i ?>"
                                    onchange="fetchSizes(this.id);" required>
                                    <option value="">Select Sizes</option>
                                    @php
                                    $product_details = DB::table('product_details')
                                    ->select('product_size')
                                    ->groupBy('product_size')
                                    ->get();
                                    @endphp
                                    @foreach ($product_details as $product)
                                    <option value="{{ $product->product_size }}" @if(isset($purchase_product_list1) && $product->product_size ==
                    $purchase_product_list1->productsizes)
                    selected
                    @endif>
                                        {{ $product->product_size }}
                                    </option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <select class="select2" id="stage<?= $i ?>" name="stage<?= $i ?>">
                                    <option value="Raw"
                                        <?= $purchase_product_list1->stage == 'Raw' ? 'selected' : '' ?>>Raw</option>
                                    <option value="Semi Ripe"
                                        <?= $purchase_product_list1->stage == 'Semi Ripe' ? 'selected' : '' ?>>Semi Ripe
                                    </option>
                                    <option value="Ripe"
                                        <?= $purchase_product_list1->stage == 'Ripe' ? 'selected' : '' ?>>Ripe</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="rate<?= $i ?>" name="rate<?= $i ?>"
                                    value="<?= $purchase_product_list1->rate ?>" class="form-control"
                                    onkeyup="Gettotal();" required>
                                <input type="hidden" id="size<?= $i ?>" class="form-control" name="size<?= $i ?>"
                                value="<?= $purchase_product_list1->size ?>"  required>

                            </td>
                            <td>

                                <input type="text" id="quantity<?= $i ?>" name="quantity<?= $i ?>"
                                    value="<?= $purchase_product_list1->Quantity ?>" class="form-control"
                                    onkeyup="Gettotal();" required>
                            </td>

                            <td>
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="deleteRow(<?= $i ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <?php $i = 1; ?>
                        <tr id="row_<?= $i ?>">

                            <td>
                                <select class="select2" name="productsizes<?= $i ?>" id="productsizes<?= $i ?>"
                                    onchange="fetchSizes(this.id);" required>
                                    <option value="">Select Sizes</option>
                                    <?php
                                    $product_details = DB::table('product_details')
                                    ->select('product_size')
                                    ->groupBy('product_size')
                                    ->get();
                                 ?>
                                    <?php  foreach ($product_details as $product){ ?>
                                    <option value="{{ $product->product_size }}">
                                        {{ $product->product_size }}
                                    </option>
                                    <?php } ?>

                                </select>
                            </td>

                            <td>
                                <select class="select2" id="stage<?= $i ?>" name="stage<?= $i ?>">
                                    <option value="Raw">Raw</option>
                                    <option value="Semi Ripe">Semi Ripe</option>
                                    <option value="Ripe">Ripe</option>
                                </select>
                            </td>

                            <td>
                                <input type="text" id="rate<?= $i ?>" name="rate<?= $i ?>" class="form-control"
                                    onkeyup="Gettotal();" required>
                                <input type="hidden" id="size<?= $i ?>" class="form-control" name="size<?= $i ?>"
                                    required>

                            </td>

                            <td>

                                <input type="text" id="quantity<?= $i ?>" name="quantity<?= $i ?>" class="form-control"
                                    onkeyup="Gettotal();" required>
                            </td>

                            <td>
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="deleteRow(<?= $i ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-center">
                                <input type="hidden" id="cnt" name="cnt" value="<?= $i ?>">
                                <button type="button" class="btn btn-primary" onclick="addRow()">Add Row</button>
                            </td>
                            <td>Total</td>
                            <td><input type="text" readonly id="totalproamt" name="totalproamt"
                                    value="<?php echo $purchase_details->Tquantity;  ?>" class="form-control"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <br>
    <div class="row">
        <div class="col">
            <div class="text-center">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button> <a
                    href="{{ route('admin.inward.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
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


// Function to add a new row
function addRow() {
    const tableBody = document.getElementById('productTable').querySelector('tbody');
    const rowCount = tableBody.rows.length + 1; // Calculate row count dynamically
    const newRow = `
                        <tr id="row_${rowCount}">

                            <td>
                            <select class="select2" name="productsizes${rowCount}" id="productsizes${rowCount}" required onchange="fetchSizes(this.id);">
                                    <option value="">Select Sizes</option>
                                    <?php
                                    $product_details = DB::table('product_details')
                                    ->select('product_size')
                                    ->groupBy('product_size')
                                    ->get();
                                 ?>
                                   <?php  foreach ($product_details as $product){ ?>
                                    <option value="{{ $product->product_size }}">
                                        {{ $product->product_size }}
                                    </option>
                                    <?php } ?>

                                </select>

                                </select>
                            </td>
                            <td>
                                <select class="select2" id="stage${rowCount}" name="stage${rowCount}">
                                    <option value="Raw">Raw</option>
                                    <option value="Semi Ripe">Semi Ripe</option>
                                    <option value="Ripe">Ripe</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="rate${rowCount}" class="form-control" name="rate${rowCount}" onkeyup="Gettotal();" required>
                                <input type="hidden" id="size${rowCount}" class="form-control" name="size${rowCount}"  required>

                            </td>
                            <td>
                                <input type="text" id="quantity${rowCount}" class="form-control" name="quantity${rowCount}" onkeyup="Gettotal();" required>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(${rowCount})">Delete</button>
                            </td>
                        </tr>
                    `;

    tableBody.insertAdjacentHTML('beforeend', newRow);

    // Update the hidden input field with the latest row count
    document.getElementById('cnt').value = rowCount;

    $(".select2").select2();
}

// Function to delete a row
function deleteRow(rowId) {
    const row = document.getElementById(`row_${rowId}`);
    if (row) {
        row.remove();

        // Update the hidden input field with the latest row count
        const tableBody = document.getElementById('productTable').querySelector('tbody');
        document.getElementById('cnt').value = tableBody.rows.length;
        // Optionally, you can trigger the `Gettotal()` function if needed
        Gettotal();
    }
}



function Gettotal() {
    // Initialize total amount to 0
    let totalAmount = 0;

    // Loop through each row where both rate and quantity inputs exist
    let rows = document.querySelectorAll('[id^="quantity"]'); // Assuming quantity fields are identified with 'quantity'

    rows.forEach(function(row) {
        let rowIndex = row.id.replace("quantity", ""); // Extract rowIndex from quantity input id

        // Get the rate value for the corresponding row (rate field should have ids like 'rate1', 'rate2', etc.)
        let rate = parseFloat(document.getElementById("rate" + rowIndex).value) ||
            0; // Default to 0 if not a number
        let quantity = parseFloat(row.value) || 0; // Default to 0 if not a number

        // Calculate the total amount for the row
        let rowAmount = rate * quantity;

        // Add the row amount to the total
        totalAmount += rowAmount;
    });

    // Update the total amount in the 'totalproamt' field
    document.getElementById('totalproamt').value = totalAmount.toFixed(2); // Display total with 2 decimal places
}
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function fetchSizes(id) {
    var services = $("#servicess").val();
    var rowIndex = id.replace('productsizes', ''); // Ensure rowIndex is derived correctly
    var productsizes = $("#productsizes" + rowIndex).val();

    $.ajax({
        url: "{{ route('admin.inward.get-sizes') }}",
        type: "GET",
        data: {
            services: services,
            productsizes: productsizes,
            rowindex: rowIndex,
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            if (response.status === "success") {
                if (response.productdetail) {
                    if (response.productdetail.purch_price) {
                        $("#rate" + rowIndex).val(response.productdetail.purch_price);
                        $("#size" + rowIndex).val(response.productdetail.id);
                    }
                }


                $("#stock").val(response.stock);


            }
        },
        error: function(xhr, status, error) {
            alert("No Size For This Product.");
        }
    });
}
</script>
<script>
    function validateQty() {
        var stock = parseFloat(document.getElementById("stock").value) || 0;
        var qtyInput = document.getElementById("qty");
        var qty = parseFloat(qtyInput.value) || 0;
        var errorMsg = document.getElementById("qty-error");

        if (qty > stock) {
            errorMsg.style.display = "block"; // Show error message
            qtyInput.value = stock; // Reset qty to stock value
        } else {
            errorMsg.style.display = "none"; // Hide error message
        }
    }
</script>

