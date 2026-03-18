<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Customer Bill Print</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      margin: 0;
      padding: 0;
      font-size: 12px;
      color: #000;
      background: #fff;
    }

    h2, h3, h4, p {
      margin: 0;
      padding: 0;
    }

    .header {
      text-align: center;
      padding: 10px 0 5px;
      border-bottom: 1px solid #333;
      margin-bottom: 10px;
    }

    .logo img {
      max-height: 60px;
      width: auto;
      margin-bottom: 5px;
    }

    .print-btn {
      margin-top: 8px;
      padding: 5px 12px;
      background-color: #000;
      color: #fff;
      border: none;
      font-size: 12px;
      cursor: pointer;
      border-radius: 2px;
    }

    .print-btn:hover {
      background-color: #333;
    }

    .cancel-btn {
      position: absolute;
      top: 8px;
      right: 8px;
      background-color: #b30000;
      color: white;
      border: none;
      font-size: 14px;
      cursor: pointer;
      width: 28px;
      height: 28px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .cancel-btn:hover {
      background-color: #800000;
    }

    .details-section {
      display: flex;
      justify-content: space-between;
      padding: 10px;
      font-size: 12px;
      border: 1px solid #ccc;
      margin: 10px;
    }

    .company-details {
      width: 48%;
      line-height: 1.5;
    }

    h4 {
      font-size: 13px;
      font-weight: normal;
      margin: 12px 10px 4px;
      padding-left: 4px;
      border-left: 2px solid #333;
    }

    table {
      width: 96%;
      margin: 8px auto;
      border-collapse: collapse;
      font-size: 11px;
    }

    th, td {
      border: 1px solid #333;
      padding: 5px;
      text-align: left;
    }

    th {
      background-color: #eaeaea;
    }

    tfoot th {
      font-weight: bold;
      background: #f0f0f0;
    }

    tfoot th:last-child {
      background-color: #ffff99;
    }

    hr {
      border: 0;
      border-top: 1px dashed #aaa;
      margin: 25px 10px;
    }

    @media print {
      * {
        margin: 0;
        padding: 0;
        font-size: 10px;
      }

      @page {
        size: A4;
        margin: 5mm;
      }

      .print-btn, .cancel-btn {
        display: none !important;
      }

      .details-section {
        border: none;
      }

      th {
        background-color: #ddd !important;
      }
      tfoot th {
      font-weight: bold;
      background: #f0f0f0;
    }

    tfoot th:last-child {
      background-color: #ffff99;
    }

    hr {
      border: 0;
      border-top: 1px dashed #aaa;
      margin: 25px 10px;
    }
      h4 {
        margin: 8px 0 4px;
      }
    }
  </style>

</head>
<body>
    <?php
use Illuminate\Support\Facades\DB;

// Fetch company details
$company = DB::table('company')->first();
$logo = DB::table('company')->value('logo');
?>
  <button class="cancel-btn" onclick="window.close()">✖</button>
  <div class="header">
    <div class="logo">
        <img src="<?php echo asset('public/' . $logo) ?>" style="height: 60px; width: auto;" alt="Company Logo">
    </div>
    <h2 class="pt-3">Customer Bill</h2>
    <button class="print-btn" onclick="window.print()">Print</button>
  </div>

  @if($groupedOrdersByCustomer->isEmpty())
    <p>No records found for the selected date range.</p>
  @else

@foreach($groupedOrdersByCustomer as $customer_id => $orders)
  <div class="details-section">
    <div class="company-details">
        <strong>Name:</strong> {{ $company->name }}<br>
        <strong>Address:</strong> {{ $company->address }}<br>
        <strong>Contact:</strong> {{ $company->phone }}
    </div>
    <div class="company-details">
      <h3>Customer Details</h3>
      <p><strong>Name:</strong> {{ $orders->first()->customer_name }}</p>
      <p><strong>Contact:</strong> {{ $orders->first()->mobile_no }}</p>
      <p>
        <strong>Address:</strong>
        <!--{{ $orders->first()->address }}, {{ $orders->first()->city_name }},-->
        <!--{{ $orders->first()->district_id }}, {{ $orders->first()->state_name }}-->
        
        {{ $orders->first()->address }}, {{ $orders->first()->state_name }}

      </p>
    </div>
  </div>
  @php
  $grandTotal = 0;
  $grandPaid = 0;
  $grandDue = 0;
@endphp
  @foreach($orders as $saleOrder)

  @php
  $summary = $paymentSummary[$saleOrder->pid] ?? ['paid' => 0, 'due' => 0];
  $grandTotal += $saleOrder->Tamount;
  $grandPaid += $summary['paid'];
  $grandDue += $summary['due'];
@endphp
<h4>Order ID: {{ $saleOrder->pid }} | Bill Date: {{ \Carbon\Carbon::parse($saleOrder->billdate)->format('d-m-Y') }}</h4>

    <table>
      <thead>
        <tr>
            <th>SR.No</th>
            <th>Product</th>
            <th>Size</th>
            <th>Qty</th>
            <th>Price</th>
            <th>GST</th>
            <th>Trans Cost</th>
            <th>Total</th>

        </tr>
      </thead>
      <tbody>
        @php $rowCount = 1; @endphp
        @if(isset($groupedDetails[$saleOrder->pid]) && $groupedDetails[$saleOrder->pid]->isNotEmpty())
          @foreach($groupedDetails[$saleOrder->pid] as $detail)
            <tr>
              <td>{{ $rowCount++ }}</td>
              <td>{{ $detail->product_name}}</td>
              <td>{{  $detail->product_size}}</td>
              <td>{{ $detail->qty}}</td>
              <td>{{ $detail->rate}}</td>
              <td>{{ $detail->gstper}}</td>
              <td>{{ $detail->transper}}</td>
              <td>{{ $detail->amount}}</td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="4">No product details available for this order.</td>
          </tr>
        @endif
      </tbody>

<tfoot>
    @php
  $summary = $paymentSummary[$saleOrder->pid] ?? ['paid' => 0, 'due' => 0];
@endphp

    <tr>
        <th colspan="7" style="text-align: right;">Total</th>
        <th style="background-color: yellow">{{ number_format($saleOrder->Tamount, 2) }}</th>
    </tr>
    <tr>
        <th colspan="7" style="text-align: right;">Paid</th>
        <th style="background-color: #d4edda">{{ number_format($summary['paid'], 2) }}</th>
    </tr>
    <tr>
        <th colspan="7" style="text-align: right;">Due</th>
        <th style="background-color: #f8d7da">{{ number_format($summary['due'], 2) }}</th>
    </tr>
  </tfoot>

    </table>
    <br>
  @endforeach

  <hr>
  <h3>All Orders Summary</h3>
  <table border="1" cellpadding="5" cellspacing="0" width="100%">
    <tfoot>
      <tr>
        <th colspan="7" style="text-align: right;">Grand Total</th>
        <th style="background-color: yellow">{{ number_format($grandTotal, 2) }}</th>
      </tr>
      <tr>
        <th colspan="7" style="text-align: right;">Total Paid</th>
        <th style="background-color: #d4edda">{{ number_format($grandPaid, 2) }}</th>
      </tr>
      <tr>
        <th colspan="7" style="text-align: right;">Total Due</th>
        <th style="background-color: #f8d7da">{{ number_format($grandDue, 2) }}</th>
      </tr>
    </tfoot>
  </table>
  <hr style="border: 0; border-top: 1px dashed #464343; margin: 20px 0;">
@endforeach

  @endif
  
   @if(isset($wholesalerGrand))
  <h2 style="text-align:center; margin-top: 30px;"> Grand Summary</h2>

  <table style="width: 60%; margin: auto; font-size: 13px;">
    <thead>
      <tr>
        <th colspan="2" style="text-align:center; background-color: #f2f2f2;">Overall  Summary</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="text-align:right;"><strong>Total Amount:</strong></td>
        <td style="background-color: yellow;">{{ number_format($wholesalerGrand['total'], 2) }}</td>
      </tr>
      <tr>
        <td style="text-align:right;"><strong>Total Paid:</strong></td>
        <td style="background-color: #d4edda;">{{ number_format($wholesalerGrand['paid'], 2) }}</td>
      </tr>
      <tr>
        <td style="text-align:right;"><strong>Total Due:</strong></td>
        <td style="background-color: #f8d7da;">{{ number_format($wholesalerGrand['due'], 2) }}</td>
      </tr>
    </tbody>
  </table>
@endif

</body>
</html>
