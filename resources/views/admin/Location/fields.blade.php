<div class="form theme-form">
    <!-- General Information Section -->

          <div class="row">



                    <div class="form-group col-md-4">
                    <label>Location Name <span class="required" style="color:red;">*</span></label>
                    <input type="text" id="location" required name="location" class="form-control" placeholder="Enter location Name" value="{{  isset($Location->location) ? $Location->location : old('location') }}" required>
                            @error('location')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                    </div>

                <div class="form-group col-md-4">
                    <label>Purchase Manager Name <span class="required" style="color:red;">*</span></label>
                    <input type="text" id="purchase_manager" required name="purchase_manager" class="form-control" placeholder="Enter Name" value="{{  isset($Location->purchase_manager) ? $Location->purchase_manager : old('purchase_manager') }}" required>
                            @error('purchase_manager')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label>Mobile No <span class="required" style="color:red;">*</span></label>
                        <input type="number" id="mobile_no" name="mobile_no" required  class="form-control" placeholder="Enter Mobile No" value="{{  isset($Location->mobile_no) ? $Location->mobile_no : old('mobile_no') }}" >
                                @error('mobile_no')
                        <span class="text-danger">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                        </div>


          <div class="row pt-2">
               <div class="form-group col-md-4 mb-3">
                                <label for="description">Address</label>
                                <textarea name="address" id="address"  required placeholder="Address" class="form-control  " ><?php if(isset($Location)){ echo $Location->address; } ?></textarea>
                            </div>


          </div>
                    <div class="row">
                        <div class="col">
                        <div class="text-center pt-5">
                                            <button type="submit" class="btn btn-primary">Save</button>

                                        <a href="{{ route('admin.Location.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                                        </div>
                        </div>
                    </div>
          </div>
          </div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
