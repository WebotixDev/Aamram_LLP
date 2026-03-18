<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk QR Stickers</title>
    <script src="https://cdn.jsdelivr.net/npm/qr-code-styling/lib/qr-code-styling.min.js"></script>
     <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 20px;
            background-color: #f8f8f8;
        }

        .sticker-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .sticker {
            display: flex;
            width: 120mm;
            height: 60mm;
            border: 2px solid black;
            background: white;
            box-sizing: border-box;
        }

        .qr-section {
            width: 45mm;
            padding: 25mm 0 0 0; /* 25mm top padding only */
            display: flex;
            justify-content: center;
            align-items: flex-start;
            border-right: 2px solid black;
            box-sizing: border-box;
        }

        .qr-section div {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .details-section {
            text-align:left;
            flex: 1;
            padding: 5mm 0 0 0; /* 25mm top padding only */

            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding-left: 10px;
        }

        .details-box {
            font-size: 14px;
            text-align: left;
        }

        .footer-box {
            font-size: 12px;
            text-align: center;
            border-top: 1px solid black;
            padding: 5px 0 5px 0;
            margin-top: auto;
        }

        @media print {
            .button-container {
                display: none;
            }
        }
    </style>
</head>
<body>

<h1>Bulk QR Stickers</h1>

<div class="button-container">
    <button onclick="printAllQRCodes()">Print Stickers</button>
</div>

<br>


@foreach ($saleOrderss as $saleorder)
    <?php
    // Fetch batch history related to the sale order
    $batchHistory = DB::table('batch_history')
        ->join('products', 'batch_history.productid', '=', 'products.id')
        ->where('batch_history.orderid', $saleorder->id) // Use orderid directly
        ->select('batch_history.id', 'batch_history.orderid', 'batch_history.sizeid', 'products.product_name')
        ->first();
        
        // dd($saleorder->id);
        // die;

    if ($batchHistory) {
        
        // echo "hiii";
        // die;
        // Extract batch details
        $batchId = $batchHistory->id;
        $orderId = $batchHistory->orderid;
        $sizeId = $batchHistory->sizeid;
        $productName = $batchHistory->product_name;
    }
    
     
      
    ?>
    @php
       
        $fetchProductSize = DB::table('product_details')
            ->where('id',$sizeId)
            ->value('product_size');
    @endphp

    <div class="sticker-container">
        <div class="sticker">
            <!-- QR Code Section -->
            <div class="qr-section">
                <div id="qrcode-{{ $saleorder->id }}"></div>
            </div>

            <!-- Details Section -->
            <div class="details-section">
                <div>
                    <strong>Name:</strong> {{ $saleorder->customer_name ?? 'N/A' }}<br>
                    <strong>Mobile:</strong> {{ $saleorder->mobile_no ?? 'N/A' }}<br>
                    <strong>Address:</strong>
                    {{ $saleorder->address ?? 'N/A' }} ,
                    {{ $saleorder->city_name ?? 'N/A' }} ,
                    {{ $saleorder->district_id ?? 'N/A' }} ,
                    {{ $saleorder->state_name ?? 'N/A' }}<br>
                    <strong>Product:</strong> {{ $productName }} ({{ $fetchProductSize }})<br>
                </div>
                <div class="footer-box">
                    <strong>Contact:</strong> +91 9175521755<br>
                    <strong>Website:</strong> <a href="https://www.aamrammango.com" target="_blank">www.aamrammango.com</a>
                </div>
            </div>
        </div>
    </div>
<br>

    <!-- Store data for QR generation -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const orderId = "{{ $saleorder->id }}";
            const productName = "{{ $productName }}";
            const batchId = "{{ $batchId }}";
            const sizeName = "{{ $fetchProductSize }}";
            const elementId = "qrcode-" + orderId;

            generateQRCode(orderId, productName, batchId, sizeName, elementId);
        });
    </script>

@endforeach

<script>
    function generateQRCode(orderId, productName, batchId, sizeName, elementId) {
        const qrCodeUrl = `https://inventory.aamramm.cominformation?orderid=${orderId}&product=${encodeURIComponent(productName)}&batchid=${batchId}&size=${encodeURIComponent(sizeName)}`;

        const qrCode = new QRCodeStyling({
            width: 120,
            height: 120,
            type: "svg",
            data: qrCodeUrl,
            dotsOptions: {
                type: "square",
                color: "#000000"
            },
            backgroundOptions: {
                color: "#ffffff"
            }
        });

        const qrCodeDiv = document.getElementById(elementId);
        if (qrCodeDiv) {
            qrCodeDiv.innerHTML = "";  // Clear previous content
            qrCode.append(qrCodeDiv);  // Append new QR code
        }
    }

    // Function to print all QR codes
    function printAllQRCodes() {
        window.print();
    }
</script>




</body>
</html>
