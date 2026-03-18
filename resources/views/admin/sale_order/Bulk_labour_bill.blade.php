<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Labour Bill Print</title>
  <style>
    /* Overall body styles: removed extra margin/padding and set a smaller font size */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      font-size: 12px; /* Extra small font size */
      line-height: 1.2;
    }
    /* Remove spacing from headings and paragraphs */
    h2, h3, p {
      margin: 0;
      padding: 0;
    }
    /* Header styling: reduced margins and paddings */
    .header {
      text-align: center;
      padding: 5px 0;
      /* border-bottom: 1px solid #000; */
      margin-bottom: 5px;
    }
    /* Table styles with reduced spacing */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 5px;
    }
    table, th, td {
      border: 1px solid black;
    }
    th, td {
      padding: 5px;
      text-align: left;
    }
    /* Details section: reduce spacing */
    .details-section {
      margin-top: 5px;
    }
    .company-details p {
      margin: 2px 0;
    }
    /* Print button: reduce padding and margin */
    .print-btn {
      margin: 5px 0;
      padding: 5px 10px;
      background-color: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
      font-size: 12px;
    }
    .print-btn:hover {
      background-color: #45a049;
    }
    /* Cancel button: slightly reduced size */
    .cancel-btn {
      position: absolute;
      top: 5px;
      right: 5px;
      background-color: red;
      color: white;
      border: none;
      font-size: 16px;
      cursor: pointer;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .cancel-btn:hover {
      background-color: darkred;
    }
    /* Print media: remove extra margins */
    @media print {
    * {
        margin: 0;
        padding: 0;
        font-size: 10px; /* Extra small font size for print */
        line-height: 1.1;
    }
    body {
        margin: 0;
        padding: 0;
    }
    @page {
        size: A5;
        margin: 5mm; /* Reduce margin for compact print */
    }
    .header {
        text-align: center;
        padding: 3px 0;
        margin-bottom: 3px;
        /* border-bottom: 1px solid #000; */
    }
    h2, h3, p {
        margin: 0;
        padding: 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 3px;
    }
    th, td {
        padding: 3px; /* Reduce padding inside table */
        border: 1px solid black;
        text-align: left;
    }
    .print-btn, .cancel-btn {
        display: none; /* Hide buttons on print */
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
    <h2 class="pt-3">Labour Bill</h2>
    <p>From: {{ $from_date }} &nbsp;&nbsp;&nbsp; To: {{ $to_date }}</p>
    <button class="print-btn" onclick="window.print()">Print</button>
  </div>

  @if($uniqueSaleOrders->isEmpty())
    <p>No records found for the selected date range.</p>
  @else
    @foreach($uniqueSaleOrders as $saleOrder)
      <div class="details-section">
        <div class="company-details">
          <h3>Customer Details</h3>
          <p><strong>Name:</strong> {{ $saleOrder->customer_name }}</p>
          <p><strong>Contact:</strong> {{ $saleOrder->mobile_no }}</p>
          <p>
            <strong>Address:</strong>
          <!--{{ $saleOrder->address }}, {{ $saleOrder->city_name }}, {{ $saleOrder->district_id }}, {{ $saleOrder->state_name }}-->

            {{ $saleOrder->order_address }}, {{ $saleOrder->state_name }}
          </p>
          <p><strong>Order Date:</strong> {{ $saleOrder->billdate }}</p>
        </div>
      </div>

      <!-- Details Table -->
      <table>
        <thead>
          <tr>
            <th>SR.No</th>
            <th>Product Name</th>
            <th>Size</th>
            <th>Quantity</th>
          </tr>
        </thead>
        <tbody>
          @if(isset($groupedDetails[$saleOrder->pid]) && $groupedDetails[$saleOrder->pid]->isNotEmpty())
            @foreach($groupedDetails[$saleOrder->pid] as $index => $detail)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $detail->product_name }}</td>
                <td>{{ $detail->product_size }}</td>
                <td>{{ $detail->qty }}</td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="4">No details available.</td>
            </tr>
          @endif
        </tbody>
      </table>
<br>
    @endforeach
  @endif
</body>
</html>
