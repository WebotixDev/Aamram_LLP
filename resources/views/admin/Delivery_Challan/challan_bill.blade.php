<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devlivery Challan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 7px;
        }

        /* Apply one border around the entire content */
        .content-wrapper {
            border: 1px solid black;
            padding: 5px 10px;

        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 1px;
            text-align: left;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #000;
        }

        .details-section {
            display: flex;
            justify-content: space-between;
            /* margin-top: 20px; */
        }

        .company-details {
            width: 48%;
        }


        .customer-details {
            width: 30%;
        }


        .print-btn {
            margin: 20px 0;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .print-btn:hover {
            background-color: #45a049;
        }

        .cancel-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: red;
            color: white;
            border: none;
            font-size: 20px;
            cursor: pointer;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cancel-btn:hover {
            background-color: darkred;
        }

        /* Hide buttons when printing */
        @media print {
            .print-btn, .cancel-btn {
                display: none !important;
            }
        }

        /* Make the table responsive */
        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Adjustments for smaller screens */
        @media (max-width: 600px) {
            .details-section {
                flex-direction: column;
            }

            .company-details, .customer-details {
                width: 100%;
            }

        }

        /* Style for the horizontal line */
        hr {
            border: 0;
            border-top: 1px solid #ccc;
            margin: 20px 0;
        }
    </style>
</head>
<body>

<?php
use Illuminate\Support\Facades\DB;

// Fetch company details
$company = DB::table('company')->first();
$logo = DB::table('company')->value('logo');
$bank = DB::table('accounts')->first(); // Fetch bank details

?>

<!-- Cancel button -->
<button class="cancel-btn" onclick="javascript:window.close()">✖</button>

<!-- Content Wrapper with border -->
<div class="content-wrapper">
    <div class="header">
        <!-- Company Logo -->
        <div class="logo">
            <img src="<?php echo asset('public/' . $logo) ?>" style="height: 80px; width: auto;" alt="Company Logo">
        </div>
        <button class="print-btn" onclick="window.print()">Print</button>
    </div>

    <div class="details-section">
        <!-- Company Details -->
        <div class="company-details">

            <h3>FROM</h3>
            <p><strong>Name:</strong> <?php echo $company->name; ?></p>
            <p><strong>Address:</strong> <?php echo $company->address; ?></p>
            <p><strong>Contact:</strong> <?php echo $company->phone; ?></p>
            <p><strong>Email:</strong> <?php echo $company->email; ?></p>


        </div>

        <!-- Add a horizontal line here for separation -->
        <hr>

        <!-- Customer Details -->
        <div class="customer-details">
            <h3>Transpoter Details</h3>
            <p><strong>Name:</strong> <?php echo $saleOrder->transporter; ?></p>
<p><strong>Date:</strong> <?php echo date('d-m-Y', strtotime($saleOrder->billdate)); ?></p>

        </div>
    </div>

    <!-- Make the table scrollable -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>SR.No</th>
                    <th>Customer Name</th>
                    <th>Address</th>
                    <th>Product Name</th>
                    <th>Size</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @php $srNo = 1; @endphp
                @foreach($saleOrderDetails as $customer => $details)
                    <!-- Display customer name and address only once -->
                    @php
                        $firstDetail = $details->first();
                    @endphp
           <tr>
            <td rowspan="{{ $details->count() }}">{{ $srNo++ }}</td>
            <td rowspan="{{ $details->count() }}">
                {{ $firstDetail->customer_name }} <strong> - {{ $firstDetail->mobile_no }}</strong>
            </td>
            <td rowspan="{{ $details->count() }}">
           <!--{{ $firstDetail->address }}, {{ $firstDetail->city_name }}, {{ $firstDetail->district_id }}, {{ $firstDetail->state_name }},{{ $firstDetail->pin_code }}-->


 {{ $firstDetail->order_address }},{{ $firstDetail->state_name }}
            </td>

            <!-- First row of this customer -->
            <td>{{ $firstDetail->product_name }}</td>
            <td>{{ $firstDetail->product_size }}</td>
            <td>{{ $firstDetail->currdispatch_qty }}</td>
        </tr>


                    <!-- Additional rows for the same customer -->
                    @foreach($details->skip(1) as $detail)
                    <tr>
                        <td>{{ $detail->product_name }}</td>
                        <td>{{ $detail->product_size }}</td>
                        <td>{{ $detail->currdispatch_qty }}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>


        </table>
    </div>




</div>

<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


</body>
</html>

