<div class="form theme-form">
@php
    // Retrieve the filter values
    $fromYear = request()->get('from_year');
    $toYear = request()->get('to_year');

    // Get the current year for 'To Year' dropdown
    $currentYear = date('Y');

    // Query the purchase_product table
    $query = DB::table('purchase_product')
        ->select('services', DB::raw('COUNT(id) as product_count'), 'size')
        ->where('complete_flag', 0);

    // Apply the date filter if both years are provided
    if ($fromYear && $toYear) {
        $startDate = $fromYear . '-06-01'; // June 1st of the "From Year"
        $endDate = $toYear . '-05-31'; // May 31st of the "To Year"
        $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Group and fetch records
    $records = $query->groupBy('services', 'size')->get();

    // Generate the years dynamically (from 2020 to current year)
    $years = range(2020, $currentYear);
@endphp

<form method="GET" action="{{ url()->current() }}">
    <div class="row mb-3">
        <!-- From Year Dropdown -->
        <div class="col-md-3">
            <label for="from_year">From Year:</label>
            <select name="from_year" id="from_year" class="form-control">
                <option value="">Select Year</option>
                @foreach ($years as $year)
                    <option value="{{ $year }}" {{ $fromYear == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- To Year Dropdown -->
        <div class="col-md-3">
            <label for="to_year">To Year:</label>
            <select name="to_year" id="to_year" class="form-control">
                <option value="">Select Year</option>
                @foreach ($years as $year)
                    <option value="{{ $year }}" {{ $toYear == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>

   
    </div>
</form>



          <div class="row">
          <table id="product" class="table table-bordered" style="width:100%; margin-right: 60px; background-color:white;">
    <tr>
        <th>Product Name</th>
        <th>Product Size</th>
        <th>Purchase Price</th>
    </tr>
    
    @php
    // Fetching all products from the products table
    $product_bulks = DB::table('products')->get();
    @endphp

    @foreach ($product_bulks as $product)
        <tr>
            <!-- Product Name with hidden input for services -->
            <td>
                <input type="text" class="form-control" value="{{ $product->product_name }}" disabled>
                <input type="hidden" class="form-control" value="{{ $product->id }}" name="services[]">
            </td>

            <!-- Product Size and Purchase Price -->
            <td>
                @php
                $product_details = DB::table('product_details')->where('parentID', $product->id)->get();
                @endphp

                @foreach ($product_details as $product_detail)
                    <!-- Product Size and Purchase Price grouped under the same product ID -->
                    <input type="text" class="form-control" name="product[{{ $product->id }}][size][]" style="margin-bottom: 10px;" value="{{ $product_detail->product_size }}" readonly>
                @endforeach
            </td>

            <td>
                @foreach ($product_details as $product_detail)
                    <!-- Purchase Price input field grouped under the same product ID -->
                    <input type="text" class="form-control" name="product[{{ $product->id }}][rate][]" style="margin-bottom: 10px;" value="{{ $product_detail->purch_price }}">
                @endforeach
            </td>
        </tr>
    @endforeach
</table>




     
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

</script>
