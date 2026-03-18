<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Bill Print</title>
    <style>
        @page {
            size: A5 portrait;
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin-top: 5px;
            padding: 0;
        }

        .content-wrapper {
            border: 1px solid black;
            width: 148mm;
            height: 210mm;
            margin: auto;
            box-sizing: border-box;
            padding: 5px 10px;
            position: relative;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid black;
            padding-bottom: 5px;
        }

        .details-section {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            padding-top: 10px;
        }

        .company-details, .customer-details {
            width: 48%;
        }

        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 3px;
            text-align: left;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            position: absolute;
            bottom: 10px;
            width: 100%;
        }

        .print-btn {
            display: block;
            margin: 10px auto;
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

        @media print {
            .print-btn, .cancel-btn {
                display: none !important;
            }

            .page-break {
                page-break-before: always;
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

<!-- Print Button (ONLY ONCE AT THE TOP) -->
<button class="print-btn" onclick="window.print()">Print</button>

<!-- Cancel button -->
<button class="cancel-btn" onclick="javascript:window.close()">✖</button>

@foreach($saleOrderDetails as $customer => $details)
<div class="page-break">
    <div class="content-wrapper">
        <div class="header">
            <!-- Company Logo -->
            <div class="logo">
                <img src="{{ asset('public/' . $logo) }}" style="height: 60px; width: auto;" alt="Company Logo">

            </div>
        </div>

        <div class="details-section">
            <!-- Company Details -->
            <div class="company-details">
                <h3>Company Details</h3>
                <p><strong>Name:</strong> {{ $company->name }}</p>
                <p><strong>Contact:</strong> {{ $company->phone }}</p>
                <p><strong>Address:</strong> {{ $company->address }}</p>
                <p><strong>Email:</strong> {{ $company->email }}</p>
            </div>

            <!-- Customer Details -->
            <div class="customer-details">
                @php
                    $firstDetail = $details->first();
                @endphp
                <h3>Customer Details</h3>
                <p><strong>Name:</strong> {{ $firstDetail->customer_name }} </p>
                <p><strong>Contact:</strong> {{ $firstDetail->mobile_no }} </p>
                <p><strong>Address:</strong>
     <!--{{ $firstDetail->address }}, {{ $firstDetail->city_name }}, {{ $firstDetail->district_id }}, {{ $firstDetail->state_name }}, {{ $firstDetail->pin_code }}-->


                    {{ $firstDetail->order_address }},{{ $firstDetail->state_name }}                    <!--<p><strong>Date:</strong> <?php echo date('d-m-Y', strtotime($firstDetail->billdate)); ?></p>-->

            </div>
        </div>

        <!-- Table for Products -->
        <div class="table-container">
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
                    @php $srNo = 1; @endphp
                    @foreach($details as $detail)
                    <tr>
                        <td>{{ $srNo++ }}</td>
                        <td>{{ $detail->product_name }}</td>
                        <td>{{ $detail->product_size }}</td>
                        <td>{{ $detail->currdispatch_qty }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for Purchase!</p>
        </div>
    </div>
</div>
@endforeach

<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html>
