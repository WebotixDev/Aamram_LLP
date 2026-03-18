<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Bill</title>

    <style>
        @page {
            size: A5;
            margin: 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .content-wrapper {
            border: 1px solid black;
            width: 148mm;
            margin: auto;
            padding: 10px;
            box-sizing: border-box;
        }

        /* HEADER */

        .header {
            text-align: center;
            border-bottom: 2px solid black;
            padding-bottom: 8px;
        }

        .header img {
            height: 55px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
        }

        .company-info {
            font-size: 12px;
        }

        /* INVOICE HEADER */

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .invoice-box {
            border: 2px solid black;
            padding: 8px 12px;
            font-size: 16px;
            font-weight: bold;
            background: #f8f8f8;
        }

        .invoice-number {
            font-size: 18px;
            color: #d40000;
        }

        .invoice-info {
            font-size: 13px;
        }

        /* DETAILS */

        .details-section {
            display: flex;
            justify-content: space-between;
            margin-top: 12px;
            border: 1px solid black;
            padding: 8px;
            font-size: 12px;
        }

        .company-details,
        .customer-details {
            width: 48%;
        }

        /* TABLE */

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 12px;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th {
            background: #e8e8e8;
            font-size: 13px;
        }

        th,
        td {
            padding: 5px;
            text-align: center;
        }

        .total-row td {
            font-size: 14px;
            font-weight: bold;
        }

        /* SIGNATURE */

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .signature-box {
            width: 40%;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid black;
            margin-top: 40px;
            padding-top: 5px;
        }

        /* FOOTER */

        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 10px;
        }

        /* PRINT BUTTON */

        .print-btn {
            display: block;
            margin: 10px auto;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            font-size: 16px;
            cursor: pointer;
        }

        .print-btn:hover {
            background: #45a049;
        }

        @media print {
            .print-btn {
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

    $location = DB::table('location')
            ->where('id', $saleOrder->location_id) // location_id stored in main table
            ->first();
    ?>

    <button class="print-btn" onclick="window.print()">Print</button>

    <div class="content-wrapper">

        <!-- HEADER -->

        <div class="header">

            <img src="<?php echo asset('public/' . $logo); ?>" alt="logo">

            <div class="company-name">
                <?php echo $company->name; ?>
            </div>

            <div class="company-info">
                <?php echo $company->address; ?><br>
                Phone : <?php echo $company->phone; ?>
            </div>

        </div>

        <!-- INVOICE HEADER -->

        <div class="invoice-header">

            <div class="invoice-box">
                Invoice No :
                <span class="invoice-number">
                    <?php echo $saleOrder->Invoicenumber; ?>
                </span>
            </div>

            <div class="invoice-info">

                <strong>Batch No :</strong>
                <?php echo $saleOrder->batch_number; ?><br>

                <strong>Date :</strong>
                <?php echo date('d-m-Y', strtotime($saleOrder->PurchaseDate)); ?><br>


            </div>

        </div>

        <!-- DETAILS -->

    <div class="details-section">

     <div class="company-details">

            <b>Purchase Location</b><br><br>

            Location : <?php echo $location->location; ?><br>

            Purchase Manager : <?php echo $location->purchase_manager; ?><br>

            Mobile : <?php echo $location->mobile_no; ?><br>

            Address : <?php echo $location->address; ?>

            </div>

            <div class="customer-details">

                <b>Supplier Details</b><br><br>

                Name : <?php echo $saleOrder->supplier_name; ?><br>
                Mobile : <?php echo $saleOrder->mobile_no; ?><br>
                Address : <?php echo $saleOrder->address; ?>

            </div>

        </div>

        <!-- TABLE -->

        <table>

            <thead>

                <tr>
                    <th width="5%">SR</th>
                    <th width="20%">Product</th>
                    <th width="30%">Size</th>
                    <th width="10%">Qty</th>
                    <th width="15%">Rate</th>
                    <th width="20%">Amount</th>
                </tr>

            </thead>

            <tbody>

                <?php
$totalAmount = 0;

foreach($saleOrderDetails as $index=>$detail):

$lineTotal = $detail->rate * $detail->Quantity;

$totalAmount += $lineTotal;
?>

                <tr>

                    <td><?php echo $index + 1; ?></td>

                    <td><?php echo $detail->product_name; ?></td>

                    <td><?php echo $detail->size_name; ?></td>

                    <td><?php echo $detail->Quantity; ?></td>

                    <td><?php echo number_format($detail->rate, 2); ?></td>

                    <td><?php echo number_format($lineTotal, 2); ?></td>

                </tr>

                <?php endforeach; ?>

            </tbody>

            <tfoot>

                <tr class="total-row">

                    <td colspan="3">TOTAL</td>

                    <td><?php echo $saleOrder->Quantity; ?></td>

                    <td></td>

                    <td><?php echo number_format($totalAmount, 2); ?></td>

                </tr>

            </tfoot>

        </table>

        <!-- SIGNATURE -->

        <div class="signature-section">

            <div class="signature-box">
                <div class="signature-line">
                    Receiver Signature
                </div>
            </div>

            <div class="signature-box">
                <div class="signature-line">
                    Farmer Signature
                </div>
            </div>

        </div>

        <!-- FOOTER -->

        <div class="footer">

            <p>Thank You!</p>

        </div>

    </div>

</body>

</html>
