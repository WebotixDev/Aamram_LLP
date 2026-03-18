<div class="form theme-form">
    <!-- General Information Section -->

          <div class="row">



                    <div class="form-group col-md-4">
                    <label>Company Name <span class="required" style="color:red;">*</span></label>
                    <input type="text" id="transporter" name="transporter" class="form-control" placeholder="Enter Company Name" required value="{{  isset($Transporter->transporter) ? $Transporter->transporter : old('transporter') }}" required>
                            @error('transporter')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label>Mobile No <span class="required" style="color:red;">*</span></label>
                        <input type="number" id="mobile_no" name="mobile_no" class="form-control" placeholder="Enter Mobile No" required value="{{  isset($Transporter->mobile_no) ? $Transporter->mobile_no : old('mobile_no') }}" >
                                @error('mobile_no')
                        <span class="text-danger">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                        </div>
            <div class="form-group col-md-4">
                <label for="gstin">GSTIN</label>
                <input
                    type="text"
                    id="gstin"
                    name="gstin"
                    class="form-control"
                    placeholder="Enter GSTIN No"
                    value="{{ isset($Transporter->gstin) ? $Transporter->gstin : old('gstin') }}"
                >

                @error('gstin')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
          </div>

          <div class="row pt-2">

                       <div class="form-group col-md-4">
                                <label for="description">Address</label>
                                <textarea name="address" id="address" required  placeholder="Address" class="form-control  " ><?php if(isset($Transporter)){ echo $Transporter->address; } ?></textarea>
                            </div>



          </div>
                    <div class="row">
                        <div class="col">
                        <div class="text-center pt-5">
                                            <button type="submit" class="btn btn-primary">Save</button>

                                        <a href="{{ route('admin.supplier.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                                        </div>
                        </div>
                    </div>
          </div>
          </div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
