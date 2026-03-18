<div class="form theme-form">
    <!-- General Information Section -->

          <div class="row">



                    <div class="form-group col-md-4">
                    <label>Supplier Name <span class="required" style="color:red;">*</span></label>
                    <input type="text" id="supplier_name" required name="supplier_name" class="form-control" placeholder="Enter Supplier Name" value="{{  isset($supplier->supplier_name) ? $supplier->supplier_name : old('supplier_name') }}" required>
                            @error('supplier_name')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label>Mobile No <span class="required" style="color:red;">*</span></label>
                        <input type="number" id="mobile_no" name="mobile_no" required  class="form-control" placeholder="Enter Mobile No" value="{{  isset($supplier->mobile_no) ? $supplier->mobile_no : old('mobile_no') }}" >
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
                    value="{{ isset($supplier->gstin) ? $supplier->gstin : old('gstin') }}"
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
                                <textarea name="address" id="address"  required placeholder="Address" class="form-control  " ><?php if(isset($supplier)){ echo $supplier->address; } ?></textarea>
                            </div>


<!-- Product Name -->
@php
    $coursearray = isset($supplier['products'])
        ? explode(',', $supplier['products'])
        : [];

    if (!isset($supplier)) {
        if (!in_array(7, $coursearray)) {
            $coursearray[] = 7;
        }
    }

    $products = DB::table('products')  ->get();
@endphp

<div class="form-group col-md-3">
    <label>Product Name <span class="required" style="color:red;">*</span></label>
    <div class="d-flex align-items-center">

        @if(isset($supplier))
            <!-- EDIT MODE: show all products, pre-select saved ones -->
            <select class="select2 form-select" id="products" required name="products[]" multiple="multiple" style="width:100%">
                @foreach($products as $product)
                    <option value="{{ $product->id }}"
                        {{ in_array($product->id, $coursearray) ? 'selected' : '' }}>
                        {{ $product->product_name }}
                    </option>
                @endforeach
            </select>

        @else
            <!-- ADD MODE: ID 7 selected automatically -->
            <select class="select2 form-select" id="products" required name="products[]" multiple="multiple" required style="width:100%">
                @foreach($products as $product)
                    <option value="{{ $product->id }}"
                        {{ in_array($product->id, $coursearray) ? 'selected' : '' }}>
                        {{ $product->product_name }}
                    </option>
                @endforeach
            </select>
        @endif

    </div>
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
