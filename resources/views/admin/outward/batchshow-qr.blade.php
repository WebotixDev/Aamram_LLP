<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Stickers</title>
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

<h1>QR Stickers</h1>
<div class="button-container">
    <button onclick="printAllQRCodes()">Print Stickers</button>
</div>
<br>
@if ($batchHistory->isEmpty())
    <p>No batch history found for this order.</p>
@else
<div class="sticker-container">
    @foreach ($batchHistory as $batch)
    @php
        $fetchProductSize = DB::table('product_details')
            ->where('id', $batch->sizeid)
            ->value('product_size');
    @endphp
    <div class="sticker">
        <!-- QR Code Section -->
        <div class="qr-section">
            <div id="qrcode-{{ $batch->id }}"></div>
        </div>
        <!-- Details Section -->
        <div class="details-section">
            <div class="details-box">
                <strong>Name:</strong> {{ $saleOrderss->customer_name }}<br>
                <strong>Mobile:</strong> {{ $saleOrderss->mobile_no }}<br>
                <strong>Address:</strong>
                {{ $saleOrderss->address }},
                {{ $saleOrderss->city_name }},
                {{ $saleOrderss->district_id }},
                {{ $saleOrderss->state_name }}<br>
                <strong>Product:</strong> {{ $batch->product_name }} ({{ $fetchProductSize }})<br>
            </div>
            <div class="footer-box">
                <strong>Contact:</strong> +91 9175521755<br>
                <strong>Website Link:</strong> <a href="https://www.aamrammango.com" target="_blank">www.aamrammango.com</a>

            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

<script>
// Function to generate QR codes
function generateQRCode(orderId, productName, batchId, sizeName, elementId) {
    const qrCodeUrl = `https://inventory.aamramm.com/information?orderid=${orderId}&product=${encodeURIComponent(productName)}&batchid=${batchId}&size=${encodeURIComponent(sizeName)}`;
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
    qrCodeDiv.innerHTML = ""; // Clear existing QR code
    qrCode.append(qrCodeDiv); // Append the QR code to the specified div
}

// Generate QR codes dynamically
@foreach ($batchHistory as $batch)
    generateQRCode(
        "{{ $batch->orderid }}",
        "{{ $batch->product_name }}",
        "{{ $batch->batch_id }}",
        "{{ $fetchProductSize }}",
        "qrcode-{{ $batch->id }}"
    );
@endforeach

// Print function
function printAllQRCodes() {
    window.print();
}
</script>

</body>
</html>
