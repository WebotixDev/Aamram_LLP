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
        <div class="form-group col-md-3">
            <label for="Invoicenumber">Stock No <span class="required">*</span></label>
            <input type="text" id="Invoicenumber" name="Invoicenumber" value="{{ old('Invoicenumber', $nextBillNo)}}"
                class="required form-control" readonly>
        </div>



        <div class="form-group col-md-2">
            <label>Stock Date <span class="required" style="color:red;">*</span></label>

            <input class="datepicker-here form-control" id="billdate" name="billdate" type="text"
                value="{{ isset($purchase_details->billdate) ? \Carbon\Carbon::parse($purchase_details->billdate)->format('d-m-Y') : (old('billdate') ?? date('d-m-Y')) }}"
                data-language="en" placeholder="Enter Date" data-date-format="dd-mm-yyyy" data-auto-close="true">

            @error('billdate')
            <span class="text-danger"><strong>{{ $message }}</strong></span>
            @enderror
        </div>


        <div class="form-group col-md-3">
            <div class="mb-3">
                <label>Types</label>
                <select class="form-control" id="type" name="type" >
                <option value="mango" <?php echo isset($purchase_details) && $purchase_details['type'] == 'mango' ? 'selected' : ''; ?> @checked(true)>Mango</option>
                <option value="other" <?php echo isset($purchase_details) && $purchase_details['type'] == 'other' ? 'selected' : ''; ?>>Other</option>
                </select>

            </div>
            </div>
      <?php

      if(isset($purchase_details->stock)){
    $servicesId=$purchase_details->product;
    $lastqty=$purchase_details->qty;
      $Date = date('Y-m-d');
       $stock = \App\Helpers\Helpers::getstockpeti($servicesId, $Date);
       $finalstock=$stock+$lastqty;



        ?>
        <div class="form-group col-md-4">
            <label>Select Products</label>
            <select class="select2" id="servicess" name="product" onchange="getstock(this.id);" readonly>
                <option value="">Select Products</option>

            </select>
        </div>
        <?php }else{

            $finalstock='';
            ?>

            <div class="form-group col-md-4">
            <label>Select Products</label>
            <select class="select2" id="servicess" name="product" onchange="getstock(this.id);">
                <option value="">Select Products</option>

            </select>
        </div>

<?php } ?>

    </div>

<div id="otherFields" class="row pt-3" style="display: none;">
        <div class="form-group col-md-4">
            <label> Sizes</label>
            <input type="text" class="required form-control" id="sizes" name="product_size"
                value="{{ isset($purchase_details->product_size) ? $purchase_details->product_size : (old('product_size') ?? 'Peti') }}"
                required>
        </div>

        <div class="form-group col-md-4">
            <label>Harvest <span class="required" style="color:red;">*</span></label>
            <input type="text" class="required form-control" id="stock" name="stock"
                value="<?php echo $finalstock; ?>" required>
        </div>

       <div class="form-group col-md-4">
    <label> Quantity <span class="required" style="color:red;">*</span></label>
    <input type="text" class="required form-control" id="qty" name="qty"
        value="<?php echo $purchase_details->qty?>" required onkeyup="validateQty()" readonly>
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
                                <select class="select2" name="productsizes_<?= $i ?>" id="productsizes_<?= $i ?>"
                                    onchange="fetchSizes(this.id);ReflectQty();" required>
                                    <option value="">Select Sizes</option>
                                    @php
                                    $product_details = DB::table('product_details')
                                    ->where('sizeoff', 0)
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
                                <select class="select2" id="stage_<?= $i ?>" name="stage_<?= $i ?>" required>
                                <option value="">Select Sizes</option>

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
                                <input type="text" id="rate_<?= $i ?>" name="rate_<?= $i ?>"
                                    value="<?= $purchase_product_list1->rate ?>" class="form-control"
                                    onkeyup="Gettotal();" required>
                                <input type="hidden" id="size_<?= $i ?>" class="form-control" name="size_<?= $i ?>"
                                value="<?= $purchase_product_list1->size ?>"  required>

                            </td>
                            <td>

                                <input type="text" id="quantity_<?= $i ?>" name="quantity_<?= $i ?>"
                                    value="<?= $purchase_product_list1->Quantity ?>" class="form-control"
                                    onkeyup="Gettotal();"  oninput="ReflectQty()"; required>
                            </td>

                            <td>
                            <button id="button_<?= $i ?>" name="button_<?= $i ?>" type="button" class=""
                            onclick="deleteRow(this.id)">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else : ?>
                        <?php $i = 1; ?>
                        <tr id="row_<?= $i ?>">

                            <td>
                                <select class="select2" name="productsizes_<?= $i ?>" id="productsizes_<?= $i ?>"
                                    onchange="fetchSizes(this.id);ReflectQty();" required>
                                    <option value="">Select Sizes</option>
                                    <?php
                                    $product_details = DB::table('product_details')
                                    ->where('sizeoff', 0)
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
                                <select class="select2" id="stage_<?= $i ?>" name="stage_<?= $i ?>" required>
                                <option value="">Select Sizes</option>
                                    <option value="Raw">Raw</option>
                                    <option value="Semi Ripe">Semi Ripe</option>
                                    <option value="Ripe">Ripe</option>
                                </select>
                            </td>

                            <td>
                                <input type="text" id="rate_<?= $i ?>" name="rate_<?= $i ?>" class="form-control"
                                    onkeyup="Gettotal();" required>
                                <input type="hidden" id="size_<?= $i ?>" class="form-control" name="size_<?= $i ?>"
                                    required>

                            </td>

                            <td>

                                <input type="text" id="quantity_<?= $i ?>" name="quantity_<?= $i ?>" class="form-control"
                                    onkeyup="Gettotal();" oninput="ReflectQty()"; required>
                            </td>

                            <td>
                            <button id="button_<?= $i ?>" name="button_<?= $i ?>" type="button" class=""
                            onclick="deleteRow(this.id)">Delete</button>
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
                            <select class="select2" name="productsizes_${rowCount}" id="productsizes_${rowCount}" required onchange="fetchSizes(this.id);ReflectQty();">
                                    <option value="">Select Sizes</option>
                                    <?php
                                    $product_details = DB::table('product_details')
                                    ->where('sizeoff', 0)
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
                                <select class="select2" id="stage_${rowCount}" name="stage_${rowCount}" required>
                                   
                                <option value="">Select Stage</option>
                                <option value="Raw">Raw</option>
                                    <option value="Semi Ripe">Semi Ripe</option>
                                    <option value="Ripe">Ripe</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="rate_${rowCount}" class="form-control" name="rate_${rowCount}" onkeyup="Gettotal();" required>
                                <input type="hidden" id="size_${rowCount}" class="form-control" name="size_${rowCount}"  required>

                            </td>
                            <td>
                                <input type="text" id="quantity_${rowCount}" class="form-control" name="quantity_${rowCount}" onkeyup="Gettotal();"  oninput="ReflectQty()"; required>
                            </td>
                            <td>
                               <button id="button_${rowCount}" name="button_${rowCount}" type="button" class="" onclick="deleteRow(this.id)">Delete</button>
                            
                                </td>
                        </tr>
                    `;

    tableBody.insertAdjacentHTML('beforeend', newRow);

    // Update the hidden input field with the latest row count
    document.getElementById('cnt').value = rowCount;

    $(".select2").select2();
}

// Function to delete a row
// function deleteRow(rowId) {
//     const row = document.getElementById(`row_${rowId}`);
//     if (row) {
//         row.remove();

//         // Update the hidden input field with the latest row count
//         const tableBody = document.getElementById('productTable').querySelector('tbody');
//         document.getElementById('cnt').value = tableBody.rows.length;
//         // Optionally, you can trigger the `Gettotal()` function if needed
//         Gettotal();

//         ReflectQty();
//     }
// }

function deleteRow(rwcnt)
{
    var id=rwcnt.split("_");
    var cnt=id[1];
    var count=$("#cnt").val();	
    if(count>1)
    {
        var r=confirm("Are you sure!");
        if (r==true)
        {	

            $("#row_"+cnt).remove();

            for(var k=cnt; k<=count; k++)
            {
                var newId=k-1;

                jQuery("#row_"+k).attr('id','row_'+newId);

                jQuery("#idd_"+k).attr('name','idd_'+newId);
                jQuery("#idd_"+k).attr('id','idd_'+newId);
                jQuery("#idd_"+newId).html(newId); 

                jQuery("#services_"+k).attr('name','services_'+newId);
                jQuery("#services_"+k).attr('id','services_'+newId);

                jQuery("#productsizes_"+k).attr('name','productsizes_'+newId);
                jQuery("#productsizes_"+k).attr('id','productsizes_'+newId);


                jQuery("#stage_"+k).attr('name','stage_'+newId);
                jQuery("#stage_"+k).attr('id','stage_'+newId);

                jQuery("#stock_"+k).attr('id','stock_'+newId);

                jQuery("#rate_"+k).attr('name','rate_'+newId);
                jQuery("#rate_"+k).attr('id','rate_'+newId);

             
                
                
                jQuery("#quantity_"+k).attr('name','quantity_'+newId);
                jQuery("#quantity_"+k).attr('id','quantity_'+newId);

              

                jQuery("#size_"+k).attr('name','size_'+newId);
                jQuery("#size_"+k).attr('id','size_'+newId);
                
                jQuery("#button_"+k).attr('name','button_'+newId);
                jQuery("#button_"+k).attr('id','button_'+newId);
        

            }
            jQuery("#cnt").val(parseFloat(count-1)); 
            
        }
        
    }
    else 
    {
        alert("Can't remove row Atleast one row is required");
        return false;
    }

    // Gettotal(); 

    ReflectQty();
}


function ReflectQty() {
    // Define categories of product sizes
    if (!window.ReflectQtyEnabled) {
        return; // Don't run the logic if disabled
    }
       var first =[
        "Box (3kg to 3.2kg) 10 to 15 Nos",
        "Box A1 (271-300 gm) 11/12 Nos",
    ];
    var validSizes = [
        "Box A2 (241-270 gm) 12 Nos",
        "Box A3 (211-240 gm) 14/15 Nos",
        "Box A4 (181-210 gm) 16/17 Nos"
    ];

    var second = [
        "Half Peti (10kg) 24 to 42 Nos",
        "Half Peti A1 (271-300 gm) 24 Nos",
        "Half Peti A2 (241-270 gm) 30 Nos",
        "Half Peti A3 (211-240 gm) 36 Nos",
        "Half Peti A4 (181-210 gm) 42 Nos"
    ];

    var third = [
        "Full Peti/Crate (18kg to 20kg) 4D/5D/6D/7D",
        "Full Peti/Crate A1 (271-300 gm) 48 Nos",
        "Full Peti/Crate A2 (241-270 gm) 60 Nos",
        "Full Peti/Crate A3 (211-240 gm) 72 Nos",
        "Full Peti/Crate A4 (181-210 gm) 84 Nos"
    ];


    // Initialize total sum
    let totalBoxesSum = 0;

    // Get stock value and ensure it's a valid number
    let stock = parseFloat(document.getElementById('stock').value);

    // Loop through all select elements to update the quantity fields
    let selects = document.querySelectorAll('select[id^="productsizes_"]');

    selects.forEach(select => {
        // Get the selected size value
        let selectedSize = select.value;

        // Assign appropriate items per box based on product size
        let itemsPerBox = 1;
        
        if (first.includes(selectedSize)) {
            itemsPerBox = 4;
        }else if (validSizes.includes(selectedSize)) {
            itemsPerBox = 5;
        } else if (second.includes(selectedSize)) {
            itemsPerBox = 2;
        } else if (third.includes(selectedSize)) {
            itemsPerBox = 1;
        } else {
            alert("Invalid product size selected.");
            return;
        }

        let quantityField = document.querySelector(`#quantity_${select.id.split('_')[1]}`);

        if (quantityField) {
            let quantity = parseFloat(quantityField.value) || 0; // Get entered quantity, default to 0

            let totalBoxes = quantity / itemsPerBox; // Perform normal division

            // Check if the totalBoxesSum exceeds the stock
            if (totalBoxesSum + totalBoxes > stock) {
                alert("Total quantity exceeds available stock!");

                // Clear the specific quantity field that exceeded the stock
                quantityField.value = ''; // Clear the field for the current size/quantity

                // Prevent adding this quantity to the total sum
                return;  // Exit the function to stop further calculations
            }

            totalBoxesSum += totalBoxes; // Add to total sum
        }
    });

    // Update qty field with the total calculated sum
    document.getElementById('qty').value = totalBoxesSum.toFixed(1);

    Gettotal();
}


function Gettotal() {
    // Initialize total amount to 0
    let totalAmount = 0;

    // Loop through each row where both rate and quantity inputs exist
    let rows = document.querySelectorAll('[id^="quantity_"]'); // Assuming quantity fields are identified with 'quantity'

    rows.forEach(function(row) {
        let rowIndex = row.id.replace("quantity_", ""); // Extract rowIndex from quantity input id

        // Get the rate value for the corresponding row (rate field should have ids like 'rate1', 'rate2', etc.)
        let rate = parseFloat(document.getElementById("rate_" + rowIndex).value) ||
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
    var rowIndex = id.replace('productsizes_', ''); // Ensure rowIndex is derived correctly
    var productsizes = $("#productsizes_" + rowIndex).val();

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
                        $("#rate_" + rowIndex).val(response.productdetail.purch_price);
                        $("#size_" + rowIndex).val(response.productdetail.id);
                    }
                }


               // $("#stock").val(response.stock);


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

function getstock(id){
    var services = $("#servicess").val();
    $.ajax({
        url: "{{ route('admin.inward.getstock') }}",
        type: "GET",
        data: {
            services: services,
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            if (response.status === "success") {
                $("#stock").val(response.stock.toFixed(1)); // Formats the stock value to 2 decimal places
                
            }
        },
        error: function(xhr, status, error) {
            alert("NoProduct.");
        }
    });

}

</script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const typeDropdown = document.getElementById("type");
        const otherFields = document.getElementById("otherFields");

        function toggleFields() {
            if (typeDropdown.value === 'mango') {
                otherFields.style.display = 'flex'; // Show the fields
                window.ReflectQtyEnabled = true;
            } else {
                otherFields.style.display = 'none'; // Hide the fields
                window.ReflectQtyEnabled = false;
            }
        }

        // Initial call on page load
        toggleFields();

        // Call when dropdown value changes
        typeDropdown.addEventListener("change", toggleFields);
    });



</script>

<script>
    $(document).ready(function () {
        var selectedProductId = "{{ isset($purchase_details) ? $purchase_details->product : '' }}";

        $('#type').on('change', function () {
            var type = $(this).val();
            var url = "{{ route('admin.product.type', ':type') }}";
            url = url.replace(':type', type);

            $.ajax({
                url: url,
                type: 'GET',
                success: function (data) {
                    $('#servicess').empty().append('<option value="">-- Select Products --</option>');

                    $.each(data, function (key, value) {
                        var selected = (value.id == selectedProductId) ? 'selected' : '';
                        $('#servicess').append('<option value="' + value.id + '" ' + selected + '>' + value.product_name + '</option>');
                    });
                },
                error: function () {
                    alert('Failed to fetch products.');
                }
            });
        });

        // Trigger change on page load if editing an existing product
        @if(isset($purchase_details))
            $('#type').trigger('change');
        @endif
    });
</script>


