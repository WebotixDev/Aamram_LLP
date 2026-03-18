<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Bill Print</title>
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
            /*height: 210mm;*/
            margin: auto;
            box-sizing: border-box;
            padding: 1px 10px ;
            /*padding-top: 20px;*/
        }

        .header {
            text-align: center;
            border-bottom: 1px solid black;
            /*padding: 10px 0;*/
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
            padding: 1px;
            text-align: left;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 5px;
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

        @media print {
            .print-btn {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<?php
use Illuminate\Support\Facades\DB;

$company = DB::table('company')->first();
$logo = DB::table('company')->value('logo');
$bank = DB::table('accounts')->first();
?>

<button class="print-btn" onclick="window.print()">Print</button>

<div class="content-wrapper">
    <div class="header">
        <img src="<?php echo asset('public/' . $logo) ?>" style="height: 50px;" alt="Company Logo">
    </div>
    
    <div class="details-section">
        <div class="company-details">
            <strong>Name:</strong> <?php echo $company->name; ?><br>
            <strong>Address:</strong> <?php echo $company->address; ?><br>
            <strong>Contact:</strong> <?php echo $company->phone; ?>
        </div>
        <div class="customer-details">
            <strong>Name:</strong> <?php echo $saleOrder->customer_name; ?><br>
            <strong>Contact:</strong> <?php echo $saleOrder->mobile_no; ?><br>
            <!--<strong>Address:</strong> <?php //echo $saleOrder->address . ', ' .  $saleOrder->city_name . ', ' . $saleOrder->district_id . ', ' . $saleOrder->state_name . ', ' . $saleOrder->pin_code; ?><br>-->
             <strong>Address:</strong> <?php echo  $saleOrder->order_address . ', ' . $saleOrder->state_name ; ?><br>
            <strong>Invoice Date:</strong> <?php echo date('d-m-Y', strtotime($saleOrder->billdate)); ?><br>
            <strong>Invoice No.:</strong> <?php echo $saleOrder->id; ?><br>
        </div>
    </div>
<br>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>SR.No</th>
                    <th>Product</th>
                    <th>Size</th>
                    <th>HSN</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>GST</th>
                    <th>TransCost</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($saleOrderDetails as $index => $detail): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo $detail->product_name; ?></td>
                    <td><?php echo $detail->product_size; ?></td>
                     <td><?php echo "08045040"; ?></td>
                    <td><?php echo $detail->qty; ?></td>
                    <td><?php echo number_format($detail->rate, 2); ?></td>
                    <td><?php echo number_format($detail->gstper, 2); ?></td>
                    <td><?php echo number_format($detail->transper); ?></td>
                    <td><?php echo number_format($detail->amount); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="8" style="text-align: right;">Total</th>
                    <th style="background-color: yellow"><?php echo number_format(($saleOrder->Tamount)); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="details-section">
        <div class="bank-details">
            <p><strong>Bank Name:</strong> <?php echo $bank->bank_name; ?></p>
            <p><strong>Account Number:</strong> <?php echo $bank->ACNo; ?></p>
            <p><strong>IFSC Code:</strong> <?php echo $bank->IFSC; ?></p>
            <p><strong>Account Holder:</strong> <?php echo $bank->account_name; ?></p>
        </div>
        <?php if ($saleOrder->user_id != 'web' && $saleOrder->user_id != 'chatbot') { ?>
            <div class="qr-code" style="text-align: center;">
                <div id="qrcode"></div>
                <p>Scan to Pay</p>
            </div>
        <?php } ?>
    </div>

    <div class="footer">
        <p>Thank you for your purchase!</p>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    var id = "<?php echo $saleOrder->id; ?>";
    new QRCode(document.getElementById("qrcode"), {
        text: `https://inventory.aamramm.com/razorpay?id=${id}`,
        width: 100,
        height: 100,
        colorDark: "#000",
        colorLight: "#fff",
        correctLevel: QRCode.CorrectLevel.H
    });
</script>
</body>
</html>
