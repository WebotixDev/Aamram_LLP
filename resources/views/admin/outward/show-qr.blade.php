<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Stickers</title>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
             <style>
       body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            /*padding: 20px;*/
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
            width: 95mm;
            height: 60mm;
            background: white;
            box-sizing: border-box;
    /*border: 2px solid black;*/

        }

        .qr-section {
            padding: 20mm 6px 0 0; /* 25mm top padding only */
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
            font-size:13px;
            text-align:left;
            flex: 1;
            padding: 10mm 0 0 0; /* 25mm top padding only */
            min-height:53mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding-left: 10px;
        }

        .details-box {
            font-size: 12px;
            text-align: left;
        }

        .footer-box {
            font-size: 12px;
            text-align: center;
            border-top: 1px solid black;
            padding: 5px 0 0 0;
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

<div class="button-container">
    <button onclick="printAllQRCodes()">Print Stickers</button>
</div>
<br>

@if ($outwardDetails->isEmpty())
    <p>No batch history found for this order.</p>
@else
<div class="sticker-container">
    @foreach ($outwardDetails as $batch)
    @php


        $fetchProductSize = DB::table('product_details')
            ->where('id', $batch->size)
            ->value('product_size');

            $fetchProduct = DB::table('products')
            ->where('id', $batch->services)
            ->value('product_name');

    @endphp
        <div class="sticker">
        <div class="qr-section">
            <div id="qrcode-{{ $batch->order_no }}"></div>
        </div>
        <!-- Details Section -->
        <div class="details-section">
            <div>
                <strong>Name:</strong> {{ $saleOrderss->customer_name }}<br>
                <strong>Mobile:</strong> {{ $saleOrderss->mobile_no }}<br>
                <strong>Address:</strong>
                   <!--{{-- {{ $saleOrderss->address }}, {{ $saleOrderss->city_name }},{{ $saleOrderss->district_id }},{{ $saleOrderss->state_name }}{{ $saleOrderss->pin_code }}, --}}-->
                {{ $saleOrderss->order_address }},{{ $saleOrderss->state_name }}

<br>
                <strong>Product:</strong> {{ $fetchProduct }} ({{ $fetchProductSize }})<br>
            </div>
            <div class="footer-box">
                <strong>Contact:</strong> +91 91755 21755<br>
                <strong>Website:</strong> <a href="https://www.aamrammango.com" target="_blank">www.aamrammango.com</a>
            </div>
        </div>
    </div>

    
    @endforeach
</div>
@endif

<script>
// Function to generate QR codes
function generateQRCode(orderId, productName, sizeName, elementId) {
    const qrCodeUrl = `https://inventory.aamramm.com/information?orderid=${orderId}&product=${encodeURIComponent(productName)}&size=${encodeURIComponent(sizeName)}`;

    new QRCode(document.getElementById(elementId), {
        text: qrCodeUrl,
         width: 130,
          height: 130,
    });
}

// Generate QR codes dynamically
@foreach ($outwardDetails as $batch)
    generateQRCode(
        "{{ $batch->order_no }}",
        "{{ $fetchProduct}}",
        "{{ $fetchProductSize }}",
        "qrcode-{{ $batch->order_no }}"
    );
@endforeach

// Print function
function printAllQRCodes() {
    window.print();
}
</script>

</body>
</html>
