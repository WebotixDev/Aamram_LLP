<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Farm Receipt Print</title>
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
      table-layout: fixed; /* Fix column width */
    }
    th, td {
      padding: 5px;
      border: 1px solid black;
      text-align: left;
      word-wrap: break-word;
    }
  th:nth-child(1), td:nth-child(1) { width: 10%; }  /* SR.No Column */
    th:nth-child(2), td:nth-child(2) { width: 25%; }  /* Product Name */
    th:nth-child(3), td:nth-child(3) { width: 25%; }  /* Size */
    th:nth-child(4), td:nth-child(4) { width: 20%; }  /* Quantity */
    th:nth-child(4), td:nth-child(5) { width: 20%; }  /* Quantity */
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
    <img src="{{ asset('public/' . $logo) }}" style="height: 60px; width: auto;" alt="Company Logo">
    <h2>Labour Receipt</h2>
    <p>From: {{ $from_date }} &nbsp;&nbsp;&nbsp; To: {{ $to_date }}</p>

    <button class="print-btn" onclick="window.print()">Print</button>
    <button class="print-btn" onclick="exportAllTablesToExcel()">Export to Excel</button>
</div>

  @if($saleOrders->isEmpty())
    <p>No records found for the selected date range.</p>
  @else
    @foreach($saleOrders as $serviceId => $orders)
    <br>
      <h3>Product : {{ $orders->first()->product_name }}</h3>
       <!-- Display service type -->

      <table>
        <thead>
          <tr>
            <th>SR.No</th>
             <th>Customer Name</th>
            <th>Product Name</th>
            <th>Size</th>
            <th>Stage</th>
            <th>Quantity</th>
          </tr>
        </thead>
        <tbody>
          @foreach($orders as $index => $saleOrder)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>{{ $saleOrder->customer_name }} <br> {{ $saleOrder->order_address}}</td>
              <td>{{ $saleOrder->product_name }}</td>
              <td>{{ $saleOrder->product_size }}</td>
              <td>{{ $saleOrder->stage }}</td>
               <td>{{ $saleOrder->currdispatch_qty }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <br>
    @endforeach
  @endif
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>

   <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>

<script>
  function exportAllTablesToExcel() {
      let workbook = XLSX.utils.book_new(); // Create a new Excel workbook
      let tables = document.querySelectorAll("table"); // Select all tables

      if (tables.length === 0) {
          alert("No tables found to export!");
          return;
      }

      tables.forEach((table, index) => {
          let productTitle = table.previousElementSibling; // Get the previous element (should be h3 for product name)
          let sheetName = productTitle ? productTitle.innerText.trim() : "Sheet" + (index + 1);

          // Ensure Excel sheet names are valid (max 31 chars, no special chars)
          sheetName = sheetName.substring(0, 31).replace(/[\/\\?*:[\]]/g, '');

          let worksheet = XLSX.utils.table_to_sheet(table); // Convert table to Excel sheet
          XLSX.utils.book_append_sheet(workbook, worksheet, sheetName); // Add sheet to workbook
      });

      // Save Excel file
      XLSX.writeFile(workbook, "Labour_Receipt.xlsx");
  }
</script>



</html>
