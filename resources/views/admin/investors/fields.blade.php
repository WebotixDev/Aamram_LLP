<div class="form theme-form">
@php
// Check if editing an existing record
if (isset($invoice) && $invoice->ReceiptNo) {
    // Use the existing ReceiptNo for edit form
    $nextBillNo = $invoice->ReceiptNo;
} else {
    // Generate the next ReceiptNo for add form
    $lastBillNo = DB::table('investors_payment')->max('ReceiptNo');
    $nextBillNo = $lastBillNo ? $lastBillNo + 1 : 1;
}


@endphp
      <div class="row ">

            <div class="form-group col-md-4">
            <label for="ReceiptNo">  No <span class="required">*</span></label>
            <input type="text" id="ReceiptNo"
                name="ReceiptNo"
                value="{{ old('ReceiptNo', $nextBillNo) }}"
                class="required form-control" readonly>
                </div>


                                <div class="form-group col-md-4">
                                    <label>Date <span class="required" style="color:red;">*</span></label>

                                    <input class="datepicker-here form-control" id="PurchaseDate" name="PurchaseDate" type="text"
                                        value="{{ isset($investor->PurchaseDate) ? \Carbon\Carbon::parse($investor->PurchaseDate)->format('d-m-Y') : (old('PurchaseDate') ?? date('d-m-Y')) }}"
                                        data-language="en"
                                        placeholder="Enter Date"
                                        data-date-format="dd-mm-yyyy" data-auto-close="true" >

                                    @error('PurchaseDate') 
                                        <span class="text-danger"><strong>{{ $message }}</strong></span>
                                    @enderror
                </div>

                <div class="form-group col-md-3" style="padding-top: 40px">
                    <label style="width: 35%;" for="status" class="col-md-6 control-label">
                        Type :- <span class="required required_lbl"></span>
                    </label>
                    <label>
                        <input type="radio" name="type" value="paid"
                            {{ !isset($investor->type) || $investor->type == 'paid' ? 'checked' : '' }} onclick="toggleBalance()"> Paid
                    </label>
                    <label style="margin-left: 30px;">
                        <input type="radio" name="type" value="receive"
                            {{ isset($investor->type) && $investor->type == 'receive' ? 'checked' : '' }} onclick="toggleBalance()" > Receive
                    </label>
                </div>

    </div>
<br>
    <div class="row">

                <div class="form-group col-md-4">
                    <label>Investors Name <span class="required" style="color:red;">*</span></label>
                    <select class="form-select select2" id="investor_name" name="investor_name" data-placeholder="Select" required>
                        <option value="">Select Investor</option>
                        @foreach(DB::table('investors_name')->get() as $invest)
                        <option value="{{ $invest->id }}"
                            @if(isset($investor->investor_name) && $investor->investor_name == $invest->id)
                                selected
                            @endif
                        >
                            {{ $invest->investors_name }}
                        </option>
                    @endforeach                    </select>
                    <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addCustomerModal">Add</button>
                </div>
    </div>

    <br><br>




                        <br><br><br>

    <div class="row">
			   <div style="width:100%;">
				<table id="pmttble" class="table table-bordered table-hover"  style="width:100%; margin-right: 60px;background-color:white;">
            <tbody>
      	    <tr>
			<th style="width: 18%;">Date</th>

			<th style="width: 18%;">Payment Method</th>
            <th style="width: 15%;">Cheque No / Trasaction ID</th>
            <th style="width: 13%;"> Balance</th>
            <th style="width: 13%;"> Amount</th>
			<th style="width: 17%;">Note</th>
            </tr>
           <tr>
			 <td>
				<div class="col-md-12 col-sm-4 col-xs-12">

				  <div class="input-group date">

                                <input class="datepicker-here form-control" id="date" name="date" type="text"
                                        value="{{ isset($investor->date) ? \Carbon\Carbon::parse($investor->date)->format('d-m-Y') : (old('date') ?? date('d-m-Y')) }}"
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
                                        <select class="select2" tabindex="-1" data-placeholder="Select Payment Method" style="width:100%" name="mode" id="mode">
                                    <option></option>
                                    <option value="cash" @if(isset($investor->mode) && $investor->mode == 'cash') selected @endif>Cash</option>
                                    <option value="cheque" @if(isset($investor->mode) && $investor->mode == 'cheque') selected @endif>Cheque</option>
                                <option value="DD" @if(isset($investor->mode) && $investor->mode == 'DD') selected @endif>DD</option>

                                    <option value="RTGS" @if(isset($investor->mode) && $investor->mode == 'RTGS') selected @endif>RTGS</option>
                                    <option value="E-cash" @if(isset($investor->mode) && $investor->mode == 'E-cash') selected @endif>E-Payment</option>
                                </select>

                                        </div>

														</td>

                    <td>
                <div class="col-md-12 col-sm-4 col-xs-12">
             <input type="text"
                      name="cheque_no"
                                readonly
                                value="<?= isset($investor->cheque_no) ? htmlspecialchars($investor->cheque_no, ENT_QUOTES, 'UTF-8') : ''; ?>"
                                class="form-control col-md-7 col-xs-12"
                                id="cheque_no">
                        </div>
                    </td>


                    @php

                    $displayValue = isset($investor) ? $balance + $investor->amt_pay: '';
                @endphp

                <td>
                    <input
                        type="text"
                        name="balance"
                        class="form-control col-md-7 col-xs-12"
                        id="paid_amount_display"
                        value="{{ $displayValue }}"
                        readonly>
                </td>



	<td>


 <input type="text" name='amt_pay' class=" form-control col-md-7 col-xs-12"  value="<?php echo isset($investor) ? $investor['amt_pay'] : '0'; ?>" id="amt_pay">

														</td>

                <td>
                    <input type="text"
                        name="narration"
                        value="<?= isset($investor->narration) ? htmlspecialchars($investor->narration, ENT_QUOTES, 'UTF-8') : ''; ?>"
                        class="form-control col-md-7 col-xs-12 "
                        id="narration">
                </td>

           </tr>
           </tbody>
    	  </table>
				</div>
			  </div>


              <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addCustomerModalLabel">Add Investor Head</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addCustomerForm">

                                <div class="mb-3">
                                    <label for="new_investor_name" class="form-label">Investor Name</label>
                                    <input type="text" class="form-control" id="new_investor_name" name="name">
                                </div>
                                <div class="text-center">
                                    <button type="button" class="btn btn-success" onclick="submitInvestorForm()">Save</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

    <div class="row">
        <div class="col">
        <div class="text-center pt-5">
                            <button type="submit" class="btn btn-primary">Save</button>

                        <a href="{{ route('admin.investors.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
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

function getchequeno(flag) {
    var type = $('#mode').val(); // Get selected payment method
    var totalvalue = $("#totalvalue").val(); // Get total value

    // Reset all fields first
    $('#cheque_no, #cheque_amt, #amt_pay')
        .prop('readonly', true)
        .val('')
        .removeClass("required form-error number");

    if (type === "cash") {
        if (flag === 0) {
            $("#amt_pay").val(totalvalue);
        }
        $('#amt_pay').prop('readonly', false).addClass("required");
    } else if (type === "cheque" || type === "DD" || type === "RTGS" || type === "E-cash") {
        $('#cheque_no').prop('readonly', false).addClass("required");
        $('#amt_pay').prop('readonly', false).addClass("required");

        if (flag === 0) {
            $('#cheque_amt').val(totalvalue);
        }
    } else if (type === "cash and cheque") {
        $('#cheque_no, #cheque_amt, #amt_pay').prop('readonly', false).addClass("required");
    } else if (type === "credit") {
        $('#amt_pay').val('0'); // Credit has no amount to pay directly
    }
}
</script>

<script>

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

<script>
    function toggleBalanceColumn() {
        var typeSelected = document.querySelector('input[name="type"]:checked').value;
        var balanceTh = document.querySelector("#pmttble th:nth-child(4)"); // Balance Column Header
        var balanceTds = document.querySelectorAll("#pmttble td:nth-child(4)"); // Balance Column Data

        if (typeSelected === "receive") {
            balanceTh.style.display = "none";
            balanceTds.forEach(td => td.style.display = "none");
        } else {
            balanceTh.style.display = "table-cell";
            balanceTds.forEach(td => td.style.display = "table-cell");
        }
    }

    // Ensure function runs on page load
    window.onload = toggleBalanceColumn;

    // Add event listeners to the radio buttons
    document.addEventListener("DOMContentLoaded", function () {
        var typeRadios = document.querySelectorAll('input[name="type"]');
        typeRadios.forEach(radio => {
            radio.addEventListener("change", toggleBalanceColumn);
        });
    });



    function submitInvestorForm() {
    let invest_name = document.getElementById('new_investor_name').value.trim();

    $.ajax({
        url: '{{ route("admin.investors_insert") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        data: {
            name: invest_name,
        },
        success: function (response) {
            if (response.success) {
                alert('Expense added successfully!');
                // $('#addCustomerForm')[0].reset();
                // $('#expense_name').val(null).trigger('change'); // Reset Select2
                // $('#addCustomerModal').modal('hide');

                // Refresh the window after a slight delay for better user experience
                location.reload();
            } else {
                alert(response.message || 'Failed to add expense.');
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
            alert('An error occurred while submitting the form.');
        }
    });
}
</script>


   <script>
    $(document).ready(function () {
        $('#investor_name').on('change', function () {
            var investorId = $(this).val();

            console.log(investorId);
            if (investorId) {
                $.ajax({
                    url: '{{ route("admin.investor-balance", ":id") }}'.replace(':id', investorId),
                    method: 'GET',
                    success: function (response) {
                        if (response.balance !== undefined) {
                            $('#paid_amount_display').val(response.balance);
                        } else {
                            $('#paid_amount_display').val('Investor not found');
                        }
                    },
                    error: function () {
                        $('#paid_amount_display').val('Error');
                    }
                });
            } else {
                $('#paid_amount_display').val('');
            }
        });
    });
</script>



