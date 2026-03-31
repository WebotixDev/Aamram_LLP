<div class="form theme-form">
@php
// -----------------------
// Determine invoice display
// -----------------------
$invoiceValue = '';
$invoiceNumber = '';
if (isset($invoice)) {
    if (is_object($invoice)) { // edit mode
        $invoiceValue = $invoice->Invoicenumber ?? '';
        $invoiceNumber = $invoice->invoice_no ?? '';
    } elseif (is_array($invoice)) {
        $invoiceValue = $invoice['formatted'] ?? '';
        $invoiceNumber = $invoice['number'] ?? '';
    }
}


@endphp

          <div class="row">

                            <div class="form-group col-md-3">
                <label>From Location <span class="required" style="color:red;">*</span></label>
            <select class="form-select select2"
                    id="from_location_id"
                    name="from_location_id"
                    data-placeholder="Select From Location"
                    required>

                    <option value="">Select Location</option>

                    @foreach(DB::table('location')->get() as $location)
                        <option value="{{ $location->id }}"
                            @if(isset($Farm_Delivery_challan->from_location_id) && $Farm_Delivery_challan->from_location_id == $location->id)
                                selected
                            @endif>

                            {{ $location->location }}

                        </option>
                    @endforeach

                </select>
    <input type="hidden" name="original_location_id"  id="original_location_id" value="{{ $Farm_Delivery_challan->from_location_id ?? '' }}">
    <input type="hidden" name="original_invoice" id="original_invoice" value="{{ $Farm_Delivery_challan->Invoicenumber ?? '' }}">
    <input type="hidden" name="original_invoice_no" id="original_invoice_no" value="{{ $Farm_Delivery_challan->invoice_no ?? '' }}">
            </div>

 <div class="form-group col-md-3">
        <label for="Invoicenumber">Delivery Challan No <span class="required">*</span></label>
        <input type="text"
               name="Invoicenumber"
               value="{{ old('Invoicenumber', $invoiceValue) }}"
               class="form-control"
               readonly>
        <input type="hidden"
               name="invoice_no"
               value="{{ old('invoice_no', $invoiceNumber) }}">
    </div>



                  <div class="form-group col-md-3">
                  <label>Challan Date <span class="required" style="color:red;">*</span></label>

                  <input class="datepicker-here form-control" id="billdate" name="billdate" type="text"
                                        value="{{ isset($Farm_Delivery_challan->challan_date) ? \Carbon\Carbon::parse($Farm_Delivery_challan->challan_date)->format('d-m-Y') : (old('challan_date') ?? date('d-m-Y')) }}"
                                        data-language="en"
                                        placeholder="Enter Date"
                                        data-date-format="dd-mm-yyyy" data-auto-close="true">
                                    @error('billdate')
                                        <span class="text-danger"><strong>{{ $message }}</strong></span>
                                    @enderror
                </div>





            <div class="form-group col-md-3">
                <label>To Location <span class="required" style="color:red;">*</span></label>
                <select class="form-select select2" id="to_location_id" name="to_location_id" data-placeholder="Select To Location" required>
                    <option value="">Select Location</option>
                    @foreach(DB::table('location')->get() as $location)
                        <option value="{{ $location->id }}"
                            @if(isset($Farm_Delivery_challan->to_location_id) && $Farm_Delivery_challan->to_location_id == $location->id)
                                selected
                            @endif
                        >
                            {{ $location->location }}
                        </option>
                    @endforeach
                </select>
            </div>
    </div>
<hr>
    <h5>Transport Details</h5>
<div class="row pt-2">

               <div class="form-group col-md-3">
                <label>Tranasporter <span class="required" style="color:red;">*</span></label>
                <select class="form-select select2" id="transporter_id" name="transporter_id" data-placeholder="Select " required>
                    <option value="">Select Tranasporter</option>
                    @foreach(DB::table('transporter')->get() as $transporter)
                        <option value="{{ $transporter->id }}"
                            @if(isset($Farm_Delivery_challan->transporter_id) && $Farm_Delivery_challan->transporter_id == $transporter->id)
                                selected
                            @endif>
                            {{ $transporter->transporter }}
                        </option>
                    @endforeach
                </select>
            </div>

               <div class="form-group col-md-4">
                    <label>Driver Name <span class="required" style="color:red;">*</span></label>
                    <input type="text" id="driver_name" name="driver_name" class="form-control" placeholder="Enter Name" required value="{{  isset($Farm_Delivery_challan->driver_name) ? $Farm_Delivery_challan->driver_name : old('driver_name') }}" required>
                            @error('driver_name')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label>Driver Mobile No <span class="required" style="color:red;">*</span></label>
                        <input type="number" id="driver_mobile_no" name="driver_mobile_no" class="form-control" placeholder="Enter Mobile No" required value="{{  isset($Farm_Delivery_challan->driver_mobile_no) ? $Farm_Delivery_challan->driver_mobile_no : old('mobile_no') }}" >
                                @error('driver_mobile_no')
                        <span class="text-danger">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                        </div>

</div>
    <br><br>

    <div id="proformatable">
        <div class="row">
            <div class="col-md-12">
                <table id="productTable"  class="table table-bordered">
                    <thead>
                        <tr>
                            <th width='20%'>PRODUCTS<span class="required" style="color:red;">*</span></th>
                            <th width='20%'>PRODUCT SIZE<span class="required" style="color:red;">*</span></th>
                            <th  width='10%'>STAGE</th>
                            <th  width='12%'>BATCH NO</th>
                            <th  width='10%'>STOCK</th>
                            <th  width='10%' >QUANTITY<span class="required" style="color:red;">*</span></th>
                            <th  width='10%' >TRANSPORT COST (PER QTY)<span class="required" style="color:red;">*</span></th>
                            <th width='2%'>Action</th>
                        </tr>
                    </thead>
                      <tbody>
                        <?php $i = 0; ?>
                        <?php if (isset($Farm_Delivery_challan_details) && count($Farm_Delivery_challan_details) > 0) : ?>
                            <?php foreach ($Farm_Delivery_challan_details as $Farm_Delivery_challan_details1) : ?>
                                <?php $i++; ?>
                                <tr id="row_<?= $i ?>"> <input type="hidden" id="original_service_<?= $i ?>" value="<?= $Farm_Delivery_challan_details1['services'] ?>">
                        <input type="hidden" id="original_size_<?= $i ?>" value="<?= $Farm_Delivery_challan_details1['size'] ?>">
                        <input type="hidden" id="original_stage_<?= $i ?>" value="<?= $Farm_Delivery_challan_details1['stage'] ?>">
                        <input type="hidden" id="original_batch_<?= $i ?>" value="<?= $Farm_Delivery_challan_details1['batch_number'] ?>">
                        <input type="hidden" id="original_qty_<?= $i ?>" value="<?= $Farm_Delivery_challan_details1['Quantity'] ?>">
                             <td style="width:20px;">
                            <select style="width:10px;" class="select2" id="services_<?= $i ?>" name="services_<?= $i ?>"
                            data-placeholder="Products" style="height: 37px; width:100%;"
                            onchange="getprice(this.id);  " required>
                            <option value="">Select Product</option>
                            @php
                            $pro_masters = DB::table('products')->get();
                            @endphp
                            @foreach ($pro_masters as $pro_master)
                            <option value="{{ $pro_master->id }}"
                            {{ $pro_master->id == $Farm_Delivery_challan_details1['services'] ? 'selected' : '' }}>
                            {{ $pro_master->product_name }}
                            </option>
                            @endforeach
                            </select>
                            </td>
                                    <td>
                    <select class="" id="size_<?= $i ?>" required name="size_<?= $i ?>" data-placeholder="Select Size" style="height: 37px; width:100%;" onchange="fetchrate(this.id); checkDuplicate(this.id.split('_')[1]);" required>
                            <option value="">Select Size</option>
                            @php
                $sizes = DB::table('product_details')
                    ->where('parentID', $Farm_Delivery_challan_details1['services'])->where('sizeoff', 0)->where('disable','!=',1)->where('status', 0)
                    ->get();
                      @endphp

                            @foreach ($sizes as $size)

                            <option value="{{ $size->id }}" {{ $size->id == $Farm_Delivery_challan_details1['size'] ? 'selected' : '' }}>
                            {{ $size->product_size }}
                            </option>
                            @endforeach
                            </select>
                            </td>
                           <td>
                            <select class="select2" required id="stage_<?= $i ?>"onchange="checkDuplicate(this.id.split('_')[1]);" name="stage_<?= $i ?>" data-placeholder="Select Stage" style="height: 37px; width:100%;" required >
                            <option value="">Select Stage</option>
                            <option value="Raw" @if($Farm_Delivery_challan_details1->stage == 'Raw') selected @endif>Raw</option>
                            <option value="Semi Ripe" @if($Farm_Delivery_challan_details1->stage == 'Semi Ripe') selected @endif>Semi Ripe</option>
                           </select>
                            </td>

                            <td>
                    <select class="" id="batch_number_<?= $i ?>" required name="batch_number_<?= $i ?>" data-placeholder="Select Batch" style="height: 37px; width:100%;"  onchange=" " required>
                            <option value="">Select Batch</option>
                            @php
                            $batch_no = DB::table('farm_inward')
                            ->where('location_id', $Farm_Delivery_challan['from_location_id'])
                            ->get();
                            @endphp

                            @foreach ($batch_no as $batch)
                            <option value="{{ $batch->batch_number }}" {{ $batch->batch_number == $Farm_Delivery_challan_details1['batch_number'] ? 'selected' : '' }}>
                            {{ $batch->batch_number }}
                            </option>
                            @endforeach
                            </select>
                            </td>
                                    <td>
                                        <input type="text" required id="stock_<?= $i ?>" name="stock_<?= $i ?>"  value="<?= $Farm_Delivery_challan_details1->rate ?>"  readonly class="form-control" required>
                                    </td>
                                    <td>
                                        <input type="text"  required  id="quantity_<?= $i ?>" name="quantity_<?= $i ?>" value="<?= $Farm_Delivery_challan_details1->Quantity ?>" onkeyup="checkQty(<?= $i ?>);Gettotal();"
                class="form-control" required>
                                                    </td>
<td>
                         <input type="text"  required  id="transcost_<?= $i ?>" name="transcost_<?= $i ?>" onkeyup="Gettotal();" value="<?= $Farm_Delivery_challan_details1->transcost ?>"
                class="form-control" required>
                            </td>
                                    <td>
      <button id="button_<?= $i ?>" name="button_<?= $i ?>" type="button" class="btn btn-danger btn-sm"
                            onclick="deleteRow(this.id)"><i class="fa fa-trash"></i>
                            </button>
                          </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <!-- If no rows exist, show a default row -->
                            <?php $i = 1; ?>
                            <tr id="row_<?= $i ?>">
                                <td>
                                           <select style="width:10px;" required class="select2" id="services_<?= $i ?>" name="services_<?= $i ?>" data-placeholder="Products" style="height: 37px; width:50%;" onchange="getprice(this.id);checkDuplicate(this.id.split('_')[1]);" required>
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
                                <select class="required" id="size_<?= $i ?>"  required name="size_<?= $i ?>" onchange="fetchrate(this.id); checkDuplicate(this.id.split('_')[1]);" data-placeholder="Select Size" style="height: 37px; width:100%;"   required>
                                    <option value=""></option>
                                </select>
                            </td>
<td>
                                <select  id="stage_<?= $i ?>" name="stage_<?= $i ?>" required data-placeholder="Select Stage" style="height: 37px; width:100%;" onchange="checkDuplicate(this.id.split('_')[1]);" required>
                            <option value="">Select Stage</option>
                            <option value="Raw">Raw</option>
                            <option value="Semi Ripe">Semi Ripe</option>
                                </select>
                            </td>
                                                  <td>
                                <select class="required" id="batch_number_<?= $i ?>"  required name="batch_number_<?= $i ?>" onchange="checkDuplicate(this.id.split('_')[1]);"  data-placeholder="Select Batch" style="height: 37px; width:100%;"  required>
                                    <option value=""></option>
                                </select>
                            </td>

                                <td>
                                    <input type="text" id="stock_<?= $i ?>" readonly required name="stock_<?= $i ?>"class="form-control" required>
                                </td>
                                <td>
                                    <input type="text" id="quantity_<?= $i ?>" name="quantity_<?= $i ?>"  onkeyup="checkQty(<?= $i ?>);Gettotal();"
            class="form-control" required>
                                </td>
                                <td>
                                    <input type="text" id="transcost_<?= $i ?>" onkeyup="Gettotal();" name="transcost_<?= $i ?>" value="100"
            class="form-control" required>
                                </td>
                                <td>
                   <button id="button_<?= $i ?>" name="button_<?= $i ?>" type="button" class="btn btn-danger btn-sm"
                            onclick="deleteRow(this.id)"><i class="fa fa-trash"></i>
                            </button>
                         </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-center">
                                <input type="hidden" id="cnt" name="cnt" value="<?= $i ?>">
                                <button type="button" class="btn btn-primary" onclick="addRow()">Add Row</button>
                            </td>
                                    <td colspan="2">Total</td>
                            <td colspan="2">
                        <input type="text" readonly id="totalproamt_" name="totalamt"
                               value="<?php echo isset($Farm_Delivery_challan->totalamt) ? $Farm_Delivery_challan->totalamt : ''; ?>"
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
                                    onchange="getprice(this.id);   checkDuplicate(${rowCount});" required>
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
                    <select  id="stage_${rowCount}" required name="stage_${rowCount}" onchange="checkDuplicate(${rowCount});"
                            data-placeholder="Select Stage" style="height: 37px; width:100%;" required>
                            <option value="">Select Stage</option>
                               <option value="Raw">Raw</option>
                        <option value="Semi Ripe">Semi Ripe</option>
                    </select>

                    </td>
                   <td>
                       <select  id="batch_number_${rowCount}" required name="batch_number_${rowCount}" onchange="checkDuplicate(${rowCount});"
                            data-placeholder="Select Batch" style="height: 37px; width:100%;" required>
                                <option value=""></option>
                            </select>
                        </td>
                 <td>

                                <input type="text" required id="stock_${rowCount}"  readonly name="stock_${rowCount}"   class="form-control" required>
                            </td>
                            <td>
                                <input type="text" required id="quantity_${rowCount}" onkeyup="checkQty(${rowCount});Gettotal();"  name="quantity_${rowCount}"  class="form-control" required>
                            </td>
                      <td>
                                <input type="text" required id="transcost_${rowCount}"  name="transcost_${rowCount}" onkeyup="Gettotal();" value="100" class="form-control" required>
                            </td>
                      <td>
                 <button id="button_${rowCount}"  name="button_${rowCount}" type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this.id)">
                <i class="fa fa-trash"></i></button>
                </td>

                </tr>
                            `;

                        tableBody.insertAdjacentHTML('beforeend', newRow);

                    // Update row count
                    document.getElementById('cnt').value = rowCount;

                    $(".select2").select2();

                    // FETCH BATCH FOR NEW ROW
                    getBatchNumbers(rowCount);
                }


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

                jQuery("#batch_number_"+k).attr('name','batch_number_'+newId);
                jQuery("#batch_number_"+k).attr('id','batch_number_'+newId);

                jQuery("#stock_"+k).attr('name','stock_'+newId);
                jQuery("#stock_"+k).attr('id','stock_'+newId);

                jQuery("#quantity_"+k).attr('name','quantity_'+newId);
                jQuery("#quantity_"+k).attr('id','quantity_'+newId);

                jQuery("#transcost_"+k).attr('name','transcost_'+newId);
                jQuery("#transcost_"+k).attr('id','transcost_'+newId);

                jQuery("#button_"+k).attr('name','button_'+newId);
                jQuery("#button_"+k).attr('id','button_'+newId);

            }

            jQuery("#cnt").val(parseFloat(count-1));

        }
    } else {
                alert("Can't remove row Atleast one row is required");
                return false;
           }
               Gettotal();

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

$(document).ready(function(){

    $(".select2").select2();

    // LOCATION CHANGE EVENT
    $("#from_location_id").on("change", function(){

        let location_id = $(this).val();

        getInvoiceBatch(location_id);

        let rowCount = $("#cnt").val();

        for(let i=1;i<=rowCount;i++){

            $("#batch_number_"+i).html('<option value="">Loading...</option>');

            getBatchNumbers(i);

        }

    });


    // LOAD BATCH ON EDIT PAGE
    let location = $("#from_location_id").val();

    if(location){

        let rowCount = $("#cnt").val();

        for(let i=1;i<=rowCount;i++){

            getBatchNumbers(i);

        }

    }

});


function getBatchNumbers(rowIndex){

    let location_id = $("#from_location_id").val();
    let selectedBatch = $("#batch_number_"+rowIndex).val();

    if(!location_id){
        return;
    }

    $.ajax({

        url:"{{ route('admin.get-batch-by-location') }}",
        type:"GET",

        data:{
            location_id:location_id
        },

        success:function(response){

            if(response.status == "success"){

                let options = '<option value="">Select Batch</option>';

                response.data.forEach(function(batch){

                    let selected = (batch.batch_number == selectedBatch) ? "selected" : "";

                    options += `<option value="${batch.batch_number}" ${selected}>${batch.batch_number}</option>`;

                });

            $("#batch_number_"+rowIndex).html(options).trigger('change');
            }

        }

    });

}



// ----------------------------
// LOCATION CHANGE
// ----------------------------
// $("#from_location_id").change(function(){

//     let location_id = $(this).val();

//     getInvoiceBatch(location_id);

//     let rowCount = $("#cnt").val();

//     for(let i=1;i<=rowCount;i++){

//         $("#batch_number_"+i).html('<option value="">Loading...</option>');

//         getBatchNumbers(i);

//     }

// });



// ----------------------------
// GET INVOICE
// ----------------------------
function getInvoiceBatch(location_id){
    let originalLocation = $("#original_location_id").val();

    if(location_id == originalLocation){
        // Restore original values
        $("input[name='Invoicenumber']").val($("#original_invoice").val());
        $("input[name='invoice_no']").val($("#original_invoice_no").val());

        return;
    }

    // Fetch new invoice/batch for different location
    $.ajax({
        url:"{{ route('admin.Farm_Delivery_challan.get-invoice') }}",

        type:"GET",
        data:{
            location_id:location_id,
            _token:"{{ csrf_token() }}"
        },
        success:function(response){
            $("input[name='Invoicenumber']").val(response.invoice.formatted);
            $("input[name='invoice_no']").val(response.invoice.number);

        }
    });
}

function fetchStock(rowIndex){

    let service = $("#services_"+rowIndex).val();
    let size = $("#size_"+rowIndex).val();
    let stage = $("#stage_"+rowIndex).val();
    let batch_number = $("#batch_number_"+rowIndex).val();

    if(!service || !size || !stage || !batch_number){
        $("#stock_"+rowIndex).val(0);
        return;
    }

    $.ajax({

        url:"{{ route('admin.FARMDC.get-stock') }}",
        type:"GET",

        data:{
            service:service,
            size:size,
            stage:stage,
            batch_number:batch_number
        },

        success:function(response){

            let stock = 0;

            if(response.status == "success"){
                stock = parseFloat(response.stock);
            }

            // ORIGINAL VALUES
            let o_service = $("#original_service_"+rowIndex).val();
            let o_size = $("#original_size_"+rowIndex).val();
            let o_stage = $("#original_stage_"+rowIndex).val();
            let o_batch = $("#original_batch_"+rowIndex).val();
            let o_qty = parseFloat($("#original_qty_"+rowIndex).val()) || 0;

            // IF SAME AS ORIGINAL SELECTION
            if(service == o_service && size == o_size && stage == o_stage && batch_number == o_batch){
                stock = stock + o_qty;
            }

    $("#stock_"+rowIndex).val(stock);

    // 🔥 IMPORTANT
    checkQty(rowIndex);
        }

    });
        Gettotal();


}
$(document).ready(function() {

    $(".select2").select2();

    let rowCount = $("#cnt").val();

    for(let i=1;i<=rowCount;i++){
        fetchStock(i);
    }

});


function checkQty(rowIndex){

    let stock = parseFloat($("#stock_"+rowIndex).val()) || 0;
    let qty   = parseFloat($("#quantity_"+rowIndex).val()) || 0;

    let originalQty = parseFloat($("#original_qty_"+rowIndex).val()) || 0;

    // actual available stock without original edit qty
    let availableStock = stock;

    if(originalQty){
        availableStock = stock;
    }

    if(qty > availableStock){

        alert("Quantity exceeds available stock!");

        $("#quantity_"+rowIndex).val(originalQty); // reset to original
        $("#quantity_"+rowIndex).focus();

        return false;
    }
}

$(document).on('change', '[id^=services_], [id^=size_], [id^=stage_], [id^=batch_number_]', function () {

    let id = $(this).attr('id');
    let rowIndex = id.split('_').pop();

    fetchStock(rowIndex);

});
function checkDuplicate(rowIndex){

    let service = $("#services_"+rowIndex).val();
    let size = $("#size_"+rowIndex).val();
    let stage = $("#stage_"+rowIndex).val();
    let batch = $("#batch_number_"+rowIndex).val();

    if(!service || !size || !stage || !batch){
        return;
    }

    let rowCount = $("#cnt").val();

    for(let i=1;i<=rowCount;i++){

        if(i == rowIndex) continue;

        let s = $("#services_"+i).val();
        let sz = $("#size_"+i).val();
        let st = $("#stage_"+i).val();
        let b = $("#batch_number_"+i).val();

        if(service == s && size == sz && stage == st && batch == b){

            alert("Duplicate entry! Same Product + Size + Stage + Batch already added.");

            $("#batch_number_"+rowIndex).val('').trigger('change');

            return;

        }

    }

}


function Gettotal() {
    // Initialize total amount to 0
    let totalAmount = 0;

    let rows = document.querySelectorAll('[id^="quantity_"]'); // Assuming quantity fields are identified with 'quantity'

    rows.forEach(function(row) {
        let rowIndex = row.id.replace("quantity_", ""); // Extract rowIndex from quantity input id

        // Get the rate value for the corresponding row (rate field should have ids like 'rate1', 'rate2', etc.)
        let rate = parseFloat(document.getElementById("transcost_" + rowIndex).value) || 0; // Default to 0 if not a number
        let quantity = parseFloat(row.value) || 0; // Default to 0 if not a number

        // Calculate the total amount for the row
        let rowAmount = rate * quantity;

        // Add the row amount to the total
        totalAmount += rowAmount;
    });

    // Update the total amount in the 'totalproamt' field
    document.getElementById('totalproamt_').value = totalAmount.toFixed(2); // Display total with 2 decimal places
}
</script>
