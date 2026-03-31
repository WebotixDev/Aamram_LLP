<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm DC Bill</title>
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

        /* DETAILS SECTION */
        .details-section {
            margin-top: 12px;
        }

        .details-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .details-left,
        .details-right {
            width: 48%;
            font-size: 12px;
        }

        .details-left b,
        .details-right b {
            font-size: 13px;
        }

        .driver-section {
            margin-top: 10px;
            font-size: 12px;
        }

        .driver-section hr {
            margin: 8px 0;
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
    $from_location = DB::table('location')->where('id', $farm_delivery_challan->from_location_id)->first();
    $to_location = DB::table('location')->where('id', $farm_delivery_challan->to_location_id)->first();
    $transporter = DB::table('transporter')->where('id', $farm_delivery_challan->transporter_id)->first();
    ?>
    <button class="print-btn" onclick="window.print()">Print</button>

    <div class="content-wrapper">
        <!-- HEADER -->
        <div class="header">
            <img src="<?php echo asset('public/' . $logo); ?>" alt="logo">
            <div class="company-name"><?php echo $company->name; ?></div>
            <div class="company-info">
                <?php echo $company->address; ?><br>
                Phone: <?php echo $company->phone; ?>
            </div>
        </div>

        <!-- INVOICE HEADER -->
        <div class="invoice-header">
            <div class="invoice-box">
                Invoice No : <span class="invoice-number"><?php echo $farm_delivery_challan->Invoicenumber; ?></span>
            </div>
            <div class="invoice-info">
                <strong>Date :</strong> <?php echo date('d-m-Y', strtotime($farm_delivery_challan->challan_date)); ?><br>
            </div>
        </div>

        <!-- DETAILS SECTION -->
        <div class="details-section">
            <!-- Row: From & To locations -->
            <div class="details-row">
                <div class="details-left">
                    <b>From Location</b><br><br>
                    Location: <?php echo $from_location->location ?? ''; ?><br>
                    Manager: <?php echo $from_location->purchase_manager ?? ''; ?><br>
                    Mobile: <?php echo $from_location->mobile_no ?? ''; ?><br>
                    Address: <?php echo $from_location->address ?? ''; ?>
                </div>
                <div class="details-right">
                    <b>To Location</b><br><br>
                    Location: <?php echo $to_location->location ?? ''; ?><br>
                    Contact: <?php echo $to_location->purchase_manager ?? ''; ?><br>
                    Mobile: <?php echo $to_location->mobile_no ?? ''; ?><br>
                    Address: <?php echo $to_location->address ?? ''; ?>
                </div>
            </div>

            <!-- Transporter & Driver Details -->
            <div class="driver-section">
                <hr>
                <b>Transporter Details:</b> <?php echo $transporter->transporter ?? ''; ?>, Mobile: <?php echo $transporter->mobile_no ?? ''; ?><br>
                <b>Driver Details:</b> <?php echo $farm_delivery_challan->driver_name ?? ''; ?>, Mobile: <?php echo $farm_delivery_challan->driver_mobile_no ?? ''; ?>
            </div>
        </div>

        <!-- TABLE -->
        <table>
            <thead>
                <tr>
                    <th width="5%">SR</th>
                    <th width="25%">Product</th>
                    <th width="30%">Size</th>
                    <th width="10%">Stage</th>
                    <th width="20%">Batch Number</th>
                    <th width="5%">Qty</th>
                    <th width="5%">Trans Cost (Per Qty)</th>
                    <th width="5%">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($farm_delivery_challan_details as $index => $detail): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo $detail->product_name; ?></td>
                        <td><?php echo $detail->size_name; ?></td>
                        <td><?php echo $detail->stage; ?></td>
                        <td><?php echo $detail->batch_number; ?></td>
                        <td><?php echo $detail->Quantity; ?></td>
                         <td><?php echo $detail->transcost; ?></td>
                         <td><?php echo $detail->transcost * $detail->Quantity; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
         <tfoot>

                <tr class="total-row">

                    <td colspan="7">TOTAL</td>


                    <td><?php echo $farm_delivery_challan->totalamt; ?></td>

                </tr>

            </tfoot>
        </table>

        <!-- SIGNATURE -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"> Authorised Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"> Receiver Signature</div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p>Thank You!</p>
        </div>
    </div>
</body>

</html>
