<div class="form theme-form">
@php
// -----------------------
// Determine invoice display
// -----------------------
$invoiceValue = '';
$invoiceNumber = '';
$batchValue = '';
$batchNumber = '';
if (isset($invoice)) {
    if (is_object($invoice)) { // edit mode
        $invoiceValue = $invoice->Invoicenumber ?? '';
        $invoiceNumber = $invoice->invoice_no ?? '';
        $batchValue = $invoice->batch_number ?? '';
        $batchNumber = $invoice->batch_no ?? '';
    } elseif (is_array($invoice)) {
        $invoiceValue = $invoice['formatted'] ?? '';
        $invoiceNumber = $invoice['number'] ?? '';
         $batchValue = $batch['formatted'] ?? '';
        $batchNumber = $batch['number'] ?? '';
    }
}


@endphp
@php
$isEdit = isset($purchase_details) ? 1 : 0;
@endphp
          <div class="row">
            <div class="form-group col-md-3">
                <label>Location <span class="required" style="color:red;">*</span></label>
                <select class="form-select select2" id="location_id"  onchange="getInvoiceBatch(this.value)" name="location_id" data-placeholder="Select " required>
                    <option value="">Select Location</option>
                    @foreach(DB::table('location')->get() as $location)
                        <option value="{{ $location->id }}"
                            @if(isset($purchase_details->location_id) && $purchase_details->location_id == $location->id)
                                selected
                            @endif
                        >
                            {{ $location->location }}
                        </option>
                    @endforeach
                </select>
<input type="hidden" id="original_location_id" value="{{ $purchase_details->location_id ?? '' }}">
<input type="hidden" id="original_invoice" value="{{ $purchase_details->Invoicenumber ?? '' }}">
<input type="hidden" id="original_invoice_no" value="{{ $purchase_details->invoice_no ?? '' }}">
<input type="hidden" id="original_batch" value="{{ $purchase_details->batch_number ?? '' }}">
<input type="hidden" id="original_batch_no" value="{{ $purchase_details->batch_no ?? '' }}">
            </div>

 <div class="form-group col-md-3">
        <label for="Invoicenumber">Farm Inward No <span class="required">*</span></label>
        <input type="text"  name="Invoicenumber"  value="{{ old('Invoicenumber', $invoiceValue) }}"  class="form-control"
               readonly>
        <input type="hidden"
               name="invoice_no"
               value="{{ old('invoice_no', $invoiceNumber) }}">
    </div>

    <!-- Batch Number -->
    <div class="form-group col-md-3">
        <label for="batch_number">Batch No</label>
        <input type="text"     name="batch_number"
               value="{{ old('batch_number', $batchValue) }}"
               class="form-control"
               readonly>
        <input type="hidden"
               name="batch_no"
               value="{{ old('batch_no', $batchNumber) }}">
    </div>

                  <div class="form-group col-md-3">
                                    <label> Inward Date <span class="required" style="color:red;">*</span></label>

                                    <input class="datepicker-here form-control" id="billdate" name="billdate" type="text"
                                        value="{{ isset($purchase_details->billdate) ? \Carbon\Carbon::parse($purchase_details->billdate)->format('d-m-Y') : (old('billdate') ?? date('d-m-Y')) }}"
                                        data-language="en"
                                        placeholder="Enter Date"
                                        data-date-format="dd-mm-yyyy" data-auto-close="true">

                                    @error('billdate')
                                        <span class="text-danger"><strong>{{ $message }}</strong></span>
                                    @enderror
                </div>



            <div class="form-group col-md-3">
                <label>Supplier <span class="required" style="color:red;">*</span></label>
                <select class="form-select select2" id="supplier" name="supplier" data-placeholder="Select " required>
                    <option value="">Select Supplier</option>
                    @foreach(DB::table('supplier')->get() as $supplier)
                        <option value="{{ $supplier->id }}"
                            @if(isset($purchase_details->supplier) && $purchase_details->supplier == $supplier->supplier_name)
                                selected
                            @endif
                        >
                            {{ $supplier->supplier_name }}
                        </option>
                    @endforeach
                </select>
            </div>




    </div>

    <br><br>

    <div id="proformatable">
        <div class="row">
            <div class="col-md-12">
                <table id="productTable"  class="table table-bordered">
                    <thead>
                        <tr>
                            <th>PRODUCTS<span class="required" style="color:red;">*</span></th>
                            <th>PRODUCT SIZE<span class="required" style="color:red;">*</span></th>
                            <th  width='15%'>STAGE</th>
                            <th  width='15%'>RATE</th>
                            <th  width='10%' >QUANTITY<span class="required" style="color:red;">*</span></th>
                            <th width='2%'>Action</th>
                        </tr>
                    </thead>
                      <tbody>
                        <?php $i = 0; ?>
                        <?php if (isset($purchase_product_list) && count($purchase_product_list) > 0) : ?>
                            <?php foreach ($purchase_product_list as $purchase_product_list1) : ?>
                                <?php $i++; ?>
                                <tr id="row_<?= $i ?>">
                             <td style="width:20px;">
                            <select style="width:10px;" class="select2" id="services_<?= $i ?>" name="services_<?= $i ?>"
                            data-placeholder="Products" style="height: 37px; width:100%;"
                            onchange="getprice(this.id);" required>
                            <option value="">Select Product</option>
                            @php
                            $pro_masters = DB::table('products')->get();
                            @endphp
                            @foreach ($pro_masters as $pro_master)
                            <option value="{{ $pro_master->id }}"
                            {{ $pro_master->id == $purchase_product_list1['services'] ? 'selected' : '' }}>
                            {{ $pro_master->product_name }}
                            </option>
                            @endforeach
                            </select>
                            </td>
                                    <td>
                    <select class="" id="size_<?= $i ?>" required name="size_<?= $i ?>" data-placeholder="Select Size" style="height: 37px; width:100%;" onchange="fetchrate(this.id);" required>
                            <option value="">Select Size</option>
                            @php
                $sizes = DB::table('product_details')
                    ->where('parentID', $purchase_product_list1['services'])->where('sizeoff', 0)->where('disable','!=',1)->where('status', 0)
                    ->get();
                      @endphp

                            @foreach ($sizes as $size)

                            <option value="{{ $size->id }}" {{ $size->id == $purchase_product_list1['size'] ? 'selected' : '' }}>
                            {{ $size->product_size }}
                            </option>
                            @endforeach
                            </select>
                                    </td>
                           <td>
                            <select class="select2" required id="stage_<?= $i ?>" name="stage_<?= $i ?>" data-placeholder="Select Stage" style="height: 37px; width:100%;" required >
                            <option value="">Select Stage</option>
                            <option value="Raw" @if($purchase_product_list1->stage == 'Raw') selected @endif>Raw</option>
                            <option value="Semi Ripe" @if($purchase_product_list1->stage == 'Semi Ripe') selected @endif>Semi Ripe</option>
                        </select>
                            </td>
                                    <td>
                                        <input type="text" required id="rate_<?= $i ?>" name="rate_<?= $i ?>" onkeyup="Gettotal();" value="<?= $purchase_product_list1->rate ?>"  class="form-control" required>
                                    </td>
                                    <td>
                                        <input type="text"  required  id="quantity_<?= $i ?>" name="quantity_<?= $i ?>" value="<?= $purchase_product_list1->Quantity ?>" onkeyup="Gettotal();"  class="form-control" required>
                                    </td>
                                    <td>
      <button id="button_<?= $i ?>" name="button_<?= $i ?>" type="button" class="btn btn-danger btn-sm"
                            onclick="deleteRow(this.id)"><i class="fa fa-trash"></i>
                            </button>                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <!-- If no rows exist, show a default row -->
                            <?php $i = 1; ?>
                            <tr id="row_<?= $i ?>">
                                <td>
                                           <select style="width:10px;" required class="select2" id="services_<?= $i ?>" name="services_<?= $i ?>" data-placeholder="Products" style="height: 37px; width:50%;" onchange="getprice(this.id);" required>
                                    <option value=""></option>
                                    @php
                                        $pro_masters = DB::table('products')->where('disable','!=',1)->get();
                                    @endphp
                                    @foreach ($pro_masters as $pro_master)
                                        <option value="{{ $pro_master->id }}">{{ $pro_master->product_name }}</option>
                                    @endforeach
                                </select>
                                </td>
                            <td>
                                <select class="required" id="size_<?= $i ?>"  required name="size_<?= $i ?>" onchange="fetchrate(this.id);" data-placeholder="Select Size" style="height: 37px; width:100%;"   required>
                                    <option value=""></option>
                                </select>
                            </td>
           <td>
                                <select  id="stage_<?= $i ?>" name="stage_<?= $i ?>" required data-placeholder="Select Stage" style="height: 37px; width:100%;" required>
                            <option value="">Select Stage</option>
                            <option value="Raw">Raw</option>
                            <option value="Semi Ripe">Semi Ripe</option>
                                </select>
                            </td>
                                <td>
                                    <input type="text" id="rate_<?= $i ?>" required name="rate_<?= $i ?>"onkeyup="Gettotal();" class="form-control" required>
                                </td>
                                <td>
                                    <input type="text" id="quantity_<?= $i ?>" name="quantity_<?= $i ?>"  onkeyup="Gettotal();" class="form-control" required>
                                </td>
                                <td>
    <button id="button_<?= $i ?>" name="button_<?= $i ?>" type="button" class="btn btn-danger btn-sm"
                            onclick="deleteRow(this.id)"><i class="fa fa-trash"></i>
                            </button>                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-center">
                                <input type="hidden" id="cnt" name="cnt" value="<?= $i ?>">
                                <button type="button" class="btn btn-primary" onclick="addRow()">Add More</button>
                            </td>
                            <td>Total</td>
                            <td colspan="2">
                        <input type="text" readonly id="totalproamt_" name="totalproamt"
                               value="<?php echo isset($purchase_details->Tquantity) ? $purchase_details->Tquantity : ''; ?>"
                               class="form-control">
                    </td>

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
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button> <a href="{{ route('admin.farm_inward.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
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

    // Trigger getprice for existing rows in edit mode
//     let rowCount = $("#cnt").val();

// for (let i = 1; i <= rowCount; i++) {

//     let product = $("#services_" + i).val();
//     let rate = $("#rate_" + i).val();
//     let qty = $("#quantity_" + i).val();

//     // Only trigger getprice if rate & qty are empty (new row)
//     if (product && (!rate || !qty)) {
//         getprice("services_" + i);
//     }
// }

});


                // Function to add a new row
                function addRow() {
                    const tableBody = document.getElementById('productTable').querySelector('tbody');
                    const rowCount = tableBody.rows.length + 1; // Calculate row count dynamically
                    const newRow = `
                        <tr id="row_${rowCount}">
                                <td style="width:20px;">
                            <select style="width:10px;" class="select2" required id="services_${rowCount}" name="services_${rowCount}"
                                    data-placeholder="Select Products" style="height: 37px; width:100%;"
                                    onchange="getprice(this.id);" required>
                                <option value=""></option>
                         @php
                                        $pro_masters = DB::table('products')->where('disable','!=',1)->get();
                                @endphp
                                @foreach ($pro_masters as $pro_master)
                                    <option value="{{ $pro_master->id }}">{{ $pro_master->product_name }}</option>
                                @endforeach
                            </select>
                        </td>
                                <td>
                       <select  id="size_${rowCount}" required name="size_${rowCount}"
                            data-placeholder="Select Size" style="height: 37px; width:100%;" onchange="fetchrate(this.id);" required>
                                <option value=""></option>
                            </select>
                        </td>
                 <td>
                    <select  id="stage_${rowCount}" required name="stage_${rowCount}"
                            data-placeholder="Select Stage" style="height: 37px; width:100%;" required>
                            <option value="">Select Stage</option>
                               <option value="Raw">Raw</option>
                        <option value="Semi Ripe">Semi Ripe</option>
                    </select>
                </td>
                            <td>
                                <input type="text" required id="rate_${rowCount}" name="rate_${rowCount}" onkeyup="Gettotal();"  class="form-control" required>
                            </td>
                            <td>
                                <input type="text" required id="quantity_${rowCount}" name="quantity_${rowCount}" onkeyup="Gettotal();" class="form-control" required>
                            </td>
                                 <td>
                <button id="button_${rowCount}"  name="button_${rowCount}" type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this.id)">
            <i class="fa fa-trash"></i></button>
</td>
                        </tr>
                    `;

                    tableBody.insertAdjacentHTML('beforeend', newRow);

                    // Update the hidden input field with the latest row count
                    document.getElementById('cnt').value = rowCount;

                    $(".select2").select2();
                }

                // Function to delete a row
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

                jQuery("#size_"+k).attr('name','size_'+newId);
                jQuery("#size_"+k).attr('id','size_'+newId);


                jQuery("#stage_"+k).attr('name','stage_'+newId);
                jQuery("#stage_"+k).attr('id','stage_'+newId);


                jQuery("#rate_"+k).attr('name','rate_'+newId);
                jQuery("#rate_"+k).attr('id','rate_'+newId);

                jQuery("#qty_"+k).attr('name','qty_'+newId);
                jQuery("#qty_"+k).attr('id','qty_'+newId);


                jQuery("#quantity_"+k).attr('name','quantity_'+newId);
                jQuery("#quantity_"+k).attr('id','quantity_'+newId);





                jQuery("#amount_"+k).attr('name','amount_'+newId);
                jQuery("#amount_"+k).attr('id','amount_'+newId);

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
        let rate = parseFloat(document.getElementById("rate_" + rowIndex).value) || 0; // Default to 0 if not a number
        let quantity = parseFloat(row.value) || 0; // Default to 0 if not a number

        // Calculate the total amount for the row
        let rowAmount = rate * quantity;

        // Add the row amount to the total
        totalAmount += rowAmount;
    });

    // Update the total amount in the 'totalproamt' field
    document.getElementById('totalproamt_').value = totalAmount.toFixed(2); // Display total with 2 decimal places
}




function fetchrate(id){

    let rowIndex = id.split("_")[1];

    let services = $("#services_"+rowIndex).val();
    let size = $("#size_"+rowIndex).val();

    if(!services || !size){
        return;
    }

    $.ajax({

        url:"{{ route('admin.farm-inward.get-rate') }}",
        type:"GET",

        data:{
            services:services,
            size:size,
            _token:"{{ csrf_token() }}"
        },

        success:function(response){

            if(response.status=="success"){

                $("#rate_"+rowIndex).val(response.rate);

                Gettotal();

            }

        }

    });

}

function getprice(id){

    let rowIndex = id.split("_")[1];

    let services = $("#services_"+rowIndex).val();

    if(!services){
        return;
    }

    $.ajax({

        url: "{{ route('admin.farm-inward.get-details') }}",
        type: "GET",

        data:{
            services:services,
            rowIndex:rowIndex,
            _token:"{{ csrf_token() }}"
        },

        success:function(response){

            if(response.status=="nosize"){

                alert("No size available for this product");

                $("#services_"+rowIndex).val('').trigger('change');

                $("#size_"+rowIndex).html('<option value="">Select Size</option>');

                $("#rate_"+rowIndex).val('');

                return;
            }

            if(response.status=="success"){

                let options = '<option value="">Select Size</option>';

                response.data.forEach(function(item){

                    options += `<option value="${item.id}">${item.product_size}</option>`;

                });

                $("#size_"+rowIndex).html(options);

            }

        },

        error:function(){

            alert("Error fetching product sizes");

        }

    });

}


</script>
<script>
function getInvoiceBatch(location_id){
    let originalLocation = $("#original_location_id").val();

    if(location_id == originalLocation){
        // Restore original values
        $("input[name='Invoicenumber']").val($("#original_invoice").val());
        $("input[name='invoice_no']").val($("#original_invoice_no").val());
        $("input[name='batch_number']").val($("#original_batch").val());
        $("input[name='batch_no']").val($("#original_batch_no").val());
        return;
    }

    // Fetch new invoice/batch for different location
    $.ajax({
        url:"{{ route('admin.farm-inward.get-invoice-batch') }}",
        type:"GET",
        data:{
            location_id:location_id,
            _token:"{{ csrf_token() }}"
        },
        success:function(response){
            $("input[name='Invoicenumber']").val(response.invoice.formatted);
            $("input[name='invoice_no']").val(response.invoice.number);
            $("input[name='batch_number']").val(response.batch.formatted);
            $("input[name='batch_no']").val(response.batch.number);
        }
    });
}
$(document).ready(function(){
    let location = $("#location_id").val();
    if(location){
        getInvoiceBatch(location);
    }
});
</script>
