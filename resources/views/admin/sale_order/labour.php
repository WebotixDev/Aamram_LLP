<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Labour Order Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }

        .header .logo {
            width: 100%;
        }

        .details-section {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .company-details {
            width: 48%;
            text-align: left; /* Align Company Details to the left */
        }



        .company-details p, .customer-details p {
            margin: 5px 0;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
        }

        /* Styling for printing */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            @page {
                size: A5;
                margin: 10mm;
            }

            .print-btn {
                display: none; /* Hide the button when printing */
            }
            .cancel-btn {
                display: none !important;
            }
        }

        /* Print button styling */
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

    </style>
</head>
<body>
<?php
use Illuminate\Support\Facades\DB;

// Fetch company details
$company = DB::table('company')->first();
$logo = DB::table('company')->value('logo');
?>
<button class="cancel-btn" onclick="javascript:window.close()">✖</button>

<div class="header">
    <!-- Company Logo -->
    <div class="logo">
        <img src="<?php echo asset('public/' . $logo) ?>" style="height: 80px; width: auto;" alt="Company Logo">
    </div>
    <button class="print-btn" onclick="window.print()">Print</button>
</div>

    <div class="details-section">


        <!-- Customer Details -->
        <div class="company-details">
            <h3>Customer Details</h3>
            <p><strong>Name:</strong> <?php echo $saleOrder->customer_name; ?></p>
            <p><strong>Contact:</strong> <?php echo $saleOrder->mobile_no; ?></p>
            <p><strong>Address:</strong> <?php echo  $saleOrder->address . ', '. $saleOrder->city_name . ', ' . $saleOrder->district_id . ', ' . $saleOrder->state_name; ?></p>
            <p><strong>Order Date:</strong> <?php echo $saleOrder->order_date; ?></p>

        </div>

    </div>


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
            <?php foreach($saleOrderDetails as $index => $detail): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo $detail->product_name; ?></td>
                <td><?php echo $detail->product_size; ?></td>
                <td><?php echo $detail->qty; ?></td>

            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>



    <!-- Print Button -->
</body>
</html>
