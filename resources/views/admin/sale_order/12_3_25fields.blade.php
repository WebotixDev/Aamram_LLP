<div class="form theme-form">
    <!-- General Information Section -->
    <div class="row">
   @php


$count = DB::table('sale_orderdetails')->count();

$nextBillNo = $count > 0 ? $count + 1 : 1;

@endphp
    <div class="form-group col-md-2">
        <label>Order No <span class="required" style="color:red;">*</span></label>

                <input type="text" id="Invoicenumber" name="Invoicenumber" value="<?php echo isset($saleOrders) ? $saleOrders['Invoicenumber'] : $nextBillNo; ?>" class="required form-control" readonly>
            </div>



            <div class="form-group col-md-2">
    <label>Order Date <span class="required" style="color:red;">*</span></label>

    <input class="datepicker-here form-control" id="billdate" name="billdate" type="text"
        value="{{ isset($saleOrders->billdate) ? \Carbon\Carbon::parse($saleOrders->billdate)->format('d-m-Y') : (old('billdate') ?? date('d-m-Y')) }}"
        data-language="en"
        placeholder="Enter Date"
        data-date-format="dd-mm-yyyy">

    @error('billdate')
        <span class="text-danger"><strong>{{ $message }}</strong></span>
    @enderror
</div>


<div class="form-group col-md-4">
    <label>Customer Name <span class="required" style="color:red;">*</span></label>
    <div class="d-flex align-items-center">
        <select class="form-select select2" id="customer_name_sale" name="customer_name_sale" data-placeholder="Select Customer" required>
            <option value="">Select Customer</option>
            @php
                $customers = DB::table('customers')->get();
            @endphp
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}"
                    @if(isset($saleOrders) && $saleOrders->customer_name == $customer->id) selected @endif>
               {{ $customer->customer_name . '-' . $customer->mobile_no  }}
                </option>
            @endforeach
        </select>
        <button type="button" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#addCustomerModal">Add</button>
    </div>
</div>


<div class="form-group col-md-2">
    <label>Select Dispatch <span class="required" style="color:red;">*</span></label>
    <select class="form-select select2" id="dispatch" name="dispatch" data-placeholder="Select Dispatch">
        <option value="yes" @if(isset($saleOrders->dispatch) && $saleOrders->dispatch == 'yes') selected @endif>Yes</option>
        <option value="no" @if(isset($saleOrders->dispatch) && $saleOrders->dispatch == 'no') selected @endif>No</option>
    </select>
</div>




        <div class="row pt-5">
<!--

        <div class="form-group col-md-2">
            <div class="form-floating form-floating-outline">
                <select class="form-select select2" id="types1" name="types1" data-placeholder="Select Types">
                    <option></option>
                    <option value="satara">Satara</option>
                    <option value="pune">Pune</option>
                </select>
                <label>Location <span class="required" style="color:red;">*</span></label>
            </div>
        </div> -->


        <div class="form-group col-md-3">

                                    <label>GST <span class="required_lbl">*</span></label><br>

                                    <input type="radio" name='gst' id="gst" onclick="checkgst(this.value);"
                                        value="Maharashtra" checked>&nbsp;&nbsp;Maharashtra
                                    <input type="radio" name='gst' id="gst" onclick="checkgst(this.value);"value="Other-States">&nbsp;&nbsp;
                                    Other-States


                                </div>


        </div>
    <br><br>

    <div id="proformatable">
        <div class="row">
            <div class="col-md-12">
                <table id="saleTable" style="width:100%; font-size:12px" class="table table-bordered">
                    <thead>
                        <tr>

                            <th>PRODUCTS<span class="required" style="color:red;">*</span></th>
                            <th>SIZE<span class="required" style="color:red;">*</span></th>
                            <th>Stage<span class="required" style="color:red;">*</span></th>
                            <th>Stock <span class="required" style="color:red;">*</span></th>
                            <th>RATE <span class="required" style="color:red;">*</span></th>
                            <th class="qty-container">Order Qty <span class="required" style="color:red;"></span></th>
                            <th class="quantity-container">Dispatch Qty <span class="required" style="color:red;"></span></th>
                            <th>GST <span class="required" style="color:red;">*</span></th>
                            <th>Trans Cost <span class="required" style="color:red;">*</span></th>
                            <th>Amt <span class="required" style="color:red;">*</span></th>

                            <th width='1%'>Action</th>
                        </tr>
                    </thead>
                    <tbody id="p_scents">
                            <?php
                            $i=0;
                            if (isset($sale_order_list)) {

                                // echo "<pre>";
                                // print_r($sale_order_list);

                                $Date = date('Y-m-d');

                            foreach ($sale_order_list as $sale_order_list1) {
                            $selectedProduct = $sale_order_list1['services'] ?? null; // Assuming product_id is the key for the selected
                            $selectedSize = $sale_order_list1['size'] ?? null; // Assuming size is the key for the selected size
                            $selectedStage = $sale_order_list1['stage'] ?? null; // Assuming stage is the key for the selected stage
                            $stock = \App\Helpers\Helpers::getstockbatch($selectedProduct,$selectedSize,$selectedStage,$Date);
                            $stocknew=$stock+$sale_order_list1['qty'];
                            $i++;

                            ?>
                            <tr id="row_<?= $i ?>">
                            <!-- Product Dropdown -->
                            <td>
                            <select class="select2" id="services<?= $i ?>" name="services<?= $i ?>"
                            data-placeholder="Products" style="height: 37px; width:100%;"
                            onchange="getprice(this.id);getstock(this.id);" required>
                            <option value="">Select Product</option>
                            @php
                            $pro_masters = DB::table('products')->get();
                            @endphp
                            @foreach ($pro_masters as $pro_master)
                            <option value="{{ $pro_master->id }}"
                            {{ $pro_master->id == $sale_order_list1['services'] ? 'selected' : '' }}>
                            {{ $pro_master->product_name }}
                            </option>
                            @endforeach
                            </select>
                            </td>

                            <!-- Size Dropdown -->
                            <td>
                            <select class="select2" id="size<?= $i ?>" name="size<?= $i ?>" data-placeholder="Select Size" style="height: 37px; width:100%;" required>
                            <option value="">Select Size</option>
                            @php
                            $sizes = DB::table('product_details')->get();
                            @endphp
                            @foreach ($sizes as $size)
                            <option value="{{ $size->id }}" {{ $size->id == $sale_order_list1['size'] ? 'selected' : '' }}>
                            {{ $size->product_size }}
                            </option>
                            @endforeach
                            </select>

                            </td>

                            <!-- Stage Dropdown -->
                            <td>
                            <select class="select2" id="stage<?= $i ?>" name="stage<?= $i ?>" data-placeholder="Select Stage" style="height: 37px; width:100%;">
                            <option>Select Stage</option>
                            <option value="Raw" @if($sale_order_list1->stage == 'Raw') selected @endif>Raw</option>
                            <option value="Semi Ripe" @if($sale_order_list1->stage == 'Semi Ripe') selected @endif>Semi Ripe</option>
                            <option value="Ripe" @if($sale_order_list1->stage == 'Ripe') selected @endif>Ripe</option>
                        </select>
                            </td>

                            <!-- Stock Field -->
                            <td>
                            <input type="text" id="stock<?= $i ?>" class="form-control"
                            value="{{ $stocknew ?? '' }}" readonly>
                            </td>

                            <!-- Rate Field -->
                            <td>
                            <input type="text" id="rate<?= $i ?>" name="rate<?= $i ?>"
                            class="form-control" value="{{ $sale_order_list1['rate'] ?? '' }}" required readonly>
                            </td>

                            <!-- Quantity Field -->
                            <td>
                            <input type="text" id="qty<?= $i ?>" name="qty<?= $i ?>"
                            class="form-control" value="{{ $sale_order_list1['qty'] ?? '' }}"
                            onkeyup="Gettotal();validateStockVsQuantity(<?= $i ?>);" required>
                            </td>
                            <td>
                            <input type="text" id="Quantity<?= $i ?>" name="Quantity<?= $i ?>"
                            class="form-control" value="{{ $sale_order_list1['Quantity'] ?? '' }}"
                            onkeyup="Gettotal();" required>
                            </td>

                            <!-- GST Percentage -->
                            <td>
                            <input type="text" id="gstper<?= $i ?>" name="gstper<?= $i ?>"
                            class="form-control" value="{{ $sale_order_list1['gstper'] ?? '' }}" readonly>
                            </td>

                            <td>
                            <input type="text" id="transper<?= $i ?>" name="transper<?= $i ?>"
                            class="form-control" onkeyup="Gettotal();" value="{{ $sale_order_list1['transper'] ?? '' }}" >
                            </td>

                            <!-- Amount -->
                            <td>
                            <input type="text" id="amount<?= $i ?>" name="amount<?= $i ?>"
                            class="form-control" value="{{ $sale_order_list1['amount'] ?? '' }}" readonly>
                            </td>

                            <!-- Delete Button -->
                            <td>
                            <button type="button" class="btn btn-danger btn-sm"
                            onclick="deleteRow(<?= $i ?>)">Delete</button>
                            </td>

                            <!-- Hidden Fields -->
                            <input type="hidden" id="CGSTper<?= $i ?>" value="{{ $sale_order_list1['CGSTper'] ?? '' }}">
                            <input type="hidden" id="SGSTper<?= $i ?>" value="{{ $sale_order_list1['SGSTper'] ?? '' }}">
                            <input type="hidden" id="IGSTper<?= $i ?>" value="{{ $sale_order_list1['IGSTper'] ?? '' }}">
                            <!-- <input type="hidden" name="cnt" id="cnt" value="<?= $i ?>"> -->
                            </tr>

                            <?php
                            }
                            } else{
                            ?>
                   <?php $i = 1; ?>
                        <tr id="row_<?= $i ?>">

                            <td>
                                <select class="select2" id="services<?= $i ?>" name="services<?= $i ?>" data-placeholder="Products" style="height: 37px; width:100%;" onchange="getprice(this.id);getstock(this.id);" required>
                                    <option value=""></option>
                                    @php
                                        $pro_masters = DB::table('products')->get();
                                    @endphp
                                    @foreach ($pro_masters as $pro_master)
                                        <option value="{{ $pro_master->id }}">{{ $pro_master->product_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="select2 required" id="size<?= $i ?>" name="size<?= $i ?>" data-placeholder="Select Size" style="height: 37px; width:100%;"   required>
                                    <option value=""></option>
                                </select>
                            </td>
                            <td>
                                <select class="select2" id="stage<?= $i ?>" name="stage<?= $i ?>" data-placeholder="Select Stage" style="height: 37px; width:100%;" required>
                            <option>Select Stage</option>
                            <option value="Raw">Raw</option>
                            <option value="Semi Ripe">Semi Ripe</option>
                            <option value="Ripe">Ripe</option>
                                </select>
                            </td>

                            <td>
                                <input type="text" id="stock<?= $i ?>"  class="form-control" value="" readonly>
                            </td>

                            <td><input name="rate<?= $i ?>" id="rate<?= $i ?>" class="form-control input-sm" type="text" data-placeholder="Rate" style="width:100%;" value="" required readonly></td>

                            <td class="qty-container">
                            <input type="text" id="qty<?= $i ?>" name="qty<?= $i ?>" onkeyup="Gettotal();validateStockVsQuantity(1);" class="form-control" required>
                            </td>

                            <td class="quantity-container">
                            <input type="text" id="quantity<?= $i ?>" name="quantity<?= $i ?>" onkeyup="Gettotal();" class="form-control" required>
                            </td>

                            <td><input name="gstper<?= $i ?>"   id="gstper<?= $i ?>" class="form-control input-sm" onkeyup="Gettotal();" type="text" style="width:100%;" value="" required readonly></td>

                            <td>
                            <input type="text" id="transper<?= $i ?>" name="transper<?= $i ?>"
                            class="form-control" onkeyup="Gettotal();" value="{{ $sale_order_list1['transper'] ?? '' }}" >
                            </td>


                            <td><input name="amount<?= $i ?>" readonly id="amount<?= $i ?>" class="form-control input-sm" type="text" data-placeholder="Amount" style="width:100%;" value=""></td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(1)">Delete</button>
                            </td>

                            <input name="CGSTper<?= $i ?>" readonly id="CGSTper<?= $i ?>" class="form-control input-sm" type="hidden" data-placeholder="CGST" style="width:100%;" value="">
                            <input name="SGSTper<?= $i ?>" readonly id="SGSTper<?= $i ?>" class="form-control input-sm" type="hidden" data-placeholder="CGST" style="width:100%;" value="">
                            <input name="IGSTper<?= $i ?>" readonly id="IGSTper<?= $i ?>" class="form-control input-sm" type="hidden" data-placeholder="CGST" style="width:100%;" value="">

                        </tr>

                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-center">
                            <input type="hidden" id="cnt" name="cnt" value="<?= $i ?>">
                            <button type="button" class="btn btn-primary" onclick="addRow()">Add Row</button>
                            </td>
                            <td>Total</td>
                            <td colspan='3'><input type="text" readonly id="totalproamt" name="totalproamt"  value="<?php echo isset($saleOrders) ? $saleOrders['Tamount'] : '0'; ?>"  class="form-control"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
                                             <br><br>
                                                <div class="row mt-5">

                                            <div class="col-md-3">

                                            <label>Discount in (%) <span class="required"
                                            style="color:red;">*</span></label>
                                                <input name="discount_per" id="discount_per"
                                                    onkeyup="calcDiscAmt();" <?php //echo $disabled; ?>
                                                    class="form-control input-sm " type="text"
                                                    data-placeholder="Discount" style="width:100%;"
                                                    value="<?php echo isset($saleOrders) ? $saleOrders['discount_per'] : '0'; ?>"
                                                    required>

                                            </div>

                                            <div class="col-md-3">

                                            <label>Discount in (<i class="fa fa-rupee"></i>) <span
                                            class="required" style="color:red;">*</span> </label>
                                                <input name="discount_rupee" id="discount_rupee"
                                                    onkeyup="calcDiscPer();" <?php //echo $disabled; ?>
                                                    class="form-control input-sm " type="text"
                                                    data-placeholder="Discount" style="width:100%;"
                                                    value="<?php echo isset($saleOrders) ? $saleOrders['discount_rupee'] : '0'; ?>"
                                                    required>

                                            </div>


                                            <div class="col-md-3">
                                            <label>Sub Total (AFT Dis.)</label>

                                                <input name="subtotal" readonly id="subtotal"
                                                    class="form-control input-sm " type="text"
                                                    data-placeholder="Sub-Total" style="width:100%;"
                                                    value="<?php echo isset($saleOrders) ? $saleOrders['subtotal'] : '0'; ?>">


                                            </div>

                                            <!-- <div class="col-md-2">
                                            <label>Transportation Cost<span class="required"
                                                        style="color:red;">*</span></label>
                                                <input name="trans_cost" id="trans_cost"
                                                    onkeyup="calctransporAmt();" class="form-control input-sm "
                                                    type="text" data-placeholder="Sub-Total" style="width:100%;"
                                                    value="<?php echo isset($saleOrders) ? $saleOrders['trans_cost'] : '0'; ?>"
                                                    required>

                                            </div> -->


													<!-- <div class="col-md-2">

                                                    <label>Transportation GST (%) <span class="required"
																style="color:red;">*</span></label>
															<input name="trans_in_per" id="trans_in_per"
																onkeyup="calctransporAmt();" class="form-control input-sm "
																type="text" data-placeholder="Sub-Total" style="width:100%;"
                                                                value="<?php //echo isset($saleOrders) ? $saleOrders['trans_in_per'] : '0'; ?>"
																required>


													</div> -->

													<!-- <div class="col-md-2">
                                                    <label>Transportation GST(<i class="fa fa-rupee"></i>)
														</label>
														<input name="trans_in_per_rs" readonly id="trans_in_per_rs"
															onkeyup="calctransporAmt();" class="form-control input-sm "
															type="text"
															data-placeholder="Transportation Cost in (<i class='fa fa-rupee'></i>)"
															style="width:100%;"
															value="<?php //echo isset($saleOrders) ? $saleOrders['trans_in_per_rs'] : '0'; ?>">

													</div>
 -->


<br>

                                                	<div class="col-md-3">
                                                    <label> Other Charges <span class="required"
																style="color:red;">*</span> </label>
														<input name="other_charges" onkeyup="calcgstAmt();"
															id="other_charges"
															class="form-control input-sm " type="text"
															data-placeholder="Discount" style="width:100%;"
															value="<?php echo isset($saleOrders) ? $saleOrders['other_charges'] : '0'; ?>"
															required>

													</div>
                                                    </div>
                                                    <br>
 <br>                                       <div class="row">
                                            <div class="col-md-3" id="CGST_div" style="">
                                            <label> CGST </label>
														<input name="CGST" readonly id="CGST"
															class="form-control input-sm " type="text"
															data-placeholder="CGST" style="width:100%;"
															value="<?php echo isset($saleOrders) ? $saleOrders['CGST'] : '0'; ?>">


													</div>

													<div class="col-md-3" id="SGST_div" style="">

                                                    <label>SGST</label>

														<input name="SGST" readonly id="SGST"
															class="form-control input-sm " type="text"
															data-placeholder="SGST" style="width:100%;"
															value="<?php echo isset($saleOrders) ? $saleOrders['SGST'] : '0'; ?>">

													</div>

													<div class="col-md-2" id="IGST_div" style="display:none">
                                                    <label>IGST</label>


														<input name="IGST" readonly id="IGST"
															class="form-control input-sm " type="text"
															data-placeholder="IGST" style="width:100%;"
															value="<?php echo isset($saleOrders) ? $saleOrders['IGST'] : '0'; ?>">


													</div>



                                            <div class="col-md-3">

                                                    <label>Total Amount</label>
                                                        <input name="Tamount" readonly id="Tamount"
                                                            class="form-control input-sm " type="text"
                                                            data-placeholder="Tamount" style="width:100%;"
                                                            value="<?php echo isset($saleOrders) ? $saleOrders['Tamount'] : '0'; ?>">


                                                    </div>

                                                    </div>

                                            </div>
                                            <br>

<br>
										<h2 style="font-size: 22px;margin-bottom: 10px;">Payment Details </h2>

											<table id="pmttble" class="table"
												style="">
												<tbody>
													<tr>
														<!--th style="width: 18%;">Next Payment Date</th-->
														<th style="width: 15%;">Payment Method </th>
														<th style="width: 15%;">Amount</th>
														<th style="width: 17%;">Note</th>
													</tr>
													<tr>

														<td>
                                        <div class="col-md-12 col-sm-4 col-xs-12">
                                        <select class="select2" tabindex="-1" data-placeholder="Select Payment Method" style="width:100%" name="mode" id="mode">
                                    <option></option>
                                    <option value="cash" @if(isset($saleOrders->mode) && $saleOrders->mode == 'cash') selected @endif>Cash</option>
                                    <option value="cheque" @if(isset($saleOrders->mode) && $saleOrders->mode == 'cheque') selected @endif>Cheque</option>
                                    <option value="epayment" @if(isset($saleOrders->mode) && $saleOrders->mode == 'epayment') selected @endif>E-Payment</option>
                                    <option value="credit" @if(isset($saleOrders->mode) && $saleOrders->mode == 'credit') selected @endif>Credit</option>
                                </select>

                                        </div>

														</td>


														<td>
                                                        <input type="text" name='amt_pay' class=" form-control col-md-7 col-xs-12"  value="<?php echo isset($saleOrders) ? $saleOrders['amt_pay'] : '0'; ?>" id="amt_pay">

														</td>
														<td>
															<input type="text" tabindex="-1" name='narration' value="<?php echo isset($saleOrders) ? $saleOrders['narration'] : '0'; ?>"
																class=" form-control col-md-7 col-xs-12" id="narration">
														</td>
													</tr>


												</tbody>
											</table>

                                    <br>
                                    <div class="row">
                                    <div class="col">
                                    <div class="text-center">
                                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                                    <a href="{{ route('admin.sale_order.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                                    </div>
                                    </div>
                                    </div>
                                    </div>


                                    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="addCustomerForm">
                                            <div class="mb-3">
                                                <label for="new_customer_name" class="form-label">Customer Name</label>
                                                <input type="text" class="form-control" id="new_customer_name" name="customer_name">
                                            </div>
                                            <div class="mb-3">
                                                <label for="new_customer_number" class="form-label">Customer Number</label>
                                                <input type="text" class="form-control" id="new_customer_number" name="customer_number">
                                            </div>
                                            <div class="mb-3">
                                                <label for="new_customer_type" class="form-label">Vendor</label>
                                                <select class="form-select" id="new_customer_type" name="customer_type">
                                                    <option value="" disabled selected>Select Type</option>
                                                    <option value="wholesaler">Wholesaler</option>
                                                    <option value="retailer">Retailer</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="state_id" class="form-label">State</label>
                                                <select class="form-select" name="state_id" id="state_id">
                                                    <option value="" selected disabled hidden>Select State</option>
                                                    @foreach ($states as $key => $state)
                                                        <option value="{{ $key }}">{{ $state }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="district_id" class="form-label">District</label>
                                                <select class="form-select" name="district_id" id="district_id">
                                                    <option value="" selected disabled hidden>Select District</option>
                                                    @foreach ($districts as $key => $district)
                                                        <option value="{{ $key }}">{{ $district }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="city_name" class="form-label">City Name</label>
                                                <select class="form-select" name="city_name" id="city_name">
                                                    <option value="" selected disabled hidden>Select City Name</option>
                                                </select>
                                            </div>
                                        </form>
                                        <div class="text-center">
                                            <button type="button" class="btn btn-success" onclick="submitCustomerForm()">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>




<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- <script src="{{ asset('assets/js/select2/select2-custom.js') }}"></script> -->
    <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.en.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.custom.js') }}"></script>
    <script src="{{ asset('assets/js/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('assets/js/dropzone/dropzone-script.js') }}"></script>

    <script>
      function addRow() {
                    const tableBody = document.getElementById('saleTable').querySelector('tbody');
                    const rowCount = tableBody.rows.length + 1; // Calculate row count dynamically

                    const newRow = `

                    <tr id="row_${rowCount}">

                <td>
                    <select class="select2" id="services${rowCount}" name="services${rowCount}"
                            data-placeholder="Select Products" style="height: 37px; width:100%;"
                            onchange="getprice(this.id);getstock(this.id);" required>
                        <option value=""></option>
                        @php
                         $pro_masters = DB::table('products')->get();
                        @endphp
                        @foreach ($pro_masters as $pro_master)
                            <option value="{{ $pro_master->id }}">{{ $pro_master->product_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select class="select2" id="size${rowCount}" name="size${rowCount}"
                            data-placeholder="Select Size" style="height: 37px; width:100%;" required>
                        <option value=""></option>
                    </select>
                </td>
                <td>
                    <select class="select2" id="stage${rowCount}" name="stage${rowCount}"
                            data-placeholder="Select Stage" style="height: 37px; width:100%;" required>
                        <option>Select Stage</option>
                               <option value="Raw">Raw</option>
                        <option value="Semi Ripe">Semi Ripe</option>
                        <option value="Ripe">Ripe</option>
                    </select>
                </td>
                <td>
                    <input type="text" id="stock${rowCount}"  class="form-control" required readonly>
                </td>
                <td>
                    <input type="text" id="rate${rowCount}" name="rate${rowCount}" class="form-control smallinput" required readonly>
                </td>
                <td class="qty-container" >
                    <input type="number" id="qty${rowCount}" name="qty${rowCount}" class="form-control smallinput"
                    onkeyup="Gettotal();validateStockVsQuantity(${rowCount});"   required>
                </td>
                <td class="quantity-container" >
                    <input type="number" id="quantity${rowCount}" name="quantity${rowCount}" class="form-control smallinput" onkeyup="Gettotal();"  >
                </td>

                <td>
                    <input type="number" id="gstper${rowCount}" name="gstper${rowCount}" onkeyup="Gettotal();" class="form-control smallinput"
                            required readonly>
                </td>
                 <td >
                    <input type="number" id="transper${rowCount}" name="transper${rowCount}" class="form-control smallinput" onkeyup="Gettotal();"  >
                </td>
                <td>
                    <input type="text" id="amount${rowCount}" name="amount${rowCount}" class="form-control smallinput" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(${rowCount})">Delete</button>
                </td>

            <input name="CGSTper${rowCount}" readonly id="CGSTper${rowCount}" class="form-control input-sm" type="hidden" data-placeholder="CGST" style="width:100%;" value="">
            <input name="SGSTper${rowCount}" readonly id="SGSTper${rowCount}" class="form-control input-sm" type="hidden" data-placeholder="CGST" style="width:100%;" value="">
            <input name="IGSTper${rowCount}" readonly id="IGSTper${rowCount}" class="form-control input-sm" type="hidden" data-placeholder="CGST" style="width:100%;" value=""


            </tr>`;

            tableBody.insertAdjacentHTML('beforeend', newRow);

            // Update the hidden input field with the latest row count
            document.getElementById('cnt').value = rowCount;


            $(".select2").select2();
    }



    // Function to delete a row
    function deleteRow(rowId) {
                    const row = document.getElementById(`row_${rowId}`);
                    if (row) {
                        row.remove();

                        // Update the hidden input field with the latest row count
                        const tableBody = document.getElementById('saleTable').querySelector('tbody');
                        document.getElementById('cnt').value = tableBody.rows.length;
                          // Optionally, you can trigger the `Gettotal()` function if needed
                Gettotal();
                    }
                }



    $(document).ready(function () {
        $(".select2").select2();
    });


	function checkgst(gsttype) {

if (gsttype == 'Maharashtra') {

    $('#CGST_div').show();
    $('#SGST_div').show();
    $('#IGST_div').hide();
}
else {
    $('#CGST_div').hide();
    $('#SGST_div').hide();
    $('#IGST_div').show();
}


}


function checkDuplicateService(currentId) {
    const currentValue = $(`#${currentId}`).val(); // Get the selected value of the current dropdown
    let isDuplicate = false;

    // Iterate over all dropdowns with the class 'select2'
    $(".select2").each(function () {
        const elementId = $(this).attr('id');
        const elementValue = $(this).val();

        // Check if the value is the same as the current dropdown but not the same dropdown itself
        if (elementId !== currentId && elementValue === currentValue && currentValue !== "") {
            isDuplicate = true;
            return false; // Break out of the loop if a duplicate is found
        }
    });

    if (isDuplicate) {
        alert("This product is already selected. Please choose a different product.");
        $(`#${currentId}`).val(''); // Reset the current dropdown
        $(`#${currentId}`).trigger('change'); // Trigger change event to refresh the dropdown
    }
}


function validateStockVsQuantity(rowId) {
    let stock = parseFloat($(`#stock${rowId}`).val());
    let quantity = parseFloat($(`#qty${rowId}`).val());

    if (quantity > stock) {
        alert("Order quantity cannot exceed available stock.");
        $(`#qty${rowId}`).val(''); // Clear the invalid quantity value
        return false;
    }
    return true;
}

function getprice(id) {
    let rowIndex = id.replace("services", "");
    let services = $("#services" + rowIndex).val();
    let gst = $('input[name=gst]:checked').val();

    $.ajax({
        url: "{{ route('admin.sale_order.get-price') }}",
        type: "GET",
        data: { services, gst, rowIndex, _token: "{{ csrf_token() }}" },
        success: function (response) {
            if (response.status === "success") {
                // Populate the size dropdown
                let sizeOptions = response.data.map(item =>
                    `<option value="${item.id}">${item.product_size}</option>`).join("");
                $("#size" + rowIndex)
                    .html(sizeOptions)
                    .trigger("change");

                // Attach change event to the size dropdown
                $("#size" + rowIndex).off("change").on("change", function () {
                    let size = $(this).val();
                    let stage = $("#stage" + rowIndex).val(); // Get the selected stage
                    getrate(services, size, rowIndex);
                    getstock(services, size, stage, rowIndex); // Update stock
                });

                // Populate stage dropdown with static options
                let stageOptions = `
                    <option value="">Select Stage</option>
                    <option value="Raw">Raw</option>
                    <option value="Semi Ripe">Semi Ripe</option>
                    <option value="Ripe">Ripe</option>
                `;
                $("#stage" + rowIndex)
                    .html(stageOptions)
                    .trigger("change");

                // Attach change event to stage dropdown
                $("#stage" + rowIndex).off("change").on("change", function () {
                    let stage = $(this).val();
                    let size = $("#size" + rowIndex).val(); // Get the selected size
                    getstock(services, size, stage, rowIndex);
                });

                // Set GST and other details
                $("#gstper" + rowIndex).val(response.gst || 0);
                $("#CGSTper" + rowIndex).val(response.pro_data.cgst || 0);
                $("#SGSTper" + rowIndex).val(response.pro_data.sgst || 0);
                $("#IGSTper" + rowIndex).val(response.pro_data.igst || 0);
                $("#quantity" + rowIndex).val(response.quantity || 0);
                $("#qty" + rowIndex).val(response.quantity || 0);
                $("#transper" + rowIndex).val(response.transper || 0);


                // Call getrate for the initially selected size
                let size = $("#size" + rowIndex).val();
                getrate(services, size, rowIndex);
            } else {
                alert("Unable to load Sizes. Please try again.");
            }
        },
        error: function () {
            alert("An error occurred while fetching the price.");
        }
    });
}

function getrate(services, size, rowIndex) {
    $.ajax({
        url: "{{ route('admin.sale_order.get-rate') }}",
        type: "GET",
        data: { services, size, rowIndex, _token: "{{ csrf_token() }}" },
        success: function (response) {
            if (response.status === "success") {
                let rate = response.rate || 0; // Default to 0 if rate is not returned
                $("#rate" + rowIndex).val(rate);
            } else {
                alert("Unable to load the rate. Please try again.");
            }
        },
        error: function () {
            alert("An error occurred while fetching the rate.");
        }
    });
}

function getstock(services, size, stage, rowIndex) {
    $.ajax({
        url: "{{ route('admin.sale_order.get-stock') }}",
        type: "GET",
        data: { services, size, stage, rowIndex, _token: "{{ csrf_token() }}" },
        success: function (response) {
            if (response.status === "success") {
                let stock = response.stock || 0; // Use default value of 0 if stock is not returned
                $("#stock" + rowIndex).val(stock); // Set the stock value in the respective input
            } else {
                alert("Unable to load stock. Please try again.");
            }
        },
        error: function () {
            alert("An error occurred while fetching the stock.");
        }
    });
}



// Function to calculate the total amounts
function Gettotal() {
    var cnt = jQuery("#cnt").val();
    console.log(cnt);
    var totalAmt = 0;
    var subtotal = 0;
    var totcgst = 0;
    var totsgst = 0;
    var totigst = 0;
    var totdisc = parseFloat($("#discount_rupee").val()) || 0;
    var gst = $('input[name=gst]:checked').val();
    let dispatch = $('#dispatch').val(); // Correct dispatch value fetching from dropdown

    for (var i = 1; i <= cnt; i++) {
        var gsttotal = parseFloat(jQuery("#gstper" + i).val()) || 0;
        var cgst = gsttotal / 2;
        var sgst = gsttotal / 2;
        var igst = gsttotal;

        // Determine quantity based on dispatch condition
        var qty = parseFloat(jQuery("#qty" + i).val()) || 0;
        var rate = parseFloat(jQuery("#rate" + i).val()) || 0;
        var transper = parseFloat(jQuery("#transper" + i).val()) || 0;


        console.log(transper);
        // console.log(rate);

        var proamt = qty * rate + transper;
        var discamt = (proamt * parseFloat($("#discount_per").val()) || 0) / 100;
        var netamt = proamt - discamt;

        totalAmt += proamt;
        $("#amount" + i).val(proamt.toFixed(2));

        // GST calculations
        if (gst === 'Maharashtra') {
            totcgst += (netamt * cgst) / 100;
            totsgst += (netamt * sgst) / 100;
        } else {
            totigst += (netamt * igst) / 100;
        }

        subtotal += netamt;
    }

    // Update fields
    jQuery("#totalproamt").val(totalAmt.toFixed(2));
    jQuery("#subtotal").val(subtotal.toFixed(2));
    jQuery("#discount_rupee").val(totdisc.toFixed(2));
    jQuery("#CGST").val(totcgst.toFixed(2));
    jQuery("#SGST").val(totsgst.toFixed(2));
    jQuery("#IGST").val(totigst.toFixed(2));

    calcgstAmt(); // Recalculate final total including GST
}

// Function to calculate transportation amount
function calctransporAmt() {
    var transCost = parseFloat($("#trans_cost").val()) || 0;
    var transInPer = parseFloat($("#trans_in_per").val()) || 0;
    var transAmt = (transCost * transInPer) / 100;
    $("#trans_in_per_rs").val(transAmt.toFixed(2));
    calcgstAmt(); // Recalculate the final total including transportation GST
}

// Function to calculate discount amount
function calcDiscAmt() {
    var discountPercentage = parseFloat($("#discount_per").val()) || 0;
    var subtotal = parseFloat($("#subtotal").val()) || 0;
    var discountAmount = (subtotal * discountPercentage) / 100;
    $("#discount_rupee").val(discountAmount.toFixed(2));
    Gettotal(); // Update total after discount
}

// Function to calculate discount percentage
function calcDiscPer() {
    var discountAmount = parseFloat($("#discount_rupee").val()) || 0;
    var subtotal = parseFloat($("#subtotal").val()) || 0;
    var discountPercentage = (discountAmount / subtotal) * 100;
    $("#discount_per").val(discountPercentage.toFixed(2));
    Gettotal(); // Update total after discount
}

// Function to calculate GST amounts
function calcgstAmt() {
    var subtotal = parseFloat($("#subtotal").val()) || 0;
    var cgst = parseFloat($("#CGST").val()) || 0;
    var sgst = parseFloat($("#SGST").val()) || 0;
    var igst = parseFloat($("#IGST").val()) || 0;
    var transAmt = parseFloat($("#trans_in_per_rs").val()) || 0;
    var otherCharges = parseFloat($("#other_charges").val()) || 0;

    var totalGst = cgst + sgst + igst + transAmt;
    var finalTotal = subtotal + totalGst + otherCharges;

    $("#Tamount").val(finalTotal.toFixed(2));
}

function submitCustomerForm() {
    // Get input values
    let customerName = document.getElementById('new_customer_name').value.trim();
    let customerNumber = document.getElementById('new_customer_number').value.trim();
    let customerType = document.getElementById('new_customer_type').value;
    let stateId = document.getElementById('state_id').value;
    let districtId = document.getElementById('district_id').value;
    let cityName = document.getElementById('city_name').value;

    // Validate inputs
    if (customerName && customerNumber && customerType && stateId && districtId && cityName) {
        $.ajax({
            url: '{{ route("admin.customers.store") }}', // Replace with your store route
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            data: {
                customer_name: customerName,
                customer_number: customerNumber,
                customer_type: customerType,
                state_id: stateId,
                district_id: districtId,
                city_name: cityName
            },
            success: function (response) {
                if (response.success) {
                    alert('Customer added successfully!');
                    // Reset form and hide modal
                    $('#addCustomerForm')[0].reset();
                    $('#addCustomerModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.message || 'Failed to add customer.');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('An error occurred while submitting the form.');
            }
        });
    } else {
        alert('Please fill out all fields before submitting.');
    }
}


$(document).ready(function () {
    $('#dispatch').on('change', function () {
        if ($(this).val() === 'yes') {
            $('.quantity-container').show();
        } else {
            $('.quantity-container').hide();

        }
    });
});


</script>


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
    $(document).ready(function () {
        $('#district_id').change(function () {
            var districtId = $(this).val(); // Get the selected district ID
            var url = "{{ route('admin.city.get-by-district') }}";

            if (districtId) {
                $.ajax({
                    url: url,
                    type: "GET",
                    data: { district_id: districtId }, // Pass district_id as data
                    success: function (data) {
                        $('#city_name').empty(); // Clear the city dropdown
                        $('#city_name').append('<option value="" selected disabled hidden >Select City Name</option>');

                        // Populate city dropdown with new options
                        $.each(data, function (key, value) {
                            $('#city_name').append('<option value="' + key + '">' + value + '</option>');
                        });
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        alert('Unable to fetch cities. Please try again.');
                    }
                });
            } else {
                $('#city_name').empty(); // Clear the city dropdown
                $('#city_name').append('<option value="" selected disabled hidden>Select City Name</option>');
            }
        });
    });
</script>







