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

              <!-- <div class="form-group col-md-3">
                        <label for="inputEmail3">Expenses No.<span class="required" style="color:red;">*</span></label>
                        <input type="text" name='exp_no' value="{{ old('exp_no', $nextBillNo)}}"  class="required form-control" id="exp_no">
                    </div> -->

                   <!-- <div class="form-group col-md-3">
                                    <label> Expense Date <span class="required" style="color:red;">*</span></label>
                                    <input class="datepicker-here form-control" id="billdate" name="billdate" type="text" 
                                        value="{{ isset($expense->billdate) ? \Carbon\Carbon::parse($expense->billdate)->format('d-m-Y') : (old('billdate') ?? date('d-m-Y')) }}" 
                                        data-language="en" 
                                        placeholder="Enter Date" 
                                        data-date-format="dd-mm-yyyy">

                                    @error('billdate')
                                        <span class="text-danger"><strong>{{ $message }}</strong></span>
                                    @enderror
                </div> -->



                    <div class="form-group col-md-4">
                       <label>Name  <span class="required required_lbl">*</span></label>
                       <input type="text" name='name' id="name" placeholder="Name" value="{{  isset($company->name) ? $company->name : old('name') }}"class=" required form-control" >
	
                            @error('name')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                    </div>

                    <div class="form-group col-md-4">
                       <label>Customer Code</label>
                         <input type="text" name='code' id="code" placeholder="Customer Code"value="{{  isset($company->code) ? $company->code : old('code') }}"  class="form-control" >
              
                    </div>

                    <div class="form-group col-md-4">
                        <label>Website</label>
                        <input type="text" name='website' id="website" placeholder="Website" value="{{  isset($company->website) ? $company->website : old('website') }}"  class="form-control" >
                 
                    </div>
          </div>
<br>
<br>
       <div class="row">
             <div class="form-group col-md-4">
                 <label>Mobile <span class="required required_lbl">*</span></label>
                   <input type="text" name='mobile' id="mobile" placeholder="Mobile" value="{{  isset($company->mobile) ? $company->mobile : old('mobile') }}"class=" required number form-control  " >
         
               </div>

            <div class="form-group col-md-4">
                <label>Phone</label>
                  <input type="text" name='phone' id="phone" placeholder="Phone" value="{{  isset($company->phone) ? $company->phone : old('phone') }}"  class=" number form-control  " >
        
             </div>


               <div class="form-group col-md-4">
                  <label for="inputEmail3"  class="control-label">Email</label>
                      <input type="text" name="email" id="email" placeholder="Email"value="{{  isset($company->email) ? $company->email : old('email') }}"class=" form-control email"       />	
             
                </div>

                </div>
                <div class="row" style="margin-top:20px;">
    <!-- Company Signature -->
    <div class="form-group col-md-4">
    <label for="sign" class="col-md-6 control-label">
        Company Signature <span class="required required_lbl"></span>
    </label>
         <div class="col-sm-7">
                    <div id="signimg_div">
                    <!-- Display the signature image -->
                    <img  src="{{ isset($company->sign) && !empty($company->sign) ? asset('public/' . $company->sign) : asset('public/uploads/signatures/default-sign-path.jpg') }}"  width="70px"  height="50px" id="sign_preview"  >
         <span   class="fa fa-trash"   style="cursor:pointer; height:25px; width:25px"   onclick="removeImage('sign');"> </span>

    <input   type="hidden"   name="sign"   id="sign_hidden" value="{{ isset($company->sign) ? $company->sign : old('sign') }}"  />
</div>

        <div id="signimgdiv">
            <input type="file" id="sign" name="sign" class="form-control" onchange="previewImage(event, 'sign_preview', 'sign_hidden');" />
        </div>
    </div>
</div>

    <!-- Company Logo -->
    <div class="form-group col-md-4">
        <label for="logo" class="col-md-6 control-label">
            Company Logo <span class="required required_lbl"></span>
        </label>
        <div class="col-sm-7">
            <div id="logoimg_div">
                <img src=" {{ isset($company->logo) && !empty($company->logo) ? asset('public/' . $company->logo) : asset('public/uploads/logos/default-sign-path.jpg') }}"  width="70px" height="50px" id="logo_preview">
                <span class="fa fa-trash" style="cursor:pointer; height:25px; width:25px" onclick="removeImage('logo');"></span>
                <input type="hidden" name="logo" id="logo_hidden" value="{{ $company->logo ?? '' }}" />
            </div>
            <div id="logoimgdiv">
                <input type="file" id="logo" name="logo" class="form-control" onchange="previewImage(event, 'logo_preview', 'logo_hidden');" />
            </div>
        </div>
    </div>
</div>

            <div class="row" style="margin-top:20px;">
		
                        <div class="form-group col-md-4">
                          <label>CIN</label>
                                <input type="text" name='CIN' id="CIN" placeholder="CIN" value="{{  isset($company->CIN) ? $company->CIN : old('CIN') }}" class=" required form-control" >
                        </div>
                        
                        <div class="form-group col-md-4">
                          <label>Transportation GST(%)</label>
                                <input type="text" name='trans_cost' id="trans_cost" placeholder="Tansportation GST (%)" value="{{  isset($company->trans_cost) ? $company->trans_cost : old('trans_cost') }}" class=" required form-control" >
                        </div>
                        
                        <div class="col-md-4">
                    <label>GST Number</label><br>
                    <input type="radio" class="pop" name="option" onclick="selectoptn();" value="yes"
                        {{ (isset($company->option) && $company->option == 'yes') || old('option') == 'yes' ? 'checked' : 'checked' }}>
                    <span style="font-size:16px;">Yes</span>

                    <input type="radio" class="pop" style="margin-left:10px;" onclick="selectoptn();" name="option" value="no"
                        {{ (isset($company->option) && $company->option == 'no') || old('option') == 'no' ? 'checked' : '' }}>
                    <span style="font-size:16px;">No</span>
                </div>

       </div>

       <div class="row" style="margin-top:20px;">
		

                    
                <div class="col-md-4" id="gstdiv" style="display:{{ (isset($company->option) && $company->option == 'yes') || old('option') == 'yes' || !isset($company->option) ? 'block' : 'none' }};">
                    <label>GST No</label>
                    <input type="text" name="gst_no" id="gst_no" placeholder="GST No"
                        value="{{ isset($company->gst_no) ? $company->gst_no : old('gst_no') }}" class="form-control" />
                </div>

            <div class="form-group col-md-4">
    <label>Address <span class="required required_lbl">*</span></label>
    <textarea 
        name="address" 
        id="address" 
        placeholder="Address" 
        class="form-control required">{{ isset($company->address) ? $company->address : old('address') }}</textarea>
</div>

				
                <div class="form-group col-md-4">
                <label>Holiday</label>
                <select class="form-control" id="daySelect" name="days">
                    <option value="">Select Day</option>
                    <option value="Monday" {{ (isset($company->days) && $company->days == 'Monday') || old('days') == 'Monday' ? 'selected' : '' }}>Monday</option>
                    <option value="Tuesday" {{ (isset($company->days) && $company->days == 'Tuesday') || old('days') == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                    <option value="Wednesday" {{ (isset($company->days) && $company->days == 'Wednesday') || old('days') == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                    <option value="Thursday" {{ (isset($company->days) && $company->days == 'Thursday') || old('days') == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                    <option value="Friday" {{ (isset($company->days) && $company->days == 'Friday') || old('days') == 'Friday' ? 'selected' : '' }}>Friday</option>
                    <option value="Saturday" {{ (isset($company->days) && $company->days == 'Saturday') || old('days') == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                    <option value="Sunday" {{ (isset($company->days) && $company->days == 'Sunday') || old('days') == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                </select>
</div>

		
		</div>

      <div class="row">
                        <div class="col">
                        <div class="text-center pt-5">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                
                                        <a href="{{ route('admin.company.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
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
function getchequeno(flag) {
    var paymentType = $('#mode').val(); // Get the selected payment mode
    var totalValue = $("#totalvalue").val(); // Get the total value

    // Reset all fields to default state
    $('#cheque_no, #cheque_amt, #amt_pay').val('');
    $('#cheque_no, #cheque_amt, #amt_pay').prop('readonly', false);
    $('#cheque_no, #cheque_amt, #amt_pay').removeClass("required form-error");
    $("#cheque_no").removeClass('number');

  
    if (paymentType === "cash") {
        // Case: Cash payment
        $('#cheque_no, #cheque_amt').prop('readonly', true); // Disable cheque fields
        $('#amt_pay').prop('readonly', false); // Enable amount field
        if (flag === 0) {
            $("#amt_pay").val(totalValue); // Set amount to total value if flag is 0
        }
    } else if (paymentType === "cheque" || paymentType === "DD" || paymentType === "RTGS") {
        // Case: Cheque/DD/RTGS payment
        $('#amt_pay').prop('readonly', true).val('0'); // Disable amount field and set to 0
        $('#cheque_no, #cheque_amt').addClass("required"); // Mark cheque fields as required
        if (flag === 0) {
            $('#cheque_amt').val(totalValue); // Set cheque amount to total value if flag is 0
        }
    } else if (paymentType === "cash and cheque") {

        $('#cheque_no, #cheque_amt').addClass("required");
        $('#amt_pay').addClass("required"); 
        $('#amt_pay').prop('readonly', false); 
    } else if (paymentType === "credit") {

        $('#cheque_no, #cheque_amt, #amt_pay').prop('readonly', true).val(''); // Disable all fields
        $('#amt_pay').val('0'); // Set amount to 0
    }
}

function selectoptn() {
    var optn = $("input:radio.pop:checked").val(); // Get the selected radio button value
    if (optn === 'yes') {
        document.getElementById('gstdiv').style.display = "block"; // Show GST No input
    } else {
        document.getElementById('gstdiv').style.display = "none"; // Hide GST No input
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
        };
        reader.readAsDataURL(file);
    }
}


    function removeImage(field) {
        document.getElementById(`${field}_preview`).src = 'default-placeholder.jpg';
        document.getElementById(`${field}_hidden`).value = '';
        document.getElementById(field).value = ''; // Clear file input
    }
</script>

