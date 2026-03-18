<div class="form theme-form">
    <!-- General Information Section -->
     
     <div class="row">

           <div class="form-group col-md-4">
                    <label>Bank Name<span class="required required_lbl">*</span></label>
                        <input type="text" name='bank_name' id="bank_name" placeholder="Name" value="{{  isset($accounts->bank_name) ? $accounts->bank_name : old('bank_name') }}"  class=" required form-control">
                        @error('bank_name')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                    </div>

		<div class="form-group col-md-4">
                                  <label>ACCOUNT NAME:<span class="required required_lbl"></span></label>
									<input type="text" name='account_name' id="account_name" placeholder="OBalance" value="{{  isset($accounts->account_name) ? $accounts->account_name : old('account_name') }}" class="  form-control">
                                    @error('IFSC')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
								</div>
								
                    <div class="form-group col-md-4">
                    <label>Account No <span class="required required_lbl">*</span></label>
                        <input type="text" name='ACNo' id="ACNo" placeholder="AC.No"
                        value="{{  isset($accounts->ACNo) ? $accounts->ACNo : old('ACNo') }}" 
                            class=" required form-control" onblur="chkAccountno();">
                            @error('ACNo')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                    </div>


                    
          </div>
<br>
        
     <div class="row">
         
         <div class="form-group col-md-4">
                    <label>Branch<span class="required required_lbl">*</span></label>

                        <input type="text" name='Branch' id="Branch" placeholder="Branch"
                        value="{{  isset($accounts->Branch) ? $accounts->Branch : old('Branch') }}" 
                            class=" required form-control">
                            @error('Branch')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                    </div>
                                <div class="form-group col-md-4">
                                    <label>IFSC <span class="required required_lbl">*</span></label>
									<input type="text" name='IFSC' id="IFSC" placeholder="IFSC" value="{{  isset($accounts->IFSC) ? $accounts->IFSC : old('IFSC') }}"  class=" required form-control">
                                    @error('IFSC')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
								</div>


					

                                
							<div class="form-group col-md-4">
    <label>Type Of Account<span class="required required_lbl">*</span></label>
    <select class="select2 form-control" style="width:100%" id="accounttype" name="accounttype">
        <option value="" disabled {{ old('accounttype', $accounts->accounttype ?? '') == '' ? 'selected' : '' }}>Select Type</option>
        <option value="Saving" {{ old('accounttype', $accounts->accounttype ?? '') == 'Saving' ? 'selected' : '' }}>Saving</option>
        <option value="Current" {{ old('accounttype', $accounts->accounttype ?? '') == 'Current' ? 'selected' : '' }}>Current</option>
    </select>
</div>

</div>
<br>
        <div class="row">
        <div class="form-group col-md-4">
                        <label>Set As Default Bank</label><br>
                        <input type="radio"   name="default_bank"  value="Yes"  {{ old('default_bank', isset($accounts->default_bank) ? $accounts->default_bank : '') == 'Yes' ? 'checked' : '' }}>
                        <span style="font-size:16px;">Yes</span>
    
                            <input style="margin-left:10px;"  type="radio"  name="default_bank"  value="No"  {{ old('default_bank', isset($accounts->default_bank) ? $accounts->default_bank : '') == 'No' ? 'checked' : '' }}>
                            <span style="font-size:16px;">No</span>
            </div>

   </div>
      


    <div class="row">
        <div class="col">
        <div class="text-center pt-5">
                            <button type="submit" class="btn btn-primary">Save</button>
                 
                        <a href="{{ route('admin.profile.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
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

