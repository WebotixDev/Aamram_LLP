
@extends('layouts.simple.master')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
    <!-- Range slider css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/rangeslider/rSlider.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/prism.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/fullcalender.css') }}">

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<style>
 @media (max-width: 768px) {
    .dataTables_wrapper {
        overflow-x: auto;
    }
    table.dataTable {
        width: 100% !important;
        table-layout: fixed;
                font-size: 8px; /* You can make this even smaller for mobile if needed */

    }
}

#example th, #example td {
    width: 50px; /* Adjust width as needed */
    white-space: nowrap; /* Prevent text from wrapping */
    text-align: left;
    font-size: 14px; /* Adjust font size for better fit */
    padding: 5px; /* Reduce padding */
}
</style>

@endsection

@section('main_content')


<?php 

 $season = session('selected_season'); // if not set, use current year

               //echo $season;

//$last_year = date('Y', strtotime('last year'));

//$currentYearStart = $last_year . '-06-01'; // 2024-06-01 if last year was 2024

//$currentYearEnd = date('Y') . '-05-31'; // 2025-05-31 if current year is 2025

$current_year = date('Y');
$next_year = date('Y', strtotime('+1 year'));

$currentYearStart = $current_year . '-06-01'; // 2025-06-01
$currentYearEnd = $next_year . '-05-31';      // 2026-05-31

                $sale_order = Auth::id();

                $top_sale_year = DB::table('sale_orderdetails')
                    // ->where('user_id', $sale_order)  // Filter by the authenticated user's ID
                 ->where('season', $season)
                    ->sum('Tamount');  // Sum of Tquantity for the filtered records



                $inward = Auth::id();

                $top_sll_year = DB::table('purchase_details')
                    // ->where('user_id', $inward)  // Filter by the authenticated user's ID
                    ->where('season', $season)
                    ->sum('Tquantity');  // Sum of Tquantity for the filtered records



                    $sale_amt = Auth::id();

                $amt_pay_sale = DB::table('sale_orderdetails')
                    // ->where('user_id', $sale_order)  // Filter by the authenticated user's ID
                    ->where('season', $season)
                    ->sum('amt_pay');  // Sum of Tquantity for the filtered records


                    $sale_payment = Auth::id();

                    $top_sale_payment = DB::table('purchase_payments')
                        // ->where('user_id', $sale_payment)  // Filter by the authenticated user's ID
                            ->where('season', $season)
                         ->select(DB::raw('SUM(COALESCE(amt_pay, 0) + COALESCE(cheque_amt, 0)) as total_amount'))
                        ->value('total_amount');




                    $expenses = DB::table('expense')
                      ->where('season', $season)
                    ->sum('amt_pay');  // Sum of Tquantity for the filtered records

                    $expensess = DB::table('expense')
                      ->where('season', $season)
                    ->sum('cheque_amt');  // Sum of Tquantity for the filtered records

                    $totalexpenses  =$expenses +$expensess ;
        ?>

    <div class="container-fluid" >
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>
                        Project Management </h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                                <svg class="stroke-icon">
                                    <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg></a></li>
                        <li class="breadcrumb-item">Dashboard</li>
                        <li class="breadcrumb-item active">Project-Management</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid" >
        <div class="row size-column">
            <div class="col-xxl-12 box-col-12">
                <div class="row">
                    <div class="col-xl-3 col-sm-6">
                        <div class="card o-hidden small-widget">
                            <div class="card-body total-project border-b-primary border-2"><span
                                    class="f-light f-w-500 f-14">Total Purchase</span>
                                <div class="project-details">
                                <div class="project-counter">
                                    <h2 class="f-w-600">{{ number_format($top_sll_year) }} <i class="fa-solid fa-indian-rupee-sign"></i></h2>
                                    <span class="f-12 f-w-400">(This Year)</span>
                                </div>

                                    <div class="product-sub bg-primary-light">
                                        <svg class="invoice-icon">
                                            <use href="{{ asset('assets/svg/icon-sprite.svg#color-swatch') }}"></use>
                                        </svg>
                                    </div>
                                </div>
                                <ul class="bubbles">
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">

                        <div class="card o-hidden small-widget">
                            <div class="card-body total-Progress border-b-warning border-2"> <span
                                    class="f-light f-w-500 f-14">Sales</span>


                                <div class="project-details">
                                    <div class="project-counter">
                                        <h2 class="f-w-600">{{ number_format($top_sale_year) }} <i class="fa-solid fa-indian-rupee-sign"></i></h2><span class="f-12 f-w-400">(This Year) </span>
                                    </div>
                                    <div class="product-sub bg-warning-light">
                                        <svg class="invoice-icon">
                                            <use href="{{ asset('assets/svg/icon-sprite.svg#tick-circle') }}"></use>
                                        </svg>
                                    </div>
                                </div>
                                <ul class="bubbles">
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="card o-hidden small-widget">
                            <div class="card-body total-Complete border-b-secondary border-2"><span
                                    class="f-light f-w-500 f-14">Payment Credited</span>
                                <div class="project-details">
                                    <div class="project-counter">
                                        <h2 class="f-w-600">{{ number_format($top_sale_payment) }} <i class="fa-solid fa-indian-rupee-sign"></i></h2><span class="f-12 f-w-400">(This Year) </span>
                                    </div>
                                    <div class="product-sub bg-secondary-light">
                                        <svg class="invoice-icon">
                                            <use href="{{ asset('assets/svg/icon-sprite.svg#add-square') }}"></use>
                                        </svg>
                                    </div>
                                </div>
                                <ul class="bubbles">
                                    <li class="bubble"> </li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"> </li>
                                    <li class="bubble"></li>
                                    <li class="bubble"> </li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"> </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6">
                        <div class="card o-hidden small-widget">
                            <div class="card-body total-upcoming"><span class="f-light f-w-500 f-14">Expenses</span>
                                <div class="project-details">
                                    <div class="project-counter">
                                        <h2 class="f-w-600 ">{{ number_format($totalexpenses) }}</h2> <i class="fa-solid fa-indian-rupee-sign"></i>(This Year) </span>
                                    </div>
                                    <div class="product-sub bg-light-light">
                                        <a href="{{ route('admin.expense.create') }}">
                                        <svg class="invoice-icon">
                                            <use href="{{ asset('assets/svg/icon-sprite.svg#edit-2') }}"></use>
                                        </svg>
                                    </a>

                                    </div>
                                </div>
                                <ul class="bubbles">
                                    <li class="bubble"> </li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                    <li class="bubble"></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                      <div class="col-md-12">
                        <div class="card">
                            <div class="card-header card-no-border total-revenue">
                                <h4>Stock Report</h4>
                            </div>
                            <div class="card-body pt-0">
                                <div class="">

                                <table class="dt-responsive table table-bordered dataTable dtr-column" id="example" aria-describedby="DataTables_Table_3_info">


                                                            <thead>
                                                            <tr>
                                        <th rowspan="2">Sr.No.</th>
                                        <th  rowspan="2">Product</th>
                                        <th colspan="3">Raw</th> 
                                        <th colspan="3">Semi Ripe</th> 
                                        <th colspan="3">Ripe</th> 

                                    </tr>
                                    <tr>
                                    
                                        <th>Purchase</th> 
                                        <th>Sale</th>
                                        <th>Stock</th>
                                        <th>Purchase</th> 
                                        <th>Sale</th>
                                        <th>Stock</th>
                                        <th>Purchase</th> 
                                        <th>Sale</th>
                                        <th>Stock</th>
                                    </tr>

                                            </thead>
                                          <tbody>
                                @php
                                
                            $k=1;
                            $records = DB::table('product_details')
                            ->select('parentID', 'id','product_size')
                            ->groupBy('parentID', 'id')
                            ->get();

                                    $Date = date('Y-m-d');
                                @endphp

                                @foreach ($records as $record)
                                    @php
                                        // Set variables for services and size
                                        $service = $record->parentID;
                                        $size = $record->id;
                                        $sizename = $record->product_size;

                                        // Fetch product name from the products table for each service
                                        $product = DB::table('products')->where('id', $service)->first();
                                        $productName = $product ? $product->product_name : 'Unknown';
                                       

                                  $pquantityraw = DB::table('purchase_product')
                                    ->join('purchase_details', 'purchase_details.id', '=', 'purchase_product.pid')
                                    ->where('purchase_product.services', $service)
                                    ->where('purchase_product.size',$size)
                                    ->where('purchase_product.stage', "Raw")
                                     ->where('purchase_details.season', $season)
                                    ->whereDate('purchase_details.PurchaseDate', '<=', $Date)
                                    ->sum('purchase_product.Quantity');


                                    $pquantitysemiripe = DB::table('purchase_product')
                                    ->join('purchase_details', 'purchase_details.id', '=', 'purchase_product.pid')
                                    ->where('purchase_product.services', $service)
                                    ->where('purchase_product.size',$size)
                                    ->where('purchase_product.stage', "Semi Ripe")
                                    ->where('purchase_details.season', $season)
                                    ->whereDate('purchase_details.PurchaseDate', '<=', $Date)
                                    ->sum('purchase_product.Quantity');


                                    $pquantityripe = DB::table('purchase_product')
                                    ->join('purchase_details', 'purchase_details.id', '=', 'purchase_product.pid')
                                    ->where('purchase_product.services', $service)
                                    ->where('purchase_product.size',$size)
                                    ->where('purchase_product.stage', "Ripe")
                                     ->where('purchase_details.season', $season)
                                    ->whereDate('purchase_details.PurchaseDate', '<=', $Date)
                                    ->sum('purchase_product.Quantity');



                                    $squantityraw = DB::table('sale_order')
                                    ->join('sale_orderdetails', 'sale_orderdetails.id', '=', 'sale_order.pid')
                                    ->where('sale_order.services', $service)
                                    ->where('sale_order.size',$size)
                                    ->where('sale_order.stage', "Raw")
                                     ->where('sale_orderdetails.season', $season)
                                    ->whereDate('sale_orderdetails.order_date', '<=', $Date)
                                    ->sum('sale_order.qty');


                                    $squantitysemiripe = DB::table('sale_order')
                                    ->join('sale_orderdetails', 'sale_orderdetails.id', '=', 'sale_order.pid')
                                    ->where('sale_order.services', $service)
                                    ->where('sale_order.size',$size)
                                    ->where('sale_order.stage', "Semi Ripe")
                                    ->where('sale_orderdetails.season', $season)
                                    ->whereDate('sale_orderdetails.order_date', '<=', $Date)
                                    ->sum('sale_order.qty');


                                    $squantityripe = DB::table('sale_order')
                                    ->join('sale_orderdetails', 'sale_orderdetails.id', '=', 'sale_order.pid')
                                    ->where('sale_order.services', $service)
                                    ->where('sale_order.size',$size)
                                    ->where('sale_order.stage', "Ripe")
                                    ->where('sale_orderdetails.season', $season)
                                    ->whereDate('sale_orderdetails.order_date', '<=', $Date)
                                    ->sum('sale_order.qty');


                                   $raw = \App\Helpers\Helpers::getstockbatch($service, $size, "Raw", $Date);
                                   $semi = \App\Helpers\Helpers::getstockbatch($service, $size, "Semi Ripe", $Date);
                                   $ripe = \App\Helpers\Helpers::getstockbatch($service, $size, "Ripe", $Date);



                                    @endphp

<?php                                   // if($pquantityraw !=0 && $pquantitysemiripe !=0 && $pquantityripe !=0 &&  $squantityraw !=0 && $squantitysemiripe !=0 && $squantityripe !=0 && $raw !=0 && $semi !=0 && $ripe!=0){
 ?>
                                    <tr>
                                        <td>{{ $k++ }}</td>
                                        <td style="text-align:left;">{{ $productName }} -  {{ $sizename }}</td>  <!-- Display product name and size -->
                                        <td>
                                          {{ $pquantityraw }}
                                        </td>
                                        <td>
                                        {{ $squantityraw }}
                                        </td>
                                        <td style="background-color: #e0f7fa; border: 1px solid #81d4fa; box-shadow: 0px 0px 5px #81d4fa; padding: 5px;">
                                        {{ $raw }}
                                       </td>
                                        <td>
                                        {{ $pquantitysemiripe }}
                                        </td>
                                        <td>
                                            {{ $squantitysemiripe }}
                                        </td>
                                        <td style="background-color: #e0f7fa; border: 1px solid #81d4fa; box-shadow: 0px 0px 5px #81d4fa; padding: 5px;">
                                        {{ $semi }}
                                        </td>
                                        <td>
                                        {{ $pquantityripe }}
                                        </td>
                                        <td>
                                            {{ $squantityripe }}
                                        </td>
                                        <td style="background-color: #e0f7fa; border: 1px solid #81d4fa; box-shadow: 0px 0px 5px #81d4fa; padding: 5px;">
                                        {{ $ripe }}
                                        </td>
                                    </tr>
                                <?php //} ?>
                                @endforeach
                            </tbody>


                            </table>


                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class="col-md-6">
                        @php

                        // Get last year's start date (June 1st of the last year)
                        $last_year = date('Y', strtotime('last year'));
                        $start_date = $last_year . '-06-01'; // 2024-06-01 if last year was 2024

                        // Get this year's end date (May 31st of the current year)
                        $end_date = date('Y') . '-05-31'; // 2025-05-31 if current year is 2025

                        // Get order count for each month within the specified range
                        $data = DB::table('sale_orderdetails')
                        ->select(DB::raw('MONTHNAME(order_date) as month_name'), DB::raw('COUNT(id) as order_count'))
                        ->where('season', $season)
                        ->groupBy(DB::raw('MONTH(order_date), MONTHNAME(order_date)')) // Group by month and month name
                        ->orderBy(DB::raw('MONTH(order_date)')) // Order by month number
                        ->get();
                        @endphp



                        <div class="card">
                            <div class="card-header card-no-border total-revenue">
                                <h4>Sale Order Count</h4>
                            </div>
                            <div class="card-body pt-0">
                                <div style="width: 60%; margin: auto;">
                                <canvas id="salesPieChart" width="400" height="400"></canvas>
                            </div>

                            </div>
                        </div>
                    </div>


                    <div class="col-md-6">
                    @php
    // Get last year's start date (June 1st of the last year)
    $last_year = date('Y', strtotime('last year'));
    $start_date = $last_year . '-06-01'; // 2024-06-01 if last year was 2024

    // Get this year's end date (May 31st of the current year)
    $end_date = date('Y') . '-05-31'; // 2025-05-31 if current year is 2025

    $data44 = DB::table('purchase_details')
                        ->where('season', $season)
        ->sum('Tquantity');  // Sum of Tquantity for the filtered records (Investment in Purchase)

    $data45 = DB::table('expense')
                        ->where('season', $season)
        ->sum('amt_pay');  // Sum of expenses for the filtered records (Investment in Expenses)

    $data46 = DB::table('purchase_payments')
                        ->where('season', $season)
 ->select(DB::raw('SUM(COALESCE(amt_pay, 0) + COALESCE(cheque_amt, 0)) as total_amount'))
        ->value('total_amount');
        
    // Calculate Total Investment and Profit/Loss
    $total_investment = $data44 + $data45;
    $profit_or_loss = $data46 - $total_investment;
    $is_profit = $profit_or_loss >= 0;
@endphp

<div class="col-md-12">
    <div class="card">
        <div class="card-header card-no-border total-revenue">
            <h4>Profit/Loss</h4>
        </div>
        <div class="card-body pt-0">
            <div style="width: 60%; margin: auto;">
                <canvas id="salesPieChartt" width="300" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

</div>

  <div class="row">



<div class="col-md-6">



                     @php
 $last_year = date('Y', strtotime('last year'));
 $start_date = $last_year . '-06-01'; // Example: 2024-06-01
 $end_date = date('Y') . '-05-31'; // Example: 2025-05-31

 // Fetching data for products and categorizing them into 'Raw', 'Semi Ripe', 'Ripe'
 $data2 = DB::table('purchase_product')
     ->join('products', 'products.id', '=', 'purchase_product.services')  // Join products table
     ->join('purchase_details', 'purchase_details.id', '=', 'purchase_product.pid')  // Join products table
     ->select('products.product_name as product_name', 'purchase_product.stage', DB::raw('SUM(purchase_product.Quantity) as Quantity'))
     ->where('purchase_details.season',$season )
     ->groupBy('products.product_name', 'purchase_product.stage')
     ->get();

 // Organizing data into categories
 $categories = ['Raw', 'Semi Ripe', 'Ripe'];
 $products_data = [];

 foreach ($data2 as $item) {
     if (!isset($products_data[$item->product_name])) {
         $products_data[$item->product_name] = [
             'Raw' => 0,
             'Semi Ripe' => 0,
             'Ripe' => 0,
         ];
     }

     // Sum the total amount by stage
     $products_data[$item->product_name][$item->stage] = $item->Quantity;
 }

 // Prepare chart data
 $labelsss = [];
 $datasetsss = [];

 foreach ($products_data as $product_name => $categories_data) {
     $labelsss[] = $product_name;
     $datasetsss1['Raw'][] = $categories_data['Raw'];
     $datasetsss2['Semi Ripe'][] = $categories_data['Semi Ripe'];
     $datasetsss3['Ripe'][] = $categories_data['Ripe'];
 }

 @endphp

     <div class="card">
     <div class="card-header card-no-border total-revenue">
         <h4>Categorywise Total Count Chart</h4>
     </div>
     <div class="card-body pt-0">
         <div style="width: 60%; margin: auto;">
             <canvas id="category" width="400" height="400"></canvas>
         </div>
     </div>
</div>

     </div>

     <div class="col-md-6">
    @php
        $last_year1 = date('Y', strtotime('last year'));
        $start_date1 = $last_year1 . '-06-01'; // Example: 2024-06-01
        $end_date1 = date('Y') . '-05-31'; // Example: 2025-05-31

        // Fetching data for products and categorizing them into 'Raw', 'Semi Ripe', 'Ripe'
        $data3 = DB::table('sale_order')
            ->join('products', 'products.id', '=', 'sale_order.services')  // Join products table
             ->join('sale_orderdetails', 'sale_orderdetails.id', '=', 'sale_order.pid')  // Join products table
            ->select('products.product_name as product_name', 'sale_order.stage', DB::raw('SUM(sale_order.qty) as qty'))
            ->where('sale_orderdetails.season', $season)
            ->groupBy('products.product_name', 'sale_order.stage')
            ->get();

        // Organizing data into categories
        $categories1 = ['Raw', 'Semi Ripe', 'Ripe'];
        $products_data1 = [];

        foreach ($data3 as $item1) {
            if (!isset($products_data1[$item1->product_name])) {
                $products_data1[$item1->product_name] = [
                    'Raw' => 0,
                    'Semi Ripe' => 0,
                    'Ripe' => 0,
                ];
            }

            // Sum the total amount by stage
            $products_data1[$item1->product_name][$item1->stage] = $item1->qty;
        }

        // Prepare chart data
        $labels1 = [];
        $dataset1 = ['Raw' => [], 'Semi Ripe' => [], 'Ripe' => []];

        foreach ($products_data1 as $product_name => $categories_data1) {
            $labels1[] = $product_name;
            $dataset1['Raw'][] = $categories_data1['Raw'];
            $dataset1['Semi Ripe'][] = $categories_data1['Semi Ripe'];
            $dataset1['Ripe'][] = $categories_data1['Ripe'];
        }
    @endphp

    <div class="card">
        <div class="card-header card-no-border total-revenue">
            <h4>Categorywise Total Sale Chart</h4>
        </div>
        <div class="card-body pt-0">
            <div style="width: 60%; margin: auto;">
                <canvas id="category1" width="400" height="400"></canvas>
            </div>
        </div>
    </div>
</div>
    </div>



    <div class="row">

<div class="col-md-6">


@php
$last_year = date('Y', strtotime('last year'));

$start_date = $last_year . '-06-01'; // 2024-06-01 if last year was 2024

$end_date = date('Y') . '-05-31'; // 2025-05-31 if current year is 2025

$data1 = DB::table('expense')
    ->join('subjects', 'expense.exp_type', '=', 'subjects.id') // Assuming exp_type is the ID in the expense table that links to subjects
    ->select('subjects.subject_name', DB::raw('SUM(amt_pay) as total_amount'))
     ->where('expense.season', $season)
    ->groupBy('subjects.subject_name') // Group by the subject name
    ->get();

$labels = $data1->pluck('subject_name')->toArray(); // Get the subject names
$values = $data1->pluck('total_amount')->toArray(); // Get the total amounts
@endphp


    <div class="card">
        <div class="card-header card-no-border total-revenue">
            <h4>Expense Chart</h4>
        </div>
        <div class="card-body pt-0">
            <div style="width: 60%; margin: auto;">
                <canvas id="expense" width="400" height="400"></canvas>
            </div>
        </div>
    </div>
</div>

            </div>

    </div>
                </div>
            </div>
        </div>

    <!-- Container-fluid Ends-->
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
    <script src="{{ asset('assets/js/chart/apex-chart/stock-prices.js') }}"></script>
    <!-- Range Slider js-->
    <script src="{{ asset('assets/js/range-slider/rSlider.min.js') }}"></script>
    <script src="{{ asset('assets/js/rangeslider/rangeslider.js') }}"></script>
    <script src="{{ asset('assets/js/prism/prism.min.js') }}"></script>
    <script src="{{ asset('assets/js/clipboard/clipboard.min.js') }}"></script>
    <script src="{{ asset('assets/js/counter/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/js/counter/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('assets/js/counter/counter-custom.js') }}"></script>
    <script src="{{ asset('assets/js/custom-card/custom-card.js') }}"></script>
    <!-- calendar js-->
    <script src="{{ asset('assets/js/calendar/fullcalender.js') }}"></script>
    <script src="{{ asset('assets/js/calendar/custom-calendar.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard/dashboard_2.js') }}"></script>
    <script src="{{ asset('assets/js/animation/wow/wow.min.js') }}"></script>
    <script>
        new WOW().init();
    </script>


     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <script>
        document.addEventListener("DOMContentLoaded", function () {
            const data = @json($data); // Pass PHP data to JavaScript
            const labels = data.map(item => item.month_name);
            const values = data.map(item => item.order_count);

            // Create Pie Chart
            const ctx = document.getElementById('salesPieChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Orders by Month',
                        data: values,
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF',
                            '#FF9F40',
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF',
                            '#FF9F40'
                        ]
                    }]
                }
            });
        });
    </script>



<script>
    var ctx = document.getElementById('salesPieChartt').getContext('2d');
    var profit = @json($is_profit ? $profit_or_loss : 0);
    var loss = @json(!$is_profit ? abs($profit_or_loss) : 0);

    var salesPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Profit', 'Loss'],
            datasets: [{
                label: 'Profit/Loss',
                data: [profit, loss],
                backgroundColor: ['#4CAF50', '#F44336'], // Green for Profit, Red for Loss
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
            },
        }
    });
</script>
    <!-- jQuery -->
<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
  $(document).ready(function() {
    $('#example').DataTable({
      "responsive": true,  // Ensures responsiveness
      "paging": true,      // Enables paging
      "searching": true,   // Enables searching
      "ordering": true,    // Enables column sorting
      "pageLength": 20,   // Show 100 entries per page
      "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]], // Dropdown options for entries per page
      "columnDefs": [{
        "orderable": false,
        "targets": [0]  // Disable sorting on the first column (Sr. No.)
      }]
    });
  });
</script>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('expense').getContext('2d');
        const labels = {!! json_encode($labels) !!}; // Dynamic labels from PHP
        const values = {!! json_encode($values) !!}; // Dynamic values from PHP

        const expenseChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Amount Paid',
                    data: values,
                    backgroundColor: [
                        'rgb(196, 36, 36)', // Dark Red
                        'rgb(22, 22, 174)', // Dark Blue
                        'rgb(144, 144, 11)', // Olive
                        'rgb(20, 165, 20)', // Dark Green
                        'rgba(75, 0, 130, 1)', // Indigo
                        'rgba(139, 69, 19, 1)' // Saddle Brown
                    ],
                    borderColor: [
                        'rgb(206, 59, 59)',
                        'rgb(17, 17, 163)',
                        'rgb(133, 133, 10)',
                        'rgb(25, 205, 25)',
                        'rgb(112, 22, 176)',
                        'rgb(188, 99, 35)'
                    ],
                    borderWidth: 2,
                    borderRadius: 8, // Rounded corners
                    hoverBackgroundColor: [
                        'rgba(196, 23, 23, 0.8)', // Dark Red on hover
                        'rgba(0, 0, 139, 0.8)', // Dark Blue on hover
                        'rgba(128, 128, 0, 0.8)', // Olive on hover
                        'rgba(0, 100, 0, 0.8)', // Dark Green on hover
                        'rgba(75, 0, 130, 0.8)', // Indigo on hover
                        'rgba(139, 69, 19, 0.8)' // Saddle Brown on hover
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: '#333', // Darker legend text
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)', // Dark tooltip background
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        padding: 10,
                        cornerRadius: 5
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount (in currency)',
                            color: '#333',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            color: 'rgba(200, 200, 200, 0.3)', // Light gridlines
                        },
                        ticks: {
                            color: '#333',
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Expense Types',
                            color: '#333',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: false // Remove vertical gridlines
                        },
                        ticks: {
                            color: '#333',
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeOutBounce' // Bouncy animation for loading bars
                }
            }
        });
    });
</script>


<script>
    var ctx = document.getElementById('category').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar',  // Choose the chart type (bar, line, etc.)
        data: {
            labels: @json($labelsss),  // Correctly reference labels
            datasets: [
                {
                    label: 'Raw',
                        @if(isset($datasetsss1['Raw']))
                            data: @json($datasetsss1['Raw']),
                        @else
                            data: null  // or some fallback value
                        @endif
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',  // More vibrant color
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    hoverBackgroundColor: 'rgba(255, 99, 132, 0.8)',  // Hover effect
                    hoverBorderColor: 'rgba(255, 99, 132, 1)'
                },
                {
                    label: 'Semi Ripe',
                @if(isset($datasetsss2['Semi Ripe']))
                    data: @json($datasetsss2['Semi Ripe']),
                @else
                    data: null  // Or some fallback value if 'Semi Ripe' doesn't exist
                @endif
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',  // More vibrant color
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    hoverBackgroundColor: 'rgba(54, 162, 235, 0.8)',  // Hover effect
                    hoverBorderColor: 'rgba(54, 162, 235, 1)'
                },
                {
                    label: 'Ripe',
            @if(isset($datasetsss3['Ripe']))
                data: @json($datasetsss3['Ripe']),
            @else
                data: null  // Fallback value if 'Ripe' is not set
            @endif
                    backgroundColor: 'rgba(255, 159, 64, 0.6)',  // More vibrant color
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 2,
                    hoverBackgroundColor: 'rgba(255, 159, 64, 0.8)',  // Hover effect
                    hoverBorderColor: 'rgba(255, 159, 64, 1)'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 14,
                            family: "'Arial', sans-serif",
                            weight: 'bold',
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(255, 255, 255, 0.5)',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(255, 255, 255, 0.2)',  // Lighter grid lines for a cleaner look
                    },
                    ticks: {
                        font: {
                            size: 12,
                            family: "'Arial', sans-serif",
                        }
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 12,
                            family: "'Arial', sans-serif",
                        }
                    }
                }
            },
            animation: {
                duration: 1000,  // Animation duration
                easing: 'easeOutBounce'  // Smooth bouncing effect
            }
        }
    });
</script>
<script>
    var ctx = document.getElementById('category1').getContext('2d');

    // Define dark colors for the bars
    var gradient1 = ctx.createLinearGradient(0, 0, 0, 400);
    gradient1.addColorStop(0, 'rgba(139, 0, 0, 1)'); // Dark Red
    gradient1.addColorStop(1, 'rgba(255, 0, 0, 1)'); // Solid Red

    var gradient2 = ctx.createLinearGradient(0, 0, 0, 400);
    gradient2.addColorStop(0, 'rgba(0, 0, 139, 1)'); // Dark Blue
    gradient2.addColorStop(1, 'rgba(0, 0, 255, 1)'); // Solid Blue

    var gradient3 = ctx.createLinearGradient(0, 0, 0, 400);
    gradient3.addColorStop(0, 'rgba(0, 100, 0, 1)'); // Dark Green
    gradient3.addColorStop(1, 'rgba(0, 128, 0, 1)'); // Solid Green

    var categoryChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labels1),
            datasets: [
                {
                    label: 'Raw',
                    data: @json($dataset1['Raw']),
                    backgroundColor: gradient1,
                    borderColor: 'rgba(139, 0, 0, 1)', // Match dark red
                    borderWidth: 1,
                    borderRadius: 5
                },
                {
                    label: 'Semi Ripe',
                    data: @json($dataset1['Semi Ripe']),
                    backgroundColor: gradient2,
                    borderColor: 'rgba(0, 0, 139, 1)', // Match dark blue
                    borderWidth: 1,
                    borderRadius: 5
                },
                {
                    label: 'Ripe',
                    data: @json($dataset1['Ripe']),
                    backgroundColor: gradient3,
                    borderColor: 'rgba(0, 100, 0, 1)', // Match dark green
                    borderWidth: 1,
                    borderRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        color: '#333'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    titleFont: {
                        size: 16,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 14
                    },
                    padding: 10,
                    cornerRadius: 5,
                    displayColors: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 12
                        },
                        color: '#555'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(200, 200, 200, 0.2)',
                        borderDash: [5, 5]
                    },
                    ticks: {
                        font: {
                            size: 12
                        },
                        color: '#555'
                    }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeOutBounce'
            }
        }
    });
</script>

@endsection
