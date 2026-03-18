<div class="form theme-form">
    <!-- General Information Section -->
    <div class="row">
   @php


$count = DB::table('sale_orderdetails')->max('Invoicenumber');

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
        data-date-format="dd-mm-yyyy" data-auto-close="true">

    @error('billdate')
        <span class="text-danger"><strong>{{ $message }}</strong></span>
    @enderror
</div>


<div class="form-group col-md-3">
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
    </div>
    <button type="button" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#addCustomerModal">Add</button>
</div>



@if(isset($saleOrders))
    @php
        $customer = DB::table('customers')->where('id', $saleOrders->customer_name)->first();
        $addresses = [];

        if ($customer) {
            if (!empty($customer->address)) {
                $addresses[] = $customer->address;
            }
            if (!empty($customer->address1)) {
                $addresses[] = $customer->address1;
            }
            if (!empty($customer->address2)) {
                $addresses[] = $customer->address2;
            }
        }
    @endphp

    <div class="form-group col-md-3">
        <label>Address <span class="required" style="color:red;">*</span></label>
        <select class="form-select select2" id="order_address" name="order_address" required>
            <option value="">Select Address</option>
            @foreach($addresses as $addr)
                <option value="{{ $addr }}" {{ $saleOrders->order_address == $addr ? 'selected' : '' }}>
                    {{ $addr }}
                </option>
            @endforeach
        </select>
    </div>
@else
    <div class="form-group col-md-3">
        <label>Address <span class="required" style="color:red;">*</span></label>
        <div class="d-flex align-items-center">
            <select class="form-select select2" id="order_address" name="order_address" required>
                <option value="">Select Address</option>
            </select>
        </div>
    </div>
@endif

<div class="form-group col-md-2">
    <label>	Wholesaler Name </label>
    <div class="d-flex align-items-center">
        <select class="form-select select2" id="wholesaler" name="wholesaler" data-placeholder="Select Wholesaler" >
            <option value="">Select Customer</option>
            @php
            $wholesaler = DB::table('customers')
                ->where('vendor', 'WholeSaler')
                ->get();
            @endphp
            @foreach ($wholesaler as $wholesalers)
                <option value="{{ $wholesalers->id }}"
                    @if(isset($saleOrders) && $saleOrders->wholesaler == $wholesalers->id) selected @endif>
               {{ $wholesalers->customer_name }}
                </option>
            @endforeach
        </select>

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

                            <th width="15%">PRODUCTS<span class="required" style="color:red;">*</span></th>
                            <th width="20%">SIZE<span class="required" style="color:red;">*</span></th>
                            <th width="10%">Stage<span class="required" style="color:red;">*</span></th>
                            <th>Stock <span class="required" style="color:red;">*</span></th>
                            <th>RATE <span class="required" style="color:red;">*</span></th>
                            <th class="qty-container">Order Qty <span class="required" style="color:red;"></span></th>
                            <th class="quantity-container">Dis.Qty <span class="required" style="color:red;"></span></th>
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
                            <select class="select2" id="services_<?= $i ?>" name="services_<?= $i ?>"
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
                            <select class="" id="size_<?= $i ?>" name="size_<?= $i ?>" data-placeholder="Select Size" style="height: 37px; width:100%;" onchange="fetchrate(this.id);" required>
                            <option value="">Select Size</option>
                            @php
                            $sizes = DB::table('product_details')->where('status', 1)->where('sizeoff', 0)->where('disable','!=',1)->where('parentID', $sale_order_list1['services'])->get();
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
                            <select class="select2" id="stage_<?= $i ?>" name="stage_<?= $i ?>" data-placeholder="Select Stage" style="height: 37px; width:100%;" required >
                            <option value="">Select Stage</option>
                            <option value="Raw" @if($sale_order_list1->stage == 'Raw') selected @endif>Raw</option>
                            <option value="Semi Ripe" @if($sale_order_list1->stage == 'Semi Ripe') selected @endif>Semi Ripe</option>
                            <!--<option value="Ripe" @if($sale_order_list1->stage == 'Ripe') selected @endif>Ripe</option>-->
                        </select>
                            </td>

                            <!-- Stock Field -->
                            <td>
                            <input type="text" id="stock_<?= $i ?>" class="form-control"
                            value="{{ $stocknew ?? '' }}" readonly>
                            </td>

                            <!-- Rate Field -->
                            <td>
                            <input type="text" id="rate_<?= $i ?>" name="rate_<?= $i ?>"
                            class="form-control" value="{{ $sale_order_list1['rate'] ?? '' }}" onkeyup="Gettotal();" required >
                            </td>

                            <!-- Quantity Field -->
                            <td>
                            <input type="text" id="qty_<?= $i ?>" name="qty_<?= $i ?>"
                            class="form-control" value="{{ $sale_order_list1['qty'] ?? '' }}"
                            onkeyup="Gettotal();" required>
                            </td>
                            <td>
                            <input type="text" id="quantity_<?= $i ?>" name="Quantity_<?= $i ?>"
                            class="form-control" value="{{ $sale_order_list1['Quantity'] ?? '' }}"
                            onkeyup="Gettotal();validateStockVsQuantity(<?= $i ?>);" required>
                            </td>

                            <!-- GST Percentage -->
                            <td>
                            <input type="text" id="gstper_<?= $i ?>" name="gstper_<?= $i ?>"
                            class="form-control" value="{{ $sale_order_list1['gstper'] ?? '' }}" readonly>
                            </td>

                            <td>
                            <input type="text" id="transper_<?= $i ?>" name="transper_<?= $i ?>"
                            class="form-control" onkeyup="Gettotal();" value="{{ $sale_order_list1['transper'] ?? '' }}" >
                            </td>

                            <!-- Amount -->
                            <td>
                            <input type="text" id="amount_<?= $i ?>" name="amount_<?= $i ?>"
                            class="form-control" value="{{ $sale_order_list1['amount'] ?? '' }}" readonly>
                            </td>

                            <!-- Delete Button -->
                            <td>
                            <button id="button_<?= $i ?>" name="button_<?= $i ?>" type="button" class=""
                            onclick="deleteRow(this.id)"><i class="fa fa-trash"></i>
                            </button>

                            </td>

                            <!-- Hidden Fields -->
                            <input type="hidden" id="CGSTper_<?= $i ?>" value="{{ $sale_order_list1['CGSTper'] ?? '' }}">
                            <input type="hidden" id="SGSTper_<?= $i ?>" value="{{ $sale_order_list1['SGSTper'] ?? '' }}">
                            <input type="hidden" id="IGSTper_<?= $i ?>" value="{{ $sale_order_list1['IGSTper'] ?? '' }}">
                            <!-- <input type="hidden" name="cnt" id="cnt" value="<?= $i ?>"> -->
                            </tr>

                            <?php
                            }
                            } else{
                            ?>
                   <?php $i = 1; ?>
                        <tr id="row_<?= $i ?>">

                            <td>
                                <select class="select2" id="services_<?= $i ?>" name="services_<?= $i ?>" data-placeholder="Products" style="height: 37px; width:100%;" onchange="getprice(this.id);getstock(this.id);" required>
                                    <option value=""></option>
                                    @php
                                        $pro_masters = DB::table('products')->where('disable','!=',1)->get();
                                    @endphp
                                    @foreach ($pro_masters as $pro_master)
                                        <option value="{{ $pro_master->id }}">{{ $pro_master->product_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select class="required" id="size_<?= $i ?>" name="size_<?= $i ?>" onchange="fetchrate(this.id);" data-placeholder="Select Size" style="height: 37px; width:100%;"   required>
                                    <option value=""></option>
                                </select>
                            </td>
                            <td>
                                <select  id="stage_<?= $i ?>" name="stage_<?= $i ?>" data-placeholder="Select Stage" style="height: 37px; width:100%;" required>
                            <option value="">Select Stage</option>
                            <option value="Raw">Raw</option>
                            <option value="Semi Ripe">Semi Ripe</option>
                            <!--<option value="Ripe">Ripe</option>-->
                                </select>
                            </td>


                            <td>
                                <input type="text" id="stock_<?= $i ?>"  class="form-control" value="" readonly>
                            </td>

                            <td><input name="rate_<?= $i ?>" id="rate_<?= $i ?>" class="form-control input-sm" onkeyup="Gettotal();" type="text" data-placeholder="Rate" style="width:100%;" value="" required ></td>

                            <td class="qty-container">
                            <input type="text" id="qty_<?= $i ?>" name="qty_<?= $i ?>" onkeyup="Gettotal();" class="form-control" required>
                            </td>

                            <td class="quantity-container">
                            <input type="text" id="quantity_<?= $i ?>" name="quantity_<?= $i ?>" onkeyup="Gettotal();validateStockVsQuantity(1);" class="form-control" required>
                            </td>

                            <td><input name="gstper_<?= $i ?>"   id="gstper_<?= $i ?>" class="form-control input-sm" onkeyup="Gettotal();" type="text" style="width:100%;" value="" required readonly></td>

                            <td>
                            <input type="text" id="transper_<?= $i ?>" name="transper_<?= $i ?>"
                            class="form-control" onkeyup="Gettotal();" value="{{ $sale_order_list1['transper'] ?? '' }}" >
                            </td>


                            <td><input name="amount_<?= $i ?>" readonly id="amount_<?= $i ?>" class="form-control input-sm" type="text" data-placeholder="Amount" style="width:100%;" value=""></td>
                            <td>
                            <button id="button_<?= $i ?>" name="button_<?= $i ?>" type="button" class=""
                            onclick="deleteRow(this.id)"><i class="fa fa-trash"></i>
                            </button>
                            </td>

                            <input name="CGSTper_<?= $i ?>" readonly id="CGSTper_<?= $i ?>" class="form-control input-sm" type="hidden" data-placeholder="CGST" style="width:100%;" value="">
                            <input name="SGSTper_<?= $i ?>" readonly id="SGSTper_<?= $i ?>" class="form-control input-sm" type="hidden" data-placeholder="CGST" style="width:100%;" value="">
                            <input name="IGSTper_<?= $i ?>" readonly id="IGSTper_<?= $i ?>" class="form-control input-sm" type="hidden" data-placeholder="CGST" style="width:100%;" value="">

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
                                                onblur="calcDiscAmt();" <?php //echo $disabled; ?>
                                                    class="form-control input-sm " type="text"
                                                    data-placeholder="Discount" style="width:100%;"
                                                    value="<?php echo isset($saleOrders) ? $saleOrders['discount_per'] : '0'; ?>"
                                                    required>

                                            </div>

                                            <div class="col-md-3">

                                            <label>Discount in (<i class="fa fa-rupee"></i>) <span
                                            class="required" style="color:red;">*</span> </label>
                                                <input name="discount_rupee" id="discount_rupee"
                                                    onblur="calcDiscPer();" <?php //echo $disabled; ?>
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
                                        <select class="select2" tabindex="-1" data-placeholder="Select Payment Method" style="width:100%" name="mode" id="mode" required>
                                    <option value=""></option>
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
                                             <div class="row">
                                            <div class= col-sm-6 "mb-3">
                                                <label for="new_customer_name" class="form-label">Customer Name</label>
                                                <input type="text" class="form-control" id="new_customer_name" name="customer_name"  oninput="this.value = this.value.toUpperCase();">
                                            </div>
                                                 <div class=" col-sm-6 mb-3">
                                                <label for="new_customer_type" class="form-label">Vendor</label>
                                                <select class="form-select" id="new_customer_type" name="customer_type">
                                                    <option value="" disabled selected>Select Type</option>
                                                    <option value="wholesaler">Wholesaler</option>
                                                    <option value="retailer">Retailer</option>
                                                    <option value="Customer">Customer</option>

                                                </select>
                                            </div>
                                            </div>
                                            <div class="row">
                                            <div class="col-sm-6 mb-3">
                                                <label for="new_customer_number" class="form-label">Mobile Number</label>
                                                <input type="text" class="form-control" id="new_customer_number" name="customer_number">
                                            </div>
                                               <div class="col-sm-6 mb-3">
                                                <label for="new_wp_number" class="form-label">WhatsApp Number</label>
                                                <input type="text" class="form-control" id="wp_number" name="wp_number">
                                            </div>

                                            </div>


                                                <div class="mb-3">
                                <label>Address<span></span></label>
                                <input class="form-control" type="text" name="address" id="address" value="" placeholder="Enter Your Address"  oninput="this.value = this.value.toUpperCase();">
                                @error('address')

                                @enderror
                            </div>
                            <div class="row">
                                            <div class="col-sm-6 mb-3">
                                                <label for="state_id" class="form-label">State</label>
                                                <select class="form-select" name="state_id" id="state_id">
                                                    <option value="" selected disabled hidden>Select State</option>
                                                    @foreach ($states as $key => $state)
                                                        <option value="{{ $key }}">{{ $state }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                         <div class="col-sm-6 mb-3">
                                                <label for="district_id" class="form-label">District</label>
                                                <input class="form-control" type="text" name="district_id" id="district_id"  placeholder="Enter District Name "  oninput="this.value = this.value.toUpperCase();">
                                            </div>
                     </div>                       <div class="row">

                                            <div class="col-sm-6 mb-3">
                                                <label for="city_name">City Name<span></span></label>
                                                <input type="text" class="form-control" name="city_name" id="city_name" placeholder="Enter City Name"  oninput="this.value = this.value.toUpperCase();">
                                            </div>

                                                                              <div class=" col-sm-6 mb-3">
                <label>Pin Code<span></span></label>
                <input class="form-control" type="text" name="pin_code" id="pin_code" value="" placeholder="Enter Pin Code">

            </div>
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

    $(document).ready(function() {

    $(".select2").select2();

    // Trigger getprice for existing rows in edit mode
    // let rowCount = $("#cnt").val();

    // for (let i = 1; i <= rowCount; i++) {
    //     let product = $("#services_" + i).val();

    //     if (product) {
    //         getprice("services_" + i);
    //     }
    // }

});
      function addRow() {
                    const tableBody = document.getElementById('saleTable').querySelector('tbody');
                    const rowCount = tableBody.rows.length + 1; // Calculate row count dynamically

                    const newRow = `

                    <tr id="row_${rowCount}">

                <td style="width:20px;">
                    <select style="width:10px;" class="select2" id="services_${rowCount}" name="services_${rowCount}"
                            data-placeholder="Select Products" style="height: 37px; width:100%;"
                            onchange="getprice(this.id);getstock(this.id);" required>
                        <option value=""></option>
                        @php
                         $pro_masters = DB::table('products')->where('disable','!=',1)->get();
                        @endphp
                        @foreach ($pro_masters as $pro_master)
                            <option value="{{ $pro_master->id }}">{{ $pro_master->product_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select  id="size_${rowCount}" name="size_${rowCount}"
                            data-placeholder="Select Size" style="height: 37px; width:100%;" onchange="fetchrate(this.id);" required>
                        <option value=""></option>
                    </select>
                </td>
                <td>
                    <select  id="stage_${rowCount}" name="stage_${rowCount}"
                            data-placeholder="Select Stage" style="height: 37px; width:100%;" required>
                            <option value="">Select Stage</option>
                               <option value="Raw">Raw</option>
                        <option value="Semi Ripe">Semi Ripe</option>
                    </select>
                </td>
                <td>
                    <input type="text" id="stock_${rowCount}"  class="form-control" required readonly>
                </td>
                <td>
                    <input type="text" id="rate_${rowCount}" name="rate_${rowCount}" class="form-control smallinput" onkeyup="Gettotal();" required >
                </td>
                <td class="qty-container" >
                    <input type="number" id="qty_${rowCount}" name="qty_${rowCount}" class="form-control smallinput"
                    onkeyup="Gettotal();"   required>
                </td>
                <td class="quantity-container" >
                    <input type="number" id="quantity_${rowCount}" name="quantity_${rowCount}" class="form-control smallinput" onkeyup="Gettotal();validateStockVsQuantity(${rowCount});"  >
                </td>

                <td>
                    <input type="number" id="gstper_${rowCount}" name="gstper_${rowCount}" onkeyup="Gettotal();" class="form-control smallinput"
                            required readonly>
                </td>
                 <td >
                    <input type="number" id="transper_${rowCount}" name="transper_${rowCount}" class="form-control smallinput" onkeyup="Gettotal();"  >
                </td>
                <td>
                    <input type="text" id="amount_${rowCount}" name="amount_${rowCount}" class="form-control smallinput" readonly required>
                </td>
                <td>
                <button id="button_${rowCount}" name="button_${rowCount}" type="button" class="" onclick="deleteRow(this.id)"><i class="fa fa-trash"></i>
</button></td>


                </td>

            <!--input name="CGSTper_${rowCount}" readonly id="CGSTper_${rowCount}" class="form-control input-sm" type="hidden" data-placeholder="CGST" style="width:100%;" value="">
            <input name="SGSTper_${rowCount}" readonly id="SGSTper_${rowCount}" class="form-control input-sm" type="hidden" data-placeholder="CGST" style="width:100%;" value="">
            <input name="IGSTper_${rowCount}" readonly id="IGSTper_${rowCount}" class="form-control input-sm" type="hidden" data-placeholder="CGST" style="width:100%;" value=""-->


            </tr>`;

            tableBody.insertAdjacentHTML('beforeend', newRow);

            // Update the hidden input field with the latest row count
            document.getElementById('cnt').value = rowCount;


            $(".select2").select2();
    }


  // Function to delete a row

  function deleteRow(rwcnt)
{
    var id=rwcnt.split("_");
    var cnt=id[1];
    var count=$("#cnt").val();
    if(count>1)
    {
        var r=confirm("Are you sure!");
        if (r==true)
        {

            $("#row_"+cnt).remove();

            for(var k=cnt; k<=count; k++)
            {
                var newId=k-1;

                jQuery("#row_"+k).attr('id','row_'+newId);

                jQuery("#idd_"+k).attr('name','idd_'+newId);
                jQuery("#idd_"+k).attr('id','idd_'+newId);
                jQuery("#idd_"+newId).html(newId);

                jQuery("#services_"+k).attr('name','services_'+newId);
                jQuery("#services_"+k).attr('id','services_'+newId);

                jQuery("#size_"+k).attr('name','size_'+newId);
                jQuery("#size_"+k).attr('id','size_'+newId);


                jQuery("#stage_"+k).attr('name','stage_'+newId);
                jQuery("#stage_"+k).attr('id','stage_'+newId);

                jQuery("#stock_"+k).attr('id','stock_'+newId);

                jQuery("#rate_"+k).attr('name','rate_'+newId);
                jQuery("#rate_"+k).attr('id','rate_'+newId);

                jQuery("#qty_"+k).attr('name','qty_'+newId);
                jQuery("#qty_"+k).attr('id','qty_'+newId);


                jQuery("#quantity_"+k).attr('name','quantity_'+newId);
                jQuery("#quantity_"+k).attr('id','quantity_'+newId);

                 jQuery("#gstper_"+k).attr('name','gstper_'+newId);
                 jQuery("#gstper_"+k).attr('id','gstper_'+newId);

                jQuery("#transper_"+k).attr('name','transper_'+newId);
                jQuery("#transper_"+k).attr('id','transper_'+newId);


                jQuery("#amount_"+k).attr('name','amount_'+newId);
                jQuery("#amount_"+k).attr('id','amount_'+newId);

                jQuery("#button_"+k).attr('name','button_'+newId);
                jQuery("#button_"+k).attr('id','button_'+newId);


            }
            jQuery("#cnt").val(parseFloat(count-1));

        }
    }
    else
    {
        alert("Can't remove row Atleast one row is required");
        return false;
    }
    Gettotal();
}





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


function validateStockVsQuantity(rowId) {
    let qty = parseFloat($(`#qty_${rowId}`).val());
    let quantity = parseFloat($(`#quantity_${rowId}`).val());

    if (quantity > qty) {
        alert("Order quantity cannot exceed available stock.");
         $(`#quantity_${rowId}`).val(''); // Clear the invalid quantity value
        // return false;
    }
    // return true;
}

function getprice(id) {
    let rowIndex = id.replace("services_", "");
    let services = $("#services_" + rowIndex).val();
    let gst = $('input[name=gst]:checked').val();

    if (!services) return; // Exit if no product selected

    $.ajax({
        url: "{{ route('admin.sale_order.get-price') }}",
        type: "GET",
        data: { services, gst, rowIndex, _token: "{{ csrf_token() }}" },
        success: function(response) {

            // ✅ Case 1: Product has no size
            if (response.status === "nosize") {
                alert("No size available for this product");

                // Reset all related fields
                $("#services_" + rowIndex).val('').trigger('change');
                $("#size_" + rowIndex).html('<option value="">Select Size</option>');
                $("#rate_" + rowIndex).val(0);
                $("#stock_" + rowIndex).val(0);
                $("#gstper_" + rowIndex).val(0);
                $("#CGSTper_" + rowIndex).val(0);
                $("#SGSTper_" + rowIndex).val(0);
                $("#IGSTper_" + rowIndex).val(0);
                $("#quantity_" + rowIndex).val(0);
                $("#qty_" + rowIndex).val(0);
                $("#transper_" + rowIndex).val(0);

                return; // Stop execution here
            }

            // ✅ Case 2: Error or unknown response
            if (response.status !== "success") {
                alert("Unable to load Sizes. Please try again.");
                return;
            }

            // ✅ Case 3: Product has sizes
            let sizeOptions = (response.data || []).map(item =>
                `<option value="${item.id}">${item.product_size}</option>`
            ).join("");
            $("#size_" + rowIndex)
                .html(sizeOptions)
                .trigger("change");

            // Attach size change event
            $("#size_" + rowIndex).off("change").on("change", function () {
                let size = $(this).val();
                let stage = $("#stage_" + rowIndex).val();
                getrate(services, size, rowIndex);
                getstock(services, size, stage, rowIndex);
            });

            // Populate stage dropdown
            let stageOptions = `
                <option value="">Select Stage</option>
                <option value="Raw">Raw</option>
                <option value="Semi Ripe">Semi Ripe</option>
                <option value="Ripe">Ripe</option>
            `;
            $("#stage_" + rowIndex)
                .html(stageOptions)
                .trigger("change");

            // Attach stage change event
            $("#stage_" + rowIndex).off("change").on("change", function () {
                let stage = $(this).val();
                let size = $("#size_" + rowIndex).val();
                getstock(services, size, stage, rowIndex);
            });

            // Set GST and product details
            $("#gstper_" + rowIndex).val(response.gst || 0);
            $("#CGSTper_" + rowIndex).val(response.pro_data.cgst || 0);
            $("#SGSTper_" + rowIndex).val(response.pro_data.sgst || 0);
            $("#IGSTper_" + rowIndex).val(response.pro_data.igst || 0);
            $("#quantity_" + rowIndex).val(response.quantity || 0);
            $("#qty_" + rowIndex).val(response.quantity || 0);
            $("#transper_" + rowIndex).val(response.transper || 0);

            // Set initial rate and stock if size exists
            let initialSize = $("#size_" + rowIndex).val();
            if (initialSize) {
                getrate(services, initialSize, rowIndex);
                let initialStage = $("#stage_" + rowIndex).val();
                getstock(services, initialSize, initialStage, rowIndex);
            }
        },
        error: function () {
            alert("An error occurred while fetching the price.");
        }
    });
}

function getrate(services, size, rowIndex) {
    if (!services || !size) return;

    $.ajax({
        url: "{{ route('admin.sale_order.get-rate') }}",
        type: "GET",
        data: { services, size, _token: "{{ csrf_token() }}" },
        success: function (response) {
            if (response.status === "success") {
                $("#rate_" + rowIndex).val(response.rate || 0);
                Gettotal(); // Update total after rate change
            } else {
                alert("Unable to fetch rate for this product.");
            }
        },
        error: function () {
            alert("An error occurred while fetching the rate.");
        }
    });
}
function fetchrate(id) {
    let rowIndex = id.split("_")[1];
    let services = $("#services_" + rowIndex).val();
    let size = $("#size_" + rowIndex).val();
    let stage = $("#stage_" + rowIndex).val();

    if (!services || !size) return;

    // Fetch the rate and stock
    getrate(services, size, rowIndex);
    getstock(services, size, stage, rowIndex);
}

function getstock(services, size, stage, rowIndex) {
        if (!services || !size || !stage) return;

    $.ajax({
        url: "{{ route('admin.sale_order.get-stock') }}",
        type: "GET",
        data: { services, size, stage, rowIndex, _token: "{{ csrf_token() }}" },
        success: function (response) {
            if (response.status === "success") {
                let stock = response.stock || 0; // Use default value of 0 if stock is not returned
                $("#stock_" + rowIndex).val(stock); // Set the stock value in the respective input
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

    var totalAmt = 0;
    var subtotal = 0;
    var totcgst = 0;
    var totsgst = 0;
    var totigst = 0;
    var totdisc = parseFloat($("#discount_rupee").val()) || 0;
    //var totdiscper = parseFloat($("#discount_per").val()) || 0;

    var gst = $('input[name=gst]:checked').val();
    let dispatch = $('#dispatch').val(); // Correct dispatch value fetching from dropdown

    for (var i = 1; i <= cnt; i++) {
        var gsttotal = parseFloat(jQuery("#gstper_" + i).val()) || 0;
        var cgst = gsttotal / 2;
        var sgst = gsttotal / 2;
        var igst = gsttotal;

        // Determine quantity based on dispatch condition
        var qty = parseFloat(jQuery("#qty_" + i).val()) || 0;
        var rate = parseFloat(jQuery("#rate_" + i).val()) || 0;
        var transperqty = parseFloat(jQuery("#transper_" + i).val()) || 0;

        // console.log(rate);

        var transper =qty * transperqty;

        var proamt = qty * rate + transper;
        var netamt = proamt;

        totalAmt += proamt;
        $("#amount_" + i).val(proamt.toFixed(2));

        // GST calculations
        if (gst === 'Maharashtra') {
            totcgst += (netamt * cgst) / 100;
            totsgst += (netamt * sgst) / 100;
        } else {
            totigst += (netamt * igst) / 100;
        }

        subtotal += netamt;
    }

    subtotal -= totdisc; // Deduct rupee discount from subtotal

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
    var subtotal = parseFloat($("#totalproamt").val()) || 0;
    var discountAmount = (subtotal * discountPercentage) / 100;
    $("#discount_rupee").val(discountAmount.toFixed(2));
    Gettotal(); // Update total after discount
}

// Function to calculate discount percentage
function calcDiscPer() {
    var discountAmount = parseFloat($("#discount_rupee").val()) || 0;
    var subtotal = parseFloat($("#totalproamt").val()) || 0;
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
    let address = document.getElementById('address').value;
    let wp_number = document.getElementById('wp_number').value;
    let pin_code = document.getElementById('pin_code').value;

    // Validate inputs
    if (customerName && customerNumber && customerType && stateId && districtId && cityName  && address && wp_number && pin_code) {
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
                city_name: cityName,
                address: address,
                wp_number:wp_number,
                pin_code :pin_code
            },
            success: function (response) {
                if (response.success) {
                    alert('Customer added successfully!');
                    // // Reset form and hide modal
                    // $('#addCustomerForm')[0].reset();
                    // $('#addCustomerModal').modal('hide');
                    window.location.reload(); // Refresh the page
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
    $(document).ready(function () {
        // Attach change event to customer dropdown
        $('#customer_name_sale').on('change', function () {
            var customerId = $(this).val();

            if (customerId) {
                $.ajax({
                    url: '{{ route("admin.customer-addresses", ":id") }}'.replace(':id', customerId),
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        // Clear and reset the address dropdown
                        $('#order_address').empty().append('<option value="">Select Address</option>');

                        // Populate address dropdown with actual text values
                        $.each(data, function (index, item) {
                            $('#order_address').append(
                                '<option value="' + item.value + '">' + item.label + '</option>'
                            );
                        });
                    },
                    error: function (xhr) {
                        console.error('Error fetching addresses:', xhr.responseText);
                        alert('Failed to load addresses. Please try again.');
                    }
                });
            } else {
                $('#order_address').empty().append('<option value="">Select Address</option>');
            }
        });
    });
</script>









