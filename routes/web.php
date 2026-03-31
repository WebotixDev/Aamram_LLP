<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Razorpay\Api\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\inward\FarmDeliveryChallanController;
Auth::routes(['register' => false, 'verify' => false]);

Route::get('/', function () {
    return redirect()->route('login');
});
    Route::get('information', [App\Http\Controllers\outward\OutwardController::class, 'show']);

Route::get('razorpay', [App\Http\Controllers\Sale_order\Sale_orderController::class, 'show'])
    ->name('sale.razorpay');

Route::get('/dynamic-razorpay-order', function(Request $request) {
    $enteredAmount = floatval($request->input('amount')); // ✅ Correct
    $sale_id = $request->sale_id;
    $customer_name = $request->customer_name;

    $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

    $order = $api->order->create([
        'receipt' => 'order_' . time(),
        'amount' => $enteredAmount * 100,
        'currency' => 'INR',
        'payment_capture' => 1,
        'notes' => ['sale_id' => $sale_id,
                   'customer_name' => $customer_name
]
    ]);

    return response()->json(['order_id' => $order['id']]);
});

  Route::get('sale-order-print', [App\Http\Controllers\Sale_order\Sale_orderController::class, 'saleBill'])->name('sale_order.print');

    // Route::get('razorpay/{id}', [App\Http\Controllers\Sale_order\Sale_orderController::class, 'show'])
    // ->name('sale.razorpay');
    // Route::get('sale-order/{id}/print', [App\Http\Controllers\Sale_order\Sale_orderController::class, 'saleBill'])->name('sale_order.print');

    Route::get('paymentsRazorpay', [App\Http\Controllers\Sale_order\Sale_orderController::class, 'paymentRazorpay'])->name('paymentsRazorpay');

use App\Http\Controllers\Sale_order\Sale_orderController;

//Route::post('/webhook', [Sale_orderController::class, 'handleWebhook']);
// Route::post('webhook', [App\Http\Controllers\RazorpayWebhookController::class, 'handle']);

// API For WEB////////////////////////////////////////////////////////////////////////////////////////////////////////////
Route::get('getProducts', [App\Http\Controllers\ApiController::class, 'getProducts']);
Route::get('insertCustomer', [App\Http\Controllers\ApiController::class, 'insertCustomer']);
Route::get('getCountries', [App\Http\Controllers\ApiController::class, 'getCountries']);
Route::get('getStates', [App\Http\Controllers\ApiController::class, 'getStates']);
Route::get('getDistrics', [App\Http\Controllers\ApiController::class, 'getDistrics']);
Route::get('getCities', [App\Http\Controllers\ApiController::class, 'getCities']);
Route::get('CustomerData', [App\Http\Controllers\ApiController::class, 'CustomerData']);
Route::get('getstockbatch', [App\Http\Controllers\ApiController::class, 'getstockbatch']);
Route::get('updateCustomer', [App\Http\Controllers\ApiController::class, 'updateCustomer']);
Route::get('saleorderData', [App\Http\Controllers\ApiController::class, 'saleorderData']);
Route::get('insertSaleorder', [App\Http\Controllers\ApiController::class, 'insertSaleorder']);
Route::get('checkMobile', [App\Http\Controllers\ApiController::class, 'checkMobile']);
Route::get('verifyOtp', [App\Http\Controllers\ApiController::class, 'verifyOtp']);
Route::get('chatbotstock', [App\Http\Controllers\ApiController::class, 'chatbotstock']);


Route::get('sale-order-print', [App\Http\Controllers\Sale_order\Sale_orderController::class, 'saleBill'])->name('sale_order.print');

Route::match(['get', 'post'], '/woocommerce/webhook', [App\Http\Controllers\WooWebhookController::class, 'handleOrder']);

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

Route::group(['middleware' => ['auth'], 'as' => 'admin.', 'prefix' => 'admin'], function () {

    Route::get('dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
Route::post('set-season-session', [App\Http\Controllers\Admin\DashboardController::class, 'setSeasonSession'])->name('set.season.session');
    Route::put('user/status/{id}', [App\Http\Controllers\Admin\UserController::class, 'status'])->name('user.status');

    Route::resource('user', App\Http\Controllers\Admin\UserController::class);
    Route::get('user/remove-image/{id}', [App\Http\Controllers\Admin\UserController::class, 'removeImage'])->name('user.removeImage');

    // Roles
    Route::resource('role', App\Http\Controllers\Admin\RoleController::class);

  // District
   Route::resource('district', App\Http\Controllers\Admin\DistrictController::class);

   Route::resource('city', App\Http\Controllers\Admin\CityController::class );

    // User_profile
    Route::get('edit-profile', [App\Http\Controllers\Admin\UserController::class, 'editProfile'])->name('user.edit-profile');

    Route::view('edit-profile-user', 'users.edit_profile')->name('edit_profile');

    Route::get('get-states', [App\Http\Controllers\Admin\UserController::class, 'getStates'])->name('user.get-states');
    Route::post('update-profile', [App\Http\Controllers\Admin\UserController::class, 'updateProfile'])->name('user.update-profile');

    // Route::get('getstates', [App\Http\Controllers\Master\GroundController::class, 'getStates'])->name('get.states');
    // Route::get('getcities', [App\Http\Controllers\Master\GroundController::class, 'getCities'])->name('get.cities');

    Route::resource('product', App\Http\Controllers\Admin\ProductController::class);
        Route::resource('product_size',App\Http\Controllers\Admin\Product_sizeController::class);
Route::get('/get-type-size/{type}', [App\Http\Controllers\Admin\ProductController::class, 'getSizeByType'])->name('product-size.type');

    Route::get('get-district', [App\Http\Controllers\Admin\CityController::class, 'getDistricts'])->name('city.get-district');

    Route::get('get-cities-by-district', [App\Http\Controllers\Admin\CustomerController::class, 'getCitiesByDistrict'])->name('city.get-by-district');


    Route::get('getstates', [App\Http\Controllers\Master\GroundController::class, 'getStates'])->name('get.states');
    Route::get('getcities', [App\Http\Controllers\Master\GroundController::class, 'getCities'])->name('get.cities');

    Route::resource('customer', App\Http\Controllers\Admin\CustomerController::class );

    Route::resource('subject',App\Http\Controllers\Admin\SubjectController::class);
    Route::resource('supplier',App\Http\Controllers\Admin\SupplierController::class);

    Route::resource('Transporter',App\Http\Controllers\Admin\TransporterController::class);
    Route::resource('Location',App\Http\Controllers\Admin\locationController::class);

    Route::resource('Season',App\Http\Controllers\Admin\SeasonController::class);

    Route::resource('inward', App\Http\Controllers\inward\InwardController::class);
Route::get('/get-type-product/{type}', [App\Http\Controllers\inward\InwardController::class, 'getProductsByType'])->name('product.type');
    Route::get('get-services-product', [App\Http\Controllers\inward\InwardController::class,'getServiceProduct'])->name('inward.get-services-product');
    Route::get('get-size-info', [App\Http\Controllers\inward\InwardController::class,'getServiceProduct'])->name('inward.get-size-info');

    Route::get('/get-product-sizes', [App\Http\Controllers\inward\InwardController::class, 'getProductSizes'])->name('inward.get-sizes');


    Route::resource('farm_inward', App\Http\Controllers\inward\farm_inwardController::class);
    Route::get('farm-inward-get-rate', [App\Http\Controllers\inward\farm_inwardController::class,'farmgetRates'])->name('farm-inward.get-rate');
    Route::get('farm-get-details', [App\Http\Controllers\inward\farm_inwardController::class,'getdetails'])->name('farm-inward.get-details');
    Route::get('FarminwardBill-print', [App\Http\Controllers\inward\farm_inwardController::class, 'FarminwardBill'])->name('FarminwardBill.print');
    Route::get('/farm-inward/get-invoice-batch', [App\Http\Controllers\inward\farm_inwardController::class, 'getInvoiceBatch'])->name('farm-inward.get-invoice-batch');
    Route::resource('Farm_Report', App\Http\Controllers\inward\Farm_inwardReportcontroller::class);

    Route::resource('Farm_Delivery_challan', App\Http\Controllers\inward\FarmDeliveryChallanController::class);
    Route::get('Farm_Delivery_challan.get-invoice', [App\Http\Controllers\inward\FarmDeliveryChallanController::class, 'getInvoiceBatch']) ->name('Farm_Delivery_challan.get-invoice');
    Route::get('/get-batch-by-location', [App\Http\Controllers\inward\FarmDeliveryChallanController::class,'getBatchByLocation']) ->name('get-batch-by-location');
    Route::get('Farm-get-stock', [App\Http\Controllers\inward\FarmDeliveryChallanController::class,'getStock'])->name('FARMDC.get-stock');

    Route::resource('warehouse_inward', App\Http\Controllers\inward\WarehouseInwardController::class);
    Route::get('warehouse_inward.get-invoice', [App\Http\Controllers\inward\WarehouseInwardController::class, 'getInvoiceBatch']) ->name('warehouse_inward.get-invoice');
    Route::get('Farm-DC-get-order-records', [App\Http\Controllers\inward\WarehouseInwardController::class, 'getOrderRecords'])->name('Farm-DC-getOrderRecords');
    Route::get('farm-stock-report',[FarmDeliveryChallanController::class, 'farmStockReport'])->name('farm.stock.forreport');
    Route::get('Warehouse-stock-report',[App\Http\Controllers\inward\WarehouseInwardController::class, 'WarehouseStockReport'])->name('Warehouse.stock.forreport');

    Route::get('Warehouse-get-stock', [App\Http\Controllers\inward\WarehouseInwardController::class,'stockReport'])->name('WarehouseStockReport.get-stock');

    Route::get('inward-get-rate', [App\Http\Controllers\inward\InwardController::class,'getRates'])->name('inward.get-rate');
    Route::get('farm-stock-report',[FarmDeliveryChallanController::class, 'farmStockReport'])->name('farm.stock.forreport');
    Route::get('get-batch-by-locationForStock',[FarmDeliveryChallanController::class, 'getBatchByLocation'])->name('get.batch.by.locationForStock');
    Route::get('Farm-get-stockReport',[FarmDeliveryChallanController::class, 'getStockReport'])->name('FARMReport.get-stock');
    Route::get('FarmDCdBill-print', [FarmDeliveryChallanController::class, 'FarmDCBill'])->name('FarmDCBill.print');


    Route::resource('ripening_chamber', App\Http\Controllers\inward\RipeningChamberController::class);
    Route::get('ripening_chamber.get-invoice', [App\Http\Controllers\inward\RipeningChamberController::class, 'getInvoiceBatch']) ->name('ripening_chamber.get-invoice');
    Route::get('ripening_chamber-data', [App\Http\Controllers\inward\RipeningChamberController::class, 'getData'])->name('ripening_chamber.data');
    Route::get('ripening_chamber-get-order-records', [App\Http\Controllers\inward\RipeningChamberController::class, 'getOrderRecords'])->name('ripening_chamber-getOrderRecords');
    Route::get('consolidated-stock-report', [App\Http\Controllers\inward\RipeningChamberController::class, 'Ripeningstock'])->name('ConsolidatedStockReport');
    Route::get('consolidated-stock-report-data', [App\Http\Controllers\inward\RipeningChamberController::class, 'getStock'])->name('ConsolidatedStockReport.get-stock');
Route::get('get-Warehouse-inward', [App\Http\Controllers\inward\RipeningChamberController::class, 'getRipeningChambers'])
    ->name('Warehouse.getRipeningWarehouse');

    Route::resource('cooling_chamber', App\Http\Controllers\inward\Cooling_ChamberController::class);
    Route::get('cooling_chamber.get-invoice', [App\Http\Controllers\inward\Cooling_ChamberController::class, 'getInvoiceBatch']) ->name('cooling_chamber.get-invoice');
    Route::get('cooling_chamber-get-order-records', [App\Http\Controllers\inward\Cooling_ChamberController::class, 'getOrderRecords'])->name('cooling_chamber-getOrderRecords');

Route::get('get-cooling-chambers', [App\Http\Controllers\inward\Cooling_ChamberController::class, 'getRipeningChambers'])
    ->name('cooling_chamber.getRipeningChambers');
    Route::resource('sale_order', App\Http\Controllers\Sale_order\Sale_orderController::class);
    Route::post('customers.store', [App\Http\Controllers\Sale_order\Sale_orderController::class,'customersstore'])->name('customers.store');
    Route::get('get-stock', [App\Http\Controllers\Sale_order\Sale_orderController::class,'getStock'])->name('sale_order.get-stock');
    Route::get('get-pricee', [App\Http\Controllers\inward\InwardController::class,'getprice'])->name('inward.get-pricee');
    Route::get('get-price', [App\Http\Controllers\Sale_order\Sale_orderController::class,'getPrice'])->name('sale_order.get-price');
    Route::get('get-rate', [App\Http\Controllers\Sale_order\Sale_orderController::class,'getRate'])->name('sale_order.get-rate');

    Route::resource('outward', App\Http\Controllers\outward\OutwardController::class);
    Route::resource('sale_payment', App\Http\Controllers\sale_payment\Sale_PaymentController::class);

    Route::get('/get-customer-records', [App\Http\Controllers\sale_payment\Sale_PaymentController::class, 'getCustomerRecords'])->name('getCustomerRecords');

    Route::get('/get-order-records', [App\Http\Controllers\outward\OutwardController::class, 'getOrderRecords'])->name('getOrderRecords');
    Route::get('/get-order', [App\Http\Controllers\outward\OutwardController::class, 'getOrders'])->name('getOrder');

    Route::resource('reports', App\Http\Controllers\reports\InwardReportController::class);

    // Custom route for generating the report
    Route::get('reports/generate-inward-report', [App\Http\Controllers\reports\InwardReportController::class, 'generateReport'])->name('reports.generate');

    Route::get('inward-reports-data', [App\Http\Controllers\reports\InwardReportController::class, 'getData'])->name('inward.reports.data');

    Route::get('inward-data', [App\Http\Controllers\inward\InwardController::class, 'getData'])->name('inward.data');


    Route::get('sale-data', [App\Http\Controllers\Sale_order\Sale_orderController::class, 'getData'])->name('sale_order.data');

    Route::get('payment-data', [App\Http\Controllers\sale_payment\Sale_PaymentController::class, 'getData'])->name('sale_payment.data');
    Route::get('sale-report-data', [App\Http\Controllers\Sale_orderReportController::class, 'getData'])->name('sale_order-report-.data');


    Route::resource('OutwardReport', App\Http\Controllers\OutwardReportController::class);


    Route::get('generate-outward-report', [App\Http\Controllers\OutwardReportController::class, 'generateReports'])->name('generate-outward-report.index');

    Route::resource('Sale_OrderReport', App\Http\Controllers\Sale_orderReportController::class);

    Route::get('generate-sale-report', [App\Http\Controllers\Sale_orderReportController::class, 'generateReports'])->name('generate-sale-report.index');

    Route::resource('Batch_report', App\Http\Controllers\reports\Batch_stockReportController::class);

    Route::get('/batch-stock-report', [App\Http\Controllers\reports\Batch_stockReportController::class, 'index'])->name('batch.stock.report');

    Route::get('batch-stock-report-data', [App\Http\Controllers\reports\Batch_stockReportController::class, 'getData'])->name('batchStockReportData');

    Route::resource('sale_PenDis_Report', App\Http\Controllers\reports\sale_PenDis_ReportController::class);

    Route::resource('Outstanding_Report', App\Http\Controllers\reports\Outstanding_ReportController::class);

    Route::get('sale_PenDis', [App\Http\Controllers\reports\sale_PenDis_ReportController::class, 'getData'])->name('sale_PenDis_Report-.data');



    Route::get('/generate-qr/{id}', [App\Http\Controllers\outward\OutwardController::class, 'generateQR'])->name('generate-qr');

   // Route::get('information', [App\Http\Controllers\outward\OutwardController::class, 'show']);


    Route::resource('profile', App\Http\Controllers\setting\ProfileController::class);

    Route::resource('expense', App\Http\Controllers\setting\ExpenseController::class);

    Route::get('get-expense-names', [App\Http\Controllers\setting\ExpenseController::class, 'getExpenseNames'])->name('get.expense.names');

    Route::post('subject.store', [App\Http\Controllers\setting\ExpenseController::class,'expensess'])->name('subjects');

    Route::resource('company', App\Http\Controllers\setting\CompanyController::class);

    Route::resource('product_bulk', App\Http\Controllers\setting\Product_bulkController::class);

    // Route::get('sale-order/{id}/print', [App\Http\Controllers\Sale_order\Sale_orderController::class, 'saleBill'])->name('sale_order.print');

    Route::get('sale-order/{id}/labour', [App\Http\Controllers\Sale_order\Sale_orderController::class, 'labourBill'])->name('sale_order.labour');

    Route::get('sale-payment/{id}/print', [App\Http\Controllers\sale_payment\Sale_PaymentController::class, 'sale_payment_bill'])->name('sale_payment.print');


    Route::resource('ledger_report', App\Http\Controllers\reports\LedgerReportController::class);


    Route::get('Bulk_labour-bill-print', [App\Http\Controllers\Sale_order\Sale_orderController::class, 'BulklabourBillPrint'])->name('Bulk_labour-bill-print');


/////// Validation

Route::get('check-mobile/{mobile_no}', [App\Http\Controllers\Admin\CustomerController::class, 'checkMobile'])->name('check.mobile');
Route::get('Bulk_qr-bill-print', [App\Http\Controllers\outward\OutwardController::class, 'QRBulklPrint'])->name('qr-bill-print');
Route::get('Bulk_qr-bill-large-print', [App\Http\Controllers\outward\OutwardController::class, 'QRBulklargePrint'])->name('qr-bill-print-large');
Route::get('/getstock', [App\Http\Controllers\inward\InwardController::class, 'getstock'])->name('inward.getstock');

    Route::get('Bulk_labour_outward', [App\Http\Controllers\outward\OutwardController::class, 'BulklabourBill'])->name('Bulk_labour_outwards');

Route::get('Farm_bulkReceiptBill', [App\Http\Controllers\outward\OutwardController::class, 'Farm_bulkReceiptBill'])->name('Farm_bulkReceipt');

Route::resource('Delivery_Challan',App\Http\Controllers\outward\Delievry_ChallanController::class);

Route::get('devlivery_challan/{id}', [App\Http\Controllers\outward\Delievry_ChallanController::class, 'challanBill'])->name('devlivery_challan.print');

Route::get('Customer-challan/{id}', [App\Http\Controllers\outward\Delievry_ChallanController::class, 'customerChllan'])->name('customer-chllan.print');

Route::resource('Customer_bill', App\Http\Controllers\Customer_saleBillController::class);

Route::get('Customer_bill-data', [App\Http\Controllers\Customer_saleBillController::class, 'getData'])->name('Customer_bill.data');


Route::get('Customer_Bill', [App\Http\Controllers\Customer_saleBillController::class, 'Customer_Bill'])->name('Customer_Sale_Bill');

    Route::resource('sale_product', App\Http\Controllers\Sale_orderProductController::class);


 Route::get('get-customer-addresses/{id}', [App\Http\Controllers\Sale_order\Sale_orderController::class,'getCustomerAddresses'])->name('customer-addresses');
Route::get('sale_pro-data', [App\Http\Controllers\Sale_orderProductController::class, 'getData'])->name('sale_product.data');

Route::resource('investors', App\Http\Controllers\InvestorsController::class);
Route::post('investors_insert', [App\Http\Controllers\InvestorsController::class , 'investorAdd'])->name('investors_insert');
Route::get('balance/{id}', [App\Http\Controllers\InvestorsController::class,'investoreBalance'])->name('investor-balance');
Route::get('investors/invest/{investorId}', [App\Http\Controllers\InvestorsController::class, 'show'])->name('invest');


     Route::get('/orders/import', [App\Http\Controllers\Admin\DashboardController::class, 'import'])->name('orders.import');
 Route::post('/orders/import/store', [App\Http\Controllers\Admin\DashboardController::class, 'importstore'])->name('orders.importstore');

});



