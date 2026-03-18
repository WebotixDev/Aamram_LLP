<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/qr-code-styling/lib/qr-code-styling.min.js"></script>
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
            height: 57mm;
            background: white;
            box-sizing: border-box;
    /*border: 2px solid black;*/

        }

        .qr-section {
            padding: 20mm 0 0 0; /* 25mm top padding only */
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
            font-size:12px;
            text-align:left;
            flex: 1;
            padding: 4mm 0 0 0; /* 25mm top padding only */
            min-height:53mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding-left: 7px;
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


@foreach ($saleOrderss as $saleorder)
    @php
        $batchHistory = DB::table('outward_details')
            ->where('id', $saleorder->id)
            ->select('order_no', 'services', 'size', 'currdispatch_qty')
            ->first();

        $orderId = $batchHistory->order_no ?? 'N/A';
        $productName = $batchHistory->services ?? 'N/A';
        $sizeId = $batchHistory->size ?? null;
        $qty = $batchHistory->currdispatch_qty ?? 1; // Default to 1 if null

        $fetchProductSize = $sizeId ? DB::table('product_details')->where('id', $sizeId)->value('product_size') : 'N/A';
        $fetchProduct = $productName ? DB::table('products')->where('id', $productName)->value('product_name') : 'N/A';
    @endphp
    
    @for ($i = 1; $i <= $qty; $i++)
        <div class="sticker-container">
            <div class="sticker">
                <div class="qr-section">
                    <div id="qrcode-{{ $saleorder->id }}-{{ $i }}"></div>
                </div>
                <div class="details-section">
                    <div>
                        <strong>Name:</strong> {{ $saleorder->customer_name ?? 'N/A' }}<br>
                        <strong>Mobile:</strong> {{ $saleorder->mobile_no ?? 'N/A' }}<br><br>
                        <strong>Product:</strong> {{ $fetchProduct }} ({{ $fetchProductSize }})<br>
                        <strong>Address:</strong>
                          <!--{{ $saleorder->address ?? 'N/A' }}, {{ $saleorder->city_name ?? 'N/A' }},  {{ $saleorder->district_id ?? 'N/A' }},{{ $saleorder->state_name ?? 'N/A' }} {{ $saleorder->pin_code ?? 'N/A' }}, --}}-->

                        {{ $saleorder->address ?? 'N/A' }},{{ $saleorder->state_name ?? 'N/A' }}
                        <br>
                    </div>
                    <div class="footer-box">
                        <strong>Contact:</strong> +91 91755 21755<br>
                        <strong>Website:</strong> <a href="https://www.aamrammango.com" target="_blank">www.aamrammango.com</a>
                    </div>
                </div>
            </div>
        </div>
        <br>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const orderId = "{{ $saleorder->id }}";
                const productName = "{{ $fetchProduct }}";
                const sizeName = "{{ $fetchProductSize }}";
                const elementId = "qrcode-" + orderId + "-{{ $i }}";

                generateQRCode(orderId, productName, sizeName, elementId);
            });
        </script>
    @endfor
@endforeach

<script>
    function generateQRCode(orderId, productName, size, elementId) {
        const qrCodeUrl = `https://inventory.aamramm.com/information?orderid=${orderId}&product=${encodeURIComponent(productName)}&size=${encodeURIComponent(size)}`;
        console.log(qrCodeUrl);
        const qrCode = new QRCodeStyling({
            width: 140,
            height: 140,
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
            qrCodeDiv.innerHTML = "";
            qrCode.append(qrCodeDiv);
        }
    }
    function printAllQRCodes() {
        window.print();
    }
</script>

</body>
</html>