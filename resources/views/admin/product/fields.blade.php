<div class="form theme-form">
    @php
    $productSizes = DB::table('product_size')->get();
@endphp
    <!-- General Information Section -->
      <!-- General Information Section -->
      <div class="row">


                            <div class="form-group col-md-3">
                                <label for="product_name">Product Name <span class="required">*</span></label>
                                <input type="text" name="product_name" id="product_name" value="<?php if(isset($products)){ echo $products->product_name; } ?>" placeholder="Product Name" class="form-control"  required>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="moq">MOQ <span class=""></span></label>
                                <input type="text" name="moq" id="moq" value="<?php if(isset($products)){ echo $products->moq; } ?>" placeholder="MOQ" class="form-control" required>
                            </div>


                        <br> <br>



                            <div class="form-group col-md-3">
                                <label for="description">Description</label>
                                <textarea name="description" id="description"  placeholder="Description" class="form-control required " required><?php if(isset($products)){ echo $products->description; } ?></textarea>
                            </div>

                        </div>
<br>
                        <div class="row">

                            <div class="form-group col-md-3" >
                            <label>CGST % <span class=" required_lbl"></span></label>
                            <input type="text" name='cgst' id="cgst"  placeholder="CGST" value="<?php if(isset($products)){ echo $products->cgst; } else{  echo "0"; } ?>"  class="  form-control" >

                            </div>



                            <div class="form-group col-md-3" >
                            <label>SGST %<span class=" "></span></label>
                            <input type="text" name='sgst' id="sgst" placeholder="SGST" value="<?php if(isset($products)){ echo $products->sgst; } else{  echo "0"; }?>"  class="  form-control  " >

                            </div>


                            <div class="form-group col-md-3" >
                            <label>IGST %<span class=" "></span></label>
                            <input type="text" name='igst' id="igst" placeholder="IGST" value="<?php if(isset($products)){ echo $products->igst; }else{  echo "0"; } ?>"  class="  form-control  " >
                            </div>

              <div class="form-group col-md-3 pt-2">
                <label for="status" class="col-md-6 control-label">
                    Status <span class="required required_lbl"></span>
                </label>
            <div class="col-sm-7">
                <label>
                    <input type="radio" name="status" value="1"
                        {{ !isset($products->status) || $products->status == '1' ? 'checked' : '' }}> Yes
                </label>
                <label style="margin-left: 50px;">
                    <input type="radio" name="status" value="0"
                        {{ isset($products->status) && $products->status == '0' ? 'checked' : '' }}> No
                </label>
            </div>

            </div>


                            </div>
                            <br>
<div class="row">
                            <div class="form-group col-md-4">
                                <label for="logo" class="col-md-6 control-label">
                                    Image <span class="required required_lbl"></span>
                                </label>
                                <div class="col-sm-7">
                                    <div id="logoimg_div" style="display: {{ isset($products->img) && !empty($products->img) ? 'block' : 'none' }};">
                                        <img src="{{ isset($products->img) && !empty($products->img) ? asset('public/' . $products->img) : asset('public/uploads/product_img/default-sign-path.jpg') }}"
                                            width="70px" height="50px" id="logo_preview">
                                        <span class="fa fa-trash" style="cursor:pointer; height:25px; width:25px" onclick="removeImage('logo');"></span>
                                        <input type="hidden" name="img" id="logo_hidden" value="{{ $products->img ?? '' }}" />
                                    </div>
                                    <div id="logoimgdiv">
                                        <input type="file" id="logo" name="img" class="form-control" onchange="previewImage(event, 'logo_preview', 'logo_hidden');" />
                                    </div>
                                </div>
                            </div>



</div>

                        <!-- Dynamic Table -->
                        <h4>Product Variants</h4>
                        <table class="table" id="productTableee">
                            <thead>
                                <tr>
                                    <th>Product Size/Make</th>
                                    <th>Purchase Price</th>
                                    <th>Sale Price </th>
                                    <th>Status (Size Show In Sale Only)</th>
                                    <th width="10%">Action</th>
                                    <th width="10%">Disable</th>
                                </tr>
                            </thead>
                            <tbody>
                        <?php $i = 0; ?>
                        <?php if (isset($product_list11) && count($product_list11) > 0) : ?>
                            <?php foreach ($product_list11 as $product_list1) : ?>
                                <?php $i++; ?>
                                   <?php
            // Check if sizeoff is 1, then hide the row using inline style or CSS class
            $hideRow = isset($product_list1->sizeoff) && $product_list1->sizeoff == '1' ? 'style="display:none;"' : '';
        ?>
                                <tr id="row_<?= $i ?>" <?= $hideRow ?>>

                                    <td>

                    <input type="hidden" id="productid<?= $i ?>" value="<?= $product_list1->product_size ?>">

                      <input type="hidden" id="proid<?= $i ?>" name="proid<?= $i ?>" value="<?= $product_list1->id ?>"  class=" required form-control  " >

            <select class="select2 size-select required"
                    id="product_size<?= $i ?>"
                    name="product_size<?= $i ?>"
                    disabled>

                    <option value="">Select Size</option>

                                         @foreach($productSizes as $size)
                                            <option value="{{ $size->id }}"
                                                @if(isset($product_list1->size_id) && $product_list1->size_id == $size->id)
                                                    selected
                                                @endif
                                            >
                                                {{ $size->product_size }}
                                            </option>
                                        @endforeach

                                        </select>
                        <input type="hidden"
                            name="product_size<?= $i ?>"
                            value="<?= $product_list1->size_id ?>">

                                    </td>
                                    <td>
                                        <input type="text" id="purch_price<?= $i ?>" name="purch_price<?= $i ?>" value="<?= $product_list1->purch_price ?>"  class=" required form-control  " required>
                                    </td>
                                    <td>
                                        <input type="text" id="dist_price<?= $i ?>" name="dist_price<?= $i ?>" value="<?= $product_list1->dist_price ?>"  class=" required form-control readonly "  required  >
                                    </td>
                                    <td>
                                    <select  id="status<?= $i ?>" name="status<?= $i ?>" class="required form-control" >
                                            <option value="1" {{ !isset($product_list1->status) || $product_list1->status == '1' ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ isset($product_list1->status) && $product_list1->status == '0' ? 'selected' : '' }}>No</option>
                                        </select>
                                    </td>
                                         <td>
                                @php


                                    // Check if that size is already used in sale_order
                                    $isUsed = DB::table('sale_order')->where('size', $product_list1->id)->exists();
                                @endphp

                                @if(!$isUsed)
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(<?= $i ?>)">Delete</button>
                                @endif
                            </td>


                       <td>
                            @php
                                    // Check if that size is already used in sale_order
              $isUsedd = DB::table('sale_order')->where('size', $product_list1->id)->exists();
                                @endphp
                @if($isUsedd)
                        <label>
                            <input type="radio" name="sizeoff<?= $i ?>" value="1" onclick="return confirm('Are you sure you want to disable?')"
                                <?= !isset($product_list1->sizeoff) || $product_list1->sizeoff == '1' ? 'checked' : '' ?>> Yes
                        </label>
                        <label>
                            <input type="radio" name="sizeoff<?= $i ?>" value="0"
                                <?= isset($product_list1->sizeoff) && $product_list1->sizeoff == '0' ? 'checked' : '' ?>> No
                        </label>
               @endif

                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <!-- If no rows exist, show a default row -->
                            <?php $i = 1; ?>
                            <tr id="row_<?= $i ?>">
                                <td>
                        <select class="select2 size-select required" required id="product_size<?= $i ?>" name="product_size<?= $i ?>">
                                                                <option value="">Select Size</option>

                            @foreach($productSizes as $size)
                                        <option value="{{ $size->id }}">{{ $size->product_size }}</option>
                                    @endforeach
                            </select>
                                </td>
                                <td>
                                    <input type="text" id="purch_price<?= $i ?>" name="purch_price<?= $i ?>"  class=" required form-control  " required>
                                </td>
                                <td>
                                    <input type="text" id="dist_price<?= $i ?>" name="dist_price<?= $i ?>"  class=" required form-control  " required >
                                </td>
                                <td>
                                    <select  id="status<?= $i ?>" name="status<?= $i ?>" class="required form-control" >
                                        <option value="1">Yes</option>
                                        <option value="0" >No</option>
                                    </select>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(<?= $i ?>)">Delete</button>
                                </td>

                                <td>
                    <label>
                        <input type="radio" name="sizeoff<?= $i ?>" value="1" onclick="return confirm('Are you sure you want to disable?')"> Yes
                    </label>
                    <label>
                        <input type="radio" name="sizeoff<?= $i ?>" value="0" checked> No
                    </label>
                    </td>

                            </tr>
                        <?php endif; ?>
                    </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-center">
                                    <input type="hidden" id="cnt" name="cnt" value="<?= $i ?>">
                                    <button type="button" class="btn btn-primary" onclick="addRow()">Add Row</button>                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="text-center pt-5">
                            <button type="submit" class="btn btn-primary">Save</button>

                        <a href="{{ route('admin.product.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
{{-- <script src="{{ asset('assets/js/vendors/select2.full.min.js') }}"></script> --}}
<script src="{{ asset('assets/js/custom-validation/validation.js') }}"></script>

<script>



$(document).ready(function () {

    $(".select2").select2();

    updateSizeOptions();   // IMPORTANT
});
function updateSizeOptions() {
    // Collect all selected values
    let selected = [];
    $('.size-select').each(function() {
        const val = $(this).val();
        if (val) selected.push(val);
    });

    // For each select, update disabled options
    $('.size-select').each(function() {
        const currentVal = $(this).val();
        const $select = $(this);

        $select.find('option').each(function() {
            const val = $(this).val();

            // Disable option if it's selected in another row
            if (val !== "" && selected.includes(val) && val !== currentVal) {
                $(this).attr('disabled', true);
            } else {
                $(this).attr('disabled', false);
            }
        });

        // Refresh Select2 properly
        $select.select2();  // Re-initialize to force redraw of disabled options
        $select.val(currentVal).trigger('change.select2'); // Preserve current value
    });
}

// Trigger update when any size is changed
$(document).on('change', '.size-select', function() {
    updateSizeOptions();
});
 // Function to add a new row
 function addRow() {
                const tableBody = document.getElementById('productTableee').querySelector('tbody');
                const rowCount = tableBody.rows.length + 1; // Calculate row count dynamically

                const newRow = `
                    <tr id="row_${rowCount}">
                        <td>

                         <select class="select2 size-select required" required id="product_size${rowCount}" name="product_size${rowCount}">
                                                            <option value="">Select Size</option>

                            @foreach($productSizes as $size)
                                    <option value="{{ $size->id }}">{{ $size->product_size }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="text" id="purch_price${rowCount}" name="purch_price${rowCount}" class="form-control" required>
                        </td>
                        <td>
                            <input type="text" id="dist_price${rowCount}" name="dist_price${rowCount}" class="form-control" >
                        </td>
                        <td>
                           <select  id="status${rowCount}" name="status${rowCount}" class="required form-control" >
                                        <option value="1">Yes</option>
                                        <option value="0" >No</option>
                                    </select>
                        </td>
                        <td>
                            <button type="button"  id="button${rowCount}" name="button${rowCount}" class="btn btn-danger btn-sm" onclick="deleteRow(${rowCount})">Delete</button>
                        </td>

                                   <td>
                    <label>
                        <input type="radio" id="sizeoff${rowCount}" name="sizeoff${rowCount}" value="1" onclick="return confirm('Are you sure you want to disable?')"> Yes
                    </label>
                    <label>
                        <input type="radio" id="sizeoff${rowCount}" name="sizeoff${rowCount}" value="0" checked> No
                    </label>
                    </td>
                    </tr>
                `;

                tableBody.insertAdjacentHTML('beforeend', newRow);

                // Update the hidden input field with the latest row count
                document.getElementById('cnt').value = rowCount;

                // Reinitialize select2 for the new select dropdown
$('#product_size' + rowCount).select2();

updateSizeOptions();
                 const selectedType = $('#type').val();
        if (selectedType) {
            loadSizes(selectedType, 'product_size' + rowCount);
        }
     }


                // Function to delete a row
                function deleteRow(rowId) {
                    // const row = document.getElementById(`row_${rowId}`);
                    // if (row) {
                    //     row.remove();

                    //     // Update the hidden input field with the latest row count
                    //     const tableBody = document.getElementById('productTableee').querySelector('tbody');
                    //     document.getElementById('cnt').value = tableBody.rows.length;
                    // }


         var cnt = rowId;
        var count = $("#cnt").val();

        if (count > 1) {
            var r = confirm("Are you sure!");
            if (r == true) {
                $("#row_" + cnt).remove();

                for (var k = parseInt(cnt) + 1; k <= count; k++) {
                    var newId = k - 1;

                    $("#row_" + k).attr('id', 'row_' + newId);

                    $("#product_size" + k).attr({ id: 'product_size' + newId, name: 'product_size' + newId });
                    $("#purch_price" + k).attr({ id: 'purch_price' + newId, name: 'purch_price' + newId });
                    $("#original_price" + k).attr({ id: 'original_price' + newId, name: 'original_price' + newId });
                    $("#dist_price" + k).attr({ id: 'dist_price' + newId, name: 'dist_price' + newId });
                    $("#web_price" + k).attr({ id: 'web_price' + newId, name: 'web_price' + newId });
                    $("#status" + k).attr({ id: 'status' + newId, name: 'status' + newId });
                    $("#sizeoff" + k).attr({ id: 'sizeoff' + newId, name: 'sizeoff' + newId });
                    $("#button" + k).attr('id', 'button' + newId).attr('onclick', `button('button${newId}')`);
                }

                $("#cnt").val(parseInt(count - 1));
            }
        } else {
            alert("Can't remove row. At least one row is required");
        }
                updateSizeOptions();

                }
</script>
<script>
function previewImage(event, previewId, hiddenFieldId) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
            document.getElementById(hiddenFieldId).value = file.name;
            document.getElementById(previewId).parentElement.style.display = 'block'; // Show the image div
        };
        reader.readAsDataURL(file);
    }
}

function removeImage(field) {
    document.getElementById(`${field}_preview`).src = 'default-placeholder.jpg';
    document.getElementById(`${field}_hidden`).value = '';
    document.getElementById(field).value = ''; // Clear file input
    document.getElementById(`${field}_preview`).parentElement.style.display = 'none'; // Hide the image div
}

</script>

