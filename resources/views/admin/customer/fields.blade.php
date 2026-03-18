<div class="form theme-form">
    <!-- General Information Section -->
     <div class="row">
         <div class="col-sm-4">
     <div class="mb-3">
                <label>Customer Name<span> *</span></label>
                <input class="form-control required" type="text" name="customer_name" value="{{ isset($customer->customer_name) ? $customer->customer_name : old('customer_name') }}" placeholder="Enter Customer Name"  oninput="this.value = this.value.toUpperCase();">
                @error('customer_name')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            </div>
            <div class="col-sm-4">
            <div class="mb-3">
                <label>Company Name<span> </span></label>
                <input class="form-control" type="text" name="company_name" value="{{ isset($customer->company_name) ? $customer->company_name : old('company_name') }}" placeholder="Enter Company Name"  oninput="this.value = this.value.toUpperCase();">
                @error('company_name')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            </div>
             <div class="col-sm-4">
        <div class="mb-3">
            <label>Mobile Number<span> *</span></label>
            <input id="mobile_no" class="form-control required" type="text" name="mobile_no" value="{{ isset($customer->mobile_no) ? $customer->mobile_no : old('mobile_no') }}" placeholder="Enter Mobile Number">
            <span id="mobile_error" class="text-danger" style="display:none;"></span>
        </div>
    </div>
     </div>
     <div class="row">
     <div class="col-sm-4">
        <div class="mb-3">
            <label>WhatsApp Number<span> *</span></label>
            <input id="wp_number" class="form-control required" type="text" name="wp_number" value="{{ isset($customer->wp_number) ? $customer->wp_number : old('wp_number') }}" placeholder="Enter Mobile Number">
            <span id="mobile_error" class="text-danger" ></span>
        </div>
    </div>



     <div class="col-sm-4">
    <div class="mb-3">
        <label>Vendor<span> </span></label>
        <select class="select" id="vendor" name="vendor" data-placeholder="Select Vendor" style="height: 37px; width:100%;" required>
            <option value="" disabled {{ old('vendor', isset($customer->vendor) ? $customer->vendor : '') == '' ? 'selected' : '' }}required>Select Vendor</option>
            <option value="WholeSaler" {{ old('vendor', isset($customer->vendor) ? $customer->vendor : '') == 'WholeSaler' ? 'selected' : '' }}required>Wholesaler</option>
            <option value="Retailer" {{ old('vendor', isset($customer->vendor) ? $customer->vendor : '') == 'Retailer' ? 'selected' : '' }}required>Retailer</option>
        </select>
        @error('vendor')
            <span class="text-danger">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

        <div class="col-sm-4">
        <div class="mb-3">
                <label>Email<span> </span></label>
                <input class="form-control" type="text" name="email_id" value="{{ isset($customer->email_id) ? $customer->email_id : old('email_id') }}" placeholder="Enter Your Email">
                @error('email_id')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
   <div class="col-sm-4">
            <div class="mb-3">
                <label>Address 1<span></span></label>
                <textarea class="form-control" name="address" placeholder="Enter Your Address 1" required oninput="this.value = this.value.toUpperCase();">{{ isset($customer->address) ? $customer->address : old('address') }}</textarea>
                @error('address')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-sm-4">
            <div class="mb-3">
                <label>Address 2 <span></span></label>
                <textarea class="form-control" name="address1" placeholder="Enter Your Address 2"  oninput="this.value = this.value.toUpperCase();">{{ isset($customer->address1) ? $customer->address1 : old('address1') }}</textarea>
                @error('address1')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="col-sm-4">
            <div class="mb-3">
                <label>Address 3<span></span></label>
                <textarea class="form-control" name="address2" placeholder="Enter Your Address 3"  oninput="this.value = this.value.toUpperCase();">{{ isset($customer->address2) ? $customer->address2 : old('address2') }}</textarea>
                @error('address2')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>


        <div class="col-sm-4">
            <input type="hidden" name="country_id" id="country_id" value="101">
            <div class="mb-3">
                <label>State<span></span></label>
                <select class="form-select" name="state_id" id="state_id"required>
                    <option value="" selected disabled hidden>Select State</option>
                    @foreach ($states as $key => $state)
                        <option value="{{ $key }}"
                            @if ((isset($customer->id) && old('state_id', $customer->state_id) == $key) || (!isset($customer->id) && $key == 15))
                                selected
                            @endif>
                            {{ $state }}
                        </option>
                    @endforeach
                </select>
            </div>
            </div>
            <div class="col-sm-4">
                <div class="mb-3">
                    <label for="district_id">District<span></span></label>
                    <input type="text" class="form-control" name="district_id"  value="{{ isset($customer->district_id) ? $customer->district_id : old('district_id') }}" id="district_id" placeholder="Enter District"  oninput="this.value = this.value.toUpperCase();">
                </div>
            </div>



            <div class="col-sm-4">
                <div class="mb-3">
                    <label for="city_name">City Name<span></span></label>
                    <input type="text" class="form-control" name="city_name" value="{{ isset($customer->city_name) ? $customer->city_name : old('city_name') }}" id="city_name" placeholder="Enter City Name"  oninput="this.value = this.value.toUpperCase();">
                </div>
            </div>
        <div class="col-sm-4">
        <div class="mb-3">
                <label>Pin Code<span></span></label>
                <input class="form-control" type="text" name="pin_code" value="{{ isset($customer->pin_code) ? $customer->pin_code : old('pin_code') }}" placeholder="Enter Pin Code">
                @error('pin_code')
                    <span class="text-danger">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>








        </div>

    <div class="row">
        <div class="col">
        <div class="text-center pt-5">
                            <button type="submit" class="btn btn-primary">Save</button>

                        <a href="{{ route('admin.customer.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
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
    // $(document).ready(function () {
    //     $('#district_id').change(function () {
    //         var districtId = $(this).val(); // Get the selected district ID
    //         var url = "{{ route('admin.city.get-by-district') }}";

    //         if (districtId) {
    //             $.ajax({
    //                 url: url,
    //                 type: "GET",
    //                 data: { district_id: districtId }, // Pass district_id as data
    //                 success: function (data) {
    //                     $('#city_name').empty(); // Clear the city dropdown
    //                     $('#city_name').append('<option value="" selected disabled hidden >Select City Name</option>');

    //                     // Populate city dropdown with new options
    //                     $.each(data, function (key, value) {
    //                         $('#city_name').append('<option value="' + key + '">' + value + '</option>');
    //                     });
    //                 },
    //                 error: function (xhr) {
    //                     console.error(xhr.responseText);
    //                     alert('Unable to fetch cities. Please try again.');
    //                 }
    //             });
    //         } else {
    //             $('#city_name').empty(); // Clear the city dropdown
    //             $('#city_name').append('<option value="" selected disabled hidden>Select City Name</option>');
    //         }
    //     });
    // });
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script type="text/javascript">
$(document).ready(function () {
    $('#mobile_no').on('blur', function () {
        var mobile_no = $(this).val().replace(/\s+/g, ''); // Trim spaces

        // Allow only numbers
        mobile_no = mobile_no.replace(/\D/g, '');

        // Ensure length is exactly 10 digits
        if (mobile_no.length > 10) {
            mobile_no = mobile_no.substring(0, 10);
        }

        $(this).val(mobile_no); // Set the cleaned value

        if (mobile_no.length < 10 && mobile_no.length > 0) {
            alert('Please input a valid 10-digit number.');
        }

        if (mobile_no.length === 10) {
            // Trigger AJAX request only if the number is exactly 10 digits
            $.ajax({
                url: '{{ route('admin.check.mobile', '') }}/' + mobile_no,
                method: 'GET',
                success: function (response) {
                    if (response.exists) {
                        $('#mobile_error').text('This mobile number is already taken.').show();
                    } else {
                        $('#mobile_error').hide();
                    }
                }
            });
        } else {
            $('#mobile_error').hide();
        }
    });
});



$(document).ready(function () {
    $('#wp_number').on('blur', function () {
        var wp_number = $(this).val().replace(/\s+/g, ''); // Trim spaces

        // Allow only numbers
        wp_number = wp_number.replace(/\D/g, '');

        // Ensure length is exactly 10 digits
        if (wp_number.length > 10) {
            wp_number = wp_number.substring(0, 10);
        }

        $(this).val(wp_number); // Set the cleaned value

        if (wp_number.length < 10 && wp_number.length > 0) {
            alert('Please input a valid 10-digit number.');
        }


    });
});

</script>



