<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Information</title>
    <style>
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        /* Fullscreen Background */
        body {
            height: 100vh;
            width: 100vw;
            background: url('{{ asset('public/logos/mango_background.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            text-align: center;
        }

        /* Overlay for better contrast */
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(2px);
        }

        /* Main Card Container */
        .container {
            position: relative;
            color: white;
            padding: 10px;
            width: 50%;
            max-width: 300px;
            min-height: 70px ;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border-radius: 18px;
            box-shadow: 0px 0px 25px rgba(0, 255, 255, 0.5);
            border: 2px solid rgba(0, 255, 255, 0.4);
            transition: 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Hover Effect */
        .container:hover {
            box-shadow: 0px 0px 40px rgba(0, 255, 255, 0.8);
            transform: scale(1.02);
        }
        

        /* Logo Styling */
        .logo img {
            height: 100px;
            width: auto;
            margin-bottom: 20px;
        }

        /* Title */
        h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Product Details */
        .details p {
            font-size: 18px;
            margin: 10px 0;
            font-weight: 300;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px;
            border-radius: 10px;
            display: inline-block;
            width: 80%;
        }

        /* Footer */
       .footer {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid rgba(255, 255, 255, 0.3);
}

.footer p {
    font-size: 16px;
    font-weight: 400;
    margin: 5px 0;
}

/* Website Link */
.footer a {
    color: cyan;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}

.footer a:hover {
    text-decoration: underline;
    color: #00ffcc;
}


        /* Subtle Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            animation: fadeIn 1s ease-in-out;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 30px;
            }
            h1 {
                font-size: 24px;
            }
            .details p {
                font-size: 16px;
                width: 90%;
            }
            .footer p {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    @php
        $logo = DB::table('company')->value('logo');
    @endphp

    <!-- Dark Overlay -->
    <div class="overlay"></div>

    <!-- Main Product Information Card -->
    <div class="container">
        <!-- Company Logo -->
        <div class="logo">
            <img src="{{ asset('public/' . $logo) }}" alt="Company Logo">
        </div>

        <!-- Product Information -->
        <h1>Product Information</h1>
        <div class="details">
            <!--<p><strong>Order ID:</strong> {{ $orderId }}</p>-->
            <p><strong></strong> {{ $product }}</p>
            <p><strong></strong> {{ $size }}</p>
            
        </div>

        <!-- Footer -->
       <div class="footer">
    <p><strong>GI. NO:</strong></p>
    <p>Ratnagiri: AU/34296/GI/139/1768</p>
    <p>Devgad: AU/19503/GI/139/1486</p>
    <br>
    <p><strong>Website :</strong> <a href="https://aamrammango.com/" target="_blank">aamrammango.com</a></p>
</div>

    </div>
</body>
</html>
