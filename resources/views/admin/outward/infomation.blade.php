<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Information</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Full background image */
        .container {
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column; /* Ensure the content is arranged vertically */
        }

        /* Content styling */
        .content {
            text-align: center;
            color: white;
            padding: 20px;
            background: rgba(0, 0, 0, 0.5); /* Optional: adds a dark overlay for better text readability */
            border-radius: 8px; /* Optional: rounds the corners of the content box */
        }

        /* Styling the image */
        img {
            height: 100px;
            width: 180px;
            margin-bottom: 20px; /* Add some space between the image and content */
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Image with 100px height and 180px width -->
        <img src="1735991730_AAMRAM Logo-01.png" alt="Logo">
        <div class="content">
            <h1>Product Information</h1>
            <p><strong>Order ID:</strong></p>
            <p><strong>Product:</strong></p>
            <p><strong>Batch ID:</strong></p>
            <p><strong>Size:</strong></p>
        </div>
    </div>
</body>
</html>
