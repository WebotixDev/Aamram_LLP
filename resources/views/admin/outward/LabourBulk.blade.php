<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Labour Bill Print</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      font-size: 12px;
      line-height: 1.2;
    }
    h2, h3, p {
      margin: 0;
      padding: 0;
    }
    .header {
      text-align: center;
      padding: 5px 0;
      margin-bottom: 5px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 5px;
      table-layout: fixed;
    }
    th, td {
      padding: 5px;
      border: 1px solid black;
      text-align: left; /* Left-aligned text */
      width: 25%;
      word-wrap: break-word;
    }
    th:nth-child(1), td:nth-child(1) { width: 10%; }  /* SR.No Column */
    th:nth-child(2), td:nth-child(2) { width: 30%; }  /* Product Name */
    th:nth-child(3), td:nth-child(3) { width: 30%; }  /* Size */
    th:nth-child(4), td:nth-child(4) { width: 20%; }  /* Quantity */

    .details-section {
      margin-top: 5px;
    }
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
    @media print {
      * {
        margin: 0;
        padding: 0;
        font-size: 10px;
        line-height: 1.1;
      }
      body {
        margin: 0;
        padding: 0;
      }
      @page {
        size: A5;
        margin: 5mm;
      }
      .header {
        text-align: center;
        padding: 3px 0;
        margin-bottom: 3px;
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
        padding: 3px;
        border: 1px solid black;
        text-align: left; /* Left-aligned text */
    }
      .print-btn, .cancel-btn {
        display: none;
      }
    }
  </style>
</head>
<body>
  <?php
  use Illuminate\Support\Facades\DB;

  $company = DB::table('company')->first();
  $logo = DB::table('company')->value('logo');
  ?>
  <button class="cancel-btn" onclick="window.close()">✖</button>
  <div class="header">
    <div class="logo">
      <img src="<?php echo asset('public/' . $logo) ?>" style="height: 60px; width: auto;" alt="Company Logo">
    </div>
    <h2 class="pt-3">Labour Bill</h2>
    <button class="print-btn" onclick="window.print()">Print</button>
  </div>

  @if($saleOrders->isEmpty())
    <p>No records found for the selected date range.</p>
  @else
    <div class="details-section">
      <table>
        <thead>
          <tr>
            <th>SR.No</th>
            <th>Customer Name</th>
            <th>Product Name</th>
            <th>Size</th>
            <th>Quantity</th>
          </tr>
        </thead>
        <tbody>
          @foreach($saleOrders as $index => $saleOrder)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>{{ $saleOrder->customer_name }}</td>
              <td>{{ $saleOrder->product_name }}</td>
              <td>{{ $saleOrder->product_size }}</td>
              <td>{{ $saleOrder->currdispatch_qty }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</body>
</html>
