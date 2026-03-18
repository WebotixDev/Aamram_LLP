<div class="form theme-form">
    <!-- General Information Section -->
    @php

 // Check if $invoice exists (edit mode)
 if (isset($invoice) && $invoice->exp_no) {
     $nextBillNo = $invoice->exp_no; // Use the existing invoice number
 } else {
     // Generate the next invoice number for create mode
     $lastBillNo = DB::table('expense')->max('exp_no');
     $nextBillNo = $lastBillNo ? $lastBillNo + 1 : 1;
 }


@endphp
          <div class="row">
              <div class="form-group col-md-4">
                        <label for="inputEmail3">Expenses No.<span class="required" style="color:red;">*</span></label>
                        <input type="text" name='exp_no' value="{{ old('exp_no', $nextBillNo)}}"  class="required form-control" id="exp_no">
                    </div>


                   <div class="form-group col-md-4">
                                    <label> Expense Date <span class="required" style="color:red;">*</span></label>
                                    <input class="datepicker-here form-control" id="billdate" name="billdate" type="text"
                                        value="{{ isset($expense->billdate) ? \Carbon\Carbon::parse($expense->billdate)->format('d-m-Y') : (old('billdate') ?? date('d-m-Y')) }}"
                                        data-language="en"
                                        placeholder="Enter Date"
                                        data-date-format="dd-mm-yyyy" data-auto-close="true">

                                    @error('billdate')
                                        <span class="text-danger"><strong>{{ $message }}</strong></span>
                                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label>Expense Category <span class="required" style="color:red;">*</span></label>
                    <select class="form-select select2" id="expense_name" name="expense_name" data-placeholder="Select" required onchange="fetchExpenseNames(this.value)">
                        <option value="">Select Expense</option>
                        @foreach(DB::table('expense_category')->get() as $expenses)
                            <option value="{{ $expenses->id }}"
                                @if(isset($expense->expense_name) && $expense->expense_name == $expenses->id)
                                    selected
                                @endif
                            >
                                {{ $expenses->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
          </div>
          <br>
          <div class="row">
            <?php
             if(isset($expense)){?>
                <div class="form-group col-md-4">
                <label>Expense Name <span class="required" style="color:red;">*</span></label>
                <select class="form-select select2" id="exp_type" name="exp_type" data-placeholder="Select" required>
                    <option value="">Select Expense</option>
                    @foreach(DB::table('subjects')->where('expense_name', $expense->expense_name)->get() as $subject)
                    <option value="{{ $subject->id }}"
                        @if(isset($expense->exp_type) && $expense->exp_type == $subject->id)
                            selected
                        @endif
                    >
                        {{ $subject->subject_name }}
                    </option>
                @endforeach

                </select>

            </div>
<?php } else { ?>

            <div class="form-group col-md-4">
                <label>Expense Name <span class="required" style="color:red;">*</span></label>
                <select class="form-select select2" id="exp_type" name="exp_type" data-placeholder="Select" required>
                    <option value="">Select Expense</option>
                    <!-- Options will be loaded dynamically -->
                </select>
                <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addCustomerModal">Add</button>
            </div>
<?php }?>


                    <div class="form-group col-md-4">
                       <label for="inc_by">Payable To <span class="required" style="color:red;">*</span></label>
                            <input type="text" name="inc_by"  value="{{  isset($expense->inc_by) ? $expense->inc_by : old('inc_by') }}"  class="required form-control" id="inc_by">
                    </div>

                  <div class="form-group col-md-4">
                                <label for="logo" class="col-md-6 control-label">
                                    Expense receipt <span class=" required_lbl"></span>
                                </label>
                                <div class="col-sm-7">
                                    <div id="logoimg_div" style="display: {{ isset($expense->expense_receipt) && !empty($expense->expense_receipt) ? 'block' : 'none' }};">
                                        <img src="{{ isset($expense->expense_receipt) && !empty($expense->expense_receipt) ? asset('public/uploads/expense_receipt/' . $expense->expense_receipt) : asset('public/uploads/expense_receipt/default-sign-path.jpg') }}"
                                            width="70px" height="50px" id="logo_preview">
                                        <span class="fa fa-trash" style="cursor:pointer; height:25px; width:25px" onclick="removeImage('logo');"></span>
                                        <input type="hidden" name="expense_receipt" id="logo_hidden" value="{{ $expense->expense_receipt ?? '' }}" />
                                    </div>
                                    <div id="logoimgdiv">
                                        <input type="file" id="logo" name="expense_receipt" class="form-control" onchange="previewImage(event, 'logo_preview', 'logo_hidden');" />
                                    </div>
                                </div>
                            </div>
          </div>
<br>
<br>
       <div class="row">
                <div style="width:100%;">
                                <table id="pmttble" class="table table-bordered table-hover"  style="width:100%; margin-right: 60px;background-color:white;">
                            <tbody>
                            <tr>
                            <th style="width: 18%;">Date</th>

                            <th style="width: 18%;">Payment Method</th>
                            <th style="width: 15%;">Cheque No / Trasaction ID</th>
                            <th style="width: 13%;"> Amount</th>
                            <th style="width: 17%;">Narration</th>
                            </tr>
                        <tr>
                            <td>
                                <div class="col-md-12 col-sm-4 col-xs-12">

                                <div class="input-group date">

                                                <input class="datepicker-here form-control" id="date" name="date" type="text"
                                                        value="{{ isset($expense->date) ? \Carbon\Carbon::parse($expense->date)->format('d-m-Y') : (old('date') ?? date('d-m-Y')) }}"
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
                                            <select class="select2" data-placeholder="Select Payment Method" onChange="getchequeno(0);" style="width:100%" name="mode" id="mode">
                                                <option value="cash">Cash</option>
                                                <option value="cheque">Cheque</option>
                                                <option value="DD">DD</option>
                                                <option value="RTGS">RTGS</option>
                                                <option value="E-cash">E-Payment</option>
                                            </select>
                                        </div>
                                    </td>

                                    <td>
                                <div class="col-md-12 col-sm-4 col-xs-12">
                            <input type="text"
                                    name="cheque_no"

                                                value="<?= isset($expense->cheque_no) ? htmlspecialchars($expense->cheque_no, ENT_QUOTES, 'UTF-8') : ''; ?>"
                                                class="form-control col-md-7 col-xs-12"
                                                id="cheque_no">
                                        </div>
                                    </td>

                                    <!--<td>-->
                                    <!--    <input type="text"-->
                                    <!--        name="cheque_amt"-->
                                    <!--        readonly-->
                                    <!--        value="<?= isset($expense->cheque_amt) ? htmlspecialchars($expense->cheque_amt, ENT_QUOTES, 'UTF-8') : ''; ?>"-->
                                    <!--        onKeyUp="Getamt(); Get_alert(this.id); amountalert();"-->
                                    <!--        onBlur="Getamt(); Get_alert(this.id); amountalert();"-->
                                    <!--    class="form-control col-md-7 col-xs-12"-->
                                    <!--        id="cheque_amt">-->
                                    <!--</td>-->

                    <td>
                                    <input type="text"
                                        name="amt_pay"
                                        value="<?= isset($expense->amt_pay) ? htmlspecialchars($expense->amt_pay, ENT_QUOTES, 'UTF-8') : ''; ?>"
                                        class="required number form-control col-md-7 col-xs-12"
                                        id="amt_pay">
                                </td>

                                <td>
                                    <input type="text"
                                        name="narration"
                                        value="<?= isset($expense->narration) ? htmlspecialchars($expense->narration, ENT_QUOTES, 'UTF-8') : ''; ?>"
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

                                        <a href="{{ route('admin.expense.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                                        </div>
                        </div>
                    </div>
                    </div>




                    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addCustomerModalLabel">Add Expense Head</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="addCustomerForm">
                                            <div class="mb-3">
                                                <label>Expense Category <span class="required" style="color:red;">*</span></label>
                                                <select class="form-select select2" id="expense_nameee" name="expense_namess" required>
                                                    <option value="">Select Expense</option>
                                                    @foreach(DB::table('expense_category')->get() as $expensee)
                                                        <option value="{{ $expensee->id }}">{{ $expensee->name }}</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                            <div class="mb-3">
                                                <label for="new_customer_name" class="form-label">Expense Name</label>
                                                <input type="text" class="form-control" id="new_expense_name" name="subject_name">
                                            </div>
                                            <div class="text-center">
                                                <button type="button" class="btn btn-success" onclick="submitCustomerForm()">Save</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>


<script>
    function updatevalue(){
        var checkbox = document.getElementById('district_status');
        if (checkbox.value == 1) {
            checkbox.value = 0;
        } else {
            checkbox.value = 1;
        }
    }
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// function getchequeno(flag) {
//     var paymentType = $('#mode').val(); // Get the selected payment mode
//     var totalValue = $("#totalvalue").val(); // Get the total value

//     // Reset all fields to default state
//     $('#cheque_no, #cheque_amt, #amt_pay').val('');
//     $('#cheque_no, #cheque_amt, #amt_pay').prop('readonly', false);
//     $('#cheque_no, #cheque_amt, #amt_pay').removeClass("required form-error");
//     $("#cheque_no").removeClass('number');


//     if (paymentType === "cash") {
//         // Case: Cash payment
//         $('#cheque_no, #cheque_amt').prop('readonly', true); // Disable cheque fields
//         $('#amt_pay').prop('readonly', false); // Enable amount field
//         if (flag === 0) {
//             $("#amt_pay").val(totalValue); // Set amount to total value if flag is 0
//         }
//     } else if (paymentType === "cheque" || paymentType === "DD" || paymentType === "RTGS") {
//         // Case: Cheque/DD/RTGS payment
//         $('#amt_pay').prop('readonly', true).val('0'); // Disable amount field and set to 0
//         $('#cheque_no, #cheque_amt').addClass("required"); // Mark cheque fields as required
//         if (flag === 0) {
//             $('#cheque_amt').val(totalValue); // Set cheque amount to total value if flag is 0
//         }
//     } else if (paymentType === "cash and cheque") {

//         $('#cheque_no, #cheque_amt').addClass("required");
//         $('#amt_pay').addClass("required");
//         $('#amt_pay').prop('readonly', false);
//     } else if (paymentType === "credit") {

//         $('#cheque_no, #cheque_amt, #amt_pay').prop('readonly', true).val(''); // Disable all fields
//         $('#amt_pay').val('0'); // Set amount to 0
//     }
// }



function submitCustomerForm() {
    let customerName = document.getElementById('new_expense_name').value.trim();
    let expenseCategory = document.getElementById('expense_nameee').value; // Fetch selected option

    $.ajax({
        url: '{{ route("admin.subjects") }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        data: {
            subject_name: customerName,
            expense_namess: expenseCategory, // Ensure this value is passed
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
   function fetchExpenseNames(expenseId, selectedExpType) {
    if (expenseId) {
        $.ajax({
            url: "{{ route('admin.get.expense.names') }}",  // Adjust the route accordingly
            type: "GET",
            data: { expense_id: expenseId },
            success: function(response) {
                let expTypeDropdown = $('#exp_type');
                expTypeDropdown.empty().append('<option value="">Select Expense</option>');

                $.each(response, function(key, value) {
                    let isSelected = selectedExpType && selectedExpType == value.subject_name ? 'selected' : '';
                    expTypeDropdown.append(`<option value="${value.id}" ${isSelected}>${value.subject_name}</option>`);
                });

                // Reinitialize Select2 if used
                expTypeDropdown.trigger('change');
            }
        });
    } else {
        $('#exp_type').empty().append('<option value="">Select Expense</option>');
    }
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

