
<div class="form theme-form">
    <!-- General Information Section -->

          <div class="row">


                    <div class="form-group col-md-4">
                    <label>Product Size<span class="required" style="color:red;">*</span></label>
                    <input type="text" id="product_size" name="product_size" class="form-control" value="{{  isset($product_size->product_size) ? $product_size->product_size : old('product_size') }}" required>
                            @error('exp_type')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                    </div>


                          <div class="form-group col-md-3 pt-2">
                            <label for="status" class="col-md-6 control-label">
                                Status <span class="required required_lbl"></span>
                            </label>
                        <div class="col-sm-7">
                            <label>
                                <input type="radio" name="status" value="1"
                                    {{ !isset($product_size->status) || $product_size->status == '1' ? 'checked' : '' }}> Yes
                            </label>
                            <label style="margin-left: 10px;">
                                <input type="radio" name="status" value="0"
                                    {{ isset($product_size->status) && $product_size->status == '0' ? 'checked' : '' }}> No
                            </label>
                        </div>

                        </div>

                    <div class="row">
                        <div class="col">
                        <div class="text-center pt-5">
                                            <button type="submit" class="btn btn-primary">Save</button>

                                        <a href="{{ route('admin.subject.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                                        </div>
                        </div>
                    </div>
          </div>
          </div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
