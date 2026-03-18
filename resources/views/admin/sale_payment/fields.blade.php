<div class="form theme-form">
@php
// Check if editing an existing record
if (isset($invoice) && $invoice->ReceiptNo) {
    // Use the existing ReceiptNo for edit form
    $nextBillNo = $invoice->ReceiptNo;
} else {
    // Generate the next ReceiptNo for add form
    $lastBillNo = DB::table('purchase_payments')->max('ReceiptNo');
    $nextBillNo = $lastBillNo ? $lastBillNo + 1 : 1;
}
@endphp
      <div class="row ">
                   
            <div class="form-group col-md-4">
            <label for="ReceiptNo">Sale Payment No <span class="required">*</span></label>
            <input type="text" id="ReceiptNo" 
                name="ReceiptNo" 
                value="{{ old('ReceiptNo', $nextBillNo) }}" 
                class="required form-control" readonly>
                </div>


                                <div class="form-group col-md-4">
                                    <label>Sale Date <span class="required" style="color:red;">*</span></label>

                                    <input class="datepicker-here form-control" id="PurchaseDate" name="PurchaseDate" type="text" 
                                        value="{{ isset($sale_details->PurchaseDate) ? \Carbon\Carbon::parse($sale_details->PurchaseDate)->format('d-m-Y') : (old('PurchaseDate') ?? date('d-m-Y')) }}" 
                                        data-language="en" 
                                        placeholder="Enter Date" 
                                        data-date-format="dd-mm-yyyy" data-auto-close="true">

                                    @error('PurchaseDate')
                                        <span class="text-danger"><strong>{{ $message }}</strong></span>
                                    @enderror
                </div>

                                <div class="form-group col-md-4">
                                    <label>
                                        Customer Name
                                        <span class="required" style="color:red;" onclick="hideshow()">*</span>
                                    </label>
                                    <select class="form-select select2 required" id="customer_name" name="customer_name" data-placeholder="Select Customer" onchange="hideshow()">
                                        <option value="">Select Customer</option>
                                        @php
                                            $customers = DB::table('customers')->get();
                                        @endphp
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ isset($sale_details->customer_name) && $sale_details->customer_name == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->customer_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                    <!-- <div class="form-group col-md-4">
                                        <label>
                                            Customer Name 
                                            <span class="required" style="color:red;">*</span>
                                        </label>
                                        <select class="form-select select2" id="customer_name" name="customer_name" data-placeholder="Select Types">
                                            <option value="">Select Customer</option>
                                            @php
                                                $customers = DB::table('customers')->get();
                                            @endphp
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                            @endforeach
                                        </select>
                                    </div> -->



    </div>

    <br><br>



    <div class="row">
    <div class="col-8" id="tableContainer">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width: 2%;"></th>
                    <th style="width: 10%;">Invoice No.</th>
                    <th style="width: 8%;">Total Payable</th>
                    <th style="width: 10%;">Amount</th>
                </tr>
            </thead>
            <?php $i = 0; ?>
            <?php if (isset($sale_payment) && count($sale_payment) > 0): ?>
                <?php foreach ($sale_payment as $sale_payments): ?>
                    <?php $i++; ?>
                    <tr id="row_<?= $i ?>">
                        <td>
                            <input type="checkbox" class="checkboxes" name="check_list" 
                                onchange="gettotaltable();getinputbox(<?= $i ?>);getinputvalues(<?= $i ?>);">
                        </td>
                        <input type="hidden" id="purchaseid_<?= $i ?>" name="purchaseid_<?= $i ?>" value="<?= $sale_payments->purchaseid ?>">
                        <td><input type="text" id="Invoicenumber_<?= $i ?>" name="Invoicenumber_<?= $i ?>" value="<?= $sale_payments->Invoicenumber ?>" class="form-control" readonly></td>
                        <td>
                            <input type="text" class="form-control" name="payamt_<?= $i ?>" id="payamt_<?= $i ?>" value="<?= $sale_payments->payamt ?>" readonly />
                        </td>
                        <td>
                            <input type="text" class="form-control" name="bank_<?= $i ?>" id="bank_<?= $i ?>" value="<?= $sale_payments->amount ?>" onkeyup="gettotaltable();" onblur="validateAmounts();" />
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tbody id="recordsTableBody">
                    <!-- Data will be loaded here via AJAX -->
                </tbody>
            <?php endif; ?>
            <tfoot>
                <tr>
                    <input type="hidden" id="cnt" name="cnt" value="<?= $i ?>">
                    <td></td>
                    <td></td>
                    <td style="text-align: center;">Total</td>
                    <td>
                        <input type="text" id="totalvalue" name="totalvalue" value="<?= htmlspecialchars($sale_details['totalvalue'] ?? '', ENT_QUOTES, 'UTF-8') ?>" style="width: 100%;" readonly>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="col-4"></div>
</div>

                        <br><br><br>

    <div class="row">
			   <div style="width:100%;">
				<table id="pmttble" class="table table-bordered table-hover"  style="width:100%; margin-right: 60px;background-color:white;">
            <tbody>
      	    <tr>
			<th style="width: 18%;">Date</th>
			
			<th style="width: 18%;">Payment Method</th>			
            <th style="width: 15%;">Cheque No / Trasaction ID</th>			
            <!--<th style="width: 13%;">Cheque Amount</th>			-->
            <th style="width: 13%;"> Amount</th>
			<th style="width: 17%;">Note</th>			   
            </tr>
           <tr>
			 <td>
				<div class="col-md-12 col-sm-4 col-xs-12">                         
							
				  <div class="input-group date"> 

                                <input class="datepicker-here form-control" id="date" name="date" type="text" 
                                        value="{{ isset($sale_details->date) ? \Carbon\Carbon::parse($sale_details->date)->format('d-m-Y') : (old('date') ?? date('d-m-Y')) }}" 
                                        data-language="en" 
                                        placeholder="Enter Date" 
                                        data-date-format="dd-mm-yyyy" data-auto-close="true">
                                @error('date')
                                <span class="text-danger"><strong>{{ $message }}</strong></span>
                                @enderror
                                </div>
					
				
				</div>
									 	
			   </td>
			
                    
                    	<td>
                    	  
                                        <div class="col-md-12 col-sm-4 col-xs-12">
                                        <select class="form-select required" tabindex="-1" data-placeholder="Select Payment Method"  name="mode" id="mode" >
                                    <option value="cash" @if(isset($sale_details->mode) && $sale_details->mode == 'cash') selected @endif>Cash</option>
                                    <option value="cheque" @if(isset($sale_details->mode) && $sale_details->mode == 'cheque') selected @endif>Cheque</option>
                                <option value="DD" @if(isset($sale_details->mode) && $sale_details->mode == 'DD') selected @endif>DD</option>

                                    <option value="RTGS" @if(isset($sale_details->mode) && $sale_details->mode == 'RTGS') selected @endif>RTGS</option>
                                    <option value="UPI" @if(isset($sale_details->mode) && $sale_details->mode == 'UPI') selected @endif>E-Payment</option>
                                </select>

                                        </div>

														</td>

                    <td>
                <div class="col-md-12 col-sm-4 col-xs-12">
             <input type="text" 
                      name="cheque_no" 
                                  
                                value="<?= isset($sale_details->cheque_no) ? htmlspecialchars($sale_details->cheque_no, ENT_QUOTES, 'UTF-8') : ''; ?>" 
                                class="form-control col-md-7 col-xs-12" 
                                id="cheque_no">
                        </div>
                    </td>

                    <!--<td>-->
                    <!--    <input type="text" -->
                    <!--        name="cheque_amt" -->
                    <!--        readonly -->
                    <!--        value="<?= isset($sale_details->cheque_amt) ? htmlspecialchars($sale_details->cheque_amt, ENT_QUOTES, 'UTF-8') : ''; ?>" -->
                    <!--        onKeyUp="Getamt(); Get_alert(this.id); amountalert();" -->
                    <!--        onBlur="Getamt(); Get_alert(this.id); amountalert();" -->
                    <!--     class="form-control col-md-7 col-xs-12" -->
                    <!--        id="cheque_amt">-->
                    <!--</td>-->
                    
                    
	<td>
 <input type="text" name='amt_pay' class=" form-control col-md-7 col-xs-12"  value="<?php echo isset($sale_details) ? $sale_details['amt_pay'] : '0'; ?>" id="amt_pay">

														</td>

                <td>
                    <input type="text" 
                        name="narration"  
                        value="<?= isset($sale_details->narration) ? htmlspecialchars($sale_details->narration, ENT_QUOTES, 'UTF-8') : ''; ?>" 
                        class="form-control col-md-7 col-xs-12 " 
                        id="narration"> 
                </td>

           </tr>
           </tbody>
    	  </table>
				</div>
			  </div>

    <div class="row">
        <div class="col">
        <div class="text-center pt-5">
                            <button type="submit" class="btn btn-primary">Save</button>
                 
                        <a href="{{ route('admin.sale_payment.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
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
            $(".select2").select2()
        });
 

 

  $(document).ready(function () {
    // Attach change event to the select field
    $('#mode').change(function () {
        getchequeno(0);
    });
});

</script>

<script>
	
// function gettotaltable() 
// 				{ 
// 					var count=$("#cnt").val();	
					
// 					var total = 0;
// 					for(var i=1; i<=count;i++)
// 					{
							
// 								if ($('#checkbox'+i).is(':checked')) {
// 									//alert("Hiiii");
// 									if(document.getElementById("bank"+i)){
// 										var get1 = $("#bank"+i).val();		if(get1==''){ get1=0; }
// 										var get2 = $("#bank1"+i).val();		if(get2==''){ get2=0; }
// 										var get=parseFloat(get1)+parseFloat(get2);
// 										if(get=='' || get=='undefined'){get=0;}
// 										//alert(get);
// 										total += parseInt(get);
// 									}
									
									
// 								}
						
// 					}
// 					$("#totalvalue").val(total.toFixed(2));
// 					$("#amt_pay").val(total.toFixed(2));
					
// 				}

                function getinputbox(rid,id)
				{
					//alert(id)
					
							if ($('#checkbox'+rid).is(':checked')) {
									//alert("Hiiii");
									
									$("#checkboxshow"+rid).html('<input type="text" class=" form-control required"  placeholder="Enter Amount" name="bank'+rid+'" id="bank'+rid+'" onkeyup="gettotaltable();getinputvalues('+rid+','+id+');" "onblur=gettotaltable();getinputvalues('+rid+','+id+');">');
										
									
								}else
								{
									$("#checkboxshow"+rid).html('');
							
								}
						
				}
                function getinputvalues(rid,id)
				{
					//alert(id);
					
					if ($('#checkbox'+rid).is(':checked')) {
							var get1 = $("#bank"+rid).val();
							if(get1==''){
								get1 = 0;
							}
							var get2 = $("#bank1"+rid).val();
							if(get2==''){
								get2 = 0;
							}
							var get = parseFloat(get1)+parseFloat(get2);
							var balance = (id-get);
						
						if(get>id)
						{
							alert("Amount should not be Greater than payble amount");
							$("#bank"+rid).val('').trigger('change');
							$("#bank1"+rid).val('').trigger('change');
							var balance=0;
							$("#checkboxvalue"+rid).html('<input type="text" class="form-control" value="'+balance+'" readonly> ');
						}else
						{
							
						}
						
					if(get!="")
					{
						$("#checkboxvalue"+rid).html('<input type="text" class="form-control" value="'+balance+'" readonly> ');
					}
					}else
					{
						$("#checkboxvalue"+rid).html('');
					}
				}

                
				
</script>
<script>

function gettotaltable() {
    let total = 0;

    // Iterate through each input field with id="bank" and sum up their values
    $('input[id^="bank_"]').each(function() {
        let value = parseFloat($(this).val());
        if (!isNaN(value)) {
            total += value;
        }
    });

    // Update the total value field
    $('#totalvalue').val(total.toFixed(2)); // Format total to 2 decimal places
    $('#amt_pay').val(total.toFixed(2)); 
}


function hideshow() {
    const customerId = $('#customer_name').val(); // Get selected customer ID

    if (customerId) {
        // Perform AJAX request to fetch records related to the selected customer
        $.ajax({
            url: "{{ route('admin.getCustomerRecords') }}",
            type: "GET",
            data: {
                customerId: customerId, // Pass customer ID
                _token: "{{ csrf_token() }}" // Send CSRF token
            },
            success: function(response) {
                // Check if the response contains data
                if (Array.isArray(response) && response.length > 0) {
                    let tableBody = '';
                    let totalAmount = 0; // Track total amount for all entries

                    response.forEach((info, index) => {
                        tableBody += `
                            <tr id="row_${index}">
                                                    <td style="width: 2%" class="controls">
                            <input type="checkbox" class="checkboxes" name="check_list" 
                                onchange="gettotaltable();getinputbox(${index},${info.Tamount});getinputvalues(${index},${info.Tamount});">
                        </td>
                        <input type='hidden' id='purchaseid_${index}' name='purchaseid[]' value="${info.ID}">
                        <td><input type="text" id="Invoicenumber_${index}" name="Invoicenumber[]" value="${info.Invoicenumber}" class="form-control required"></td>
                        <td>

                            <input type="text" class="form-control required" name="payamt[]" id="payamt_${index}"
                                value="${info.Tamount}" readonly required/>
                        </td>
                        <td id="checkboxshow_${index}">
                            <input type="text" class="form-control required" name="bank[]" id="bank_${index}"
                                onkeyup="gettotaltable();"  required/>
                        </td>
                                                </tr>`;
                        totalAmount += parseFloat(info.Tamount); // Add to total amount
                    });

                    // Update the table body with the fetched data
                    $('#recordsTableBody').html(tableBody);
                    $('#totalvalue').val(totalAmount.toFixed(2)); // Update the total value
                    $('#amt_pay').val(totalAmount.toFixed(2)); // Update the amount to pay field
                } else {
                    // No records found
                    resetTable(); // Call resetTable function to clear the table
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", error);
                alert("An error occurred while fetching the records.");
            }
        });
    } else {
        // No customer is selected, reset the table
        resetTable();
    }
}

// Function to reset the table
function resetTable() {
    // Clear the table body
    $('#recordsTableBody').html('<tr><td colspan="4">Select a customer to view records</td></tr>');

    // Reset the total value field
    $('#totalvalue').val('');
    $('#amt_pay').val('');
}


            </script>
            <script>
                       function validateAmounts() {
    const payAmtInput = document.getElementById("payamt");
    const bankAmtInput = document.getElementById("bank");

    const payAmt = parseFloat(payAmtInput.value) || 0; // Default to 0 if empty or invalid
    const bankAmt = parseFloat(bankAmtInput.value) || 0;

    if (payAmt <= bankAmt) {
        payAmtInput.style.borderColor = "red"; // Highlight the Pay Amount field
        alert("Pay Amount must be greater than Bank Amount!");
    } else {
        payAmtInput.style.borderColor = ""; // Reset the border color if valid
    }
}
            </script>


