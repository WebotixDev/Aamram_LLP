<?php


use Razorpay\Api\Api;
$api = new Api('rzp_live_osImyM9ZsLZsBn','srvAm6T1ldlwyKsPIKOkfEeN');


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Aamram </title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #3399cc, #f5f7fa);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .payment-container {
            background: #fff;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            padding: 20px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .payment-container h2 {
            color: #3399cc;
        }

        .payment-container img {
            width: 100px;
        }

        .payment-details {
            margin: 20px 0;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 5px;
            text-align: left;
        }

        .payment-details p {
            margin: 5px 0;
            font-size: 14px;
        }

        .pay-now-btn {
            display: inline-block;
            background: #3399cc;
            color: #fff;
            text-transform: uppercase;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }

        .pay-now-btn:hover {
            background: #2b88b8;
        }
        .do {
            display: inline-block;
            background: #3399cc;
            color: #fff;
            text-transform: uppercase;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }

        .do:hover {
            background: #2b88b8;
        }

        .footer-note {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }

        input[type="text"] {
    width: 70%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-top: 10px;
    box-sizing: border-box; /* Ensure padding is included in width */
    transition: border-color 0.3s ease;
}

input[type="text"]:focus {
    border-color: #3399cc; /* Highlight the border color when the input is focused */
    outline: none;
}
button {
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background-color: #45a049;
}


    </style>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php
use Illuminate\Support\Facades\DB;
$company = DB::table('company')->first();
$name = DB::table('customers')
    ->where('id', $salerazorpay->customer_name)
    ->first();


    
      $amtsum = DB::table('purchase_payment_info')
            ->where('Invoicenumber', $salerazorpay->id)
            ->sum('amount');


$logo = DB::table('company')->value('logo');



?>
   <div class="payment-container">
    <img src="<?php echo asset('public/' . $logo) ?>" style="height: 80px; width: auto;" alt="Company Logo">
    <h2>Complete Your Payment</h2>
    <form method="get" action="" id="formpayment">
    <div class="payment-details">
        <h3><strong>Name:</strong> &nbsp;<?php echo $name->customer_name; ?></h3>
  <div style="display: flex; gap: 40px;">
    <p><strong>Total Amount:</strong> <?php echo $salerazorpay->Tamount; ?></p>
    <p><strong>Amount Paid:</strong> <?php echo $amtsum; ?></p>
</div>
   <p><strong>Amount:</strong>&nbsp;&nbsp;
    <input type="text" id="amount" onkeyup="validateAmount()" name="amount" 
        value="<?php 
            if (isset($_GET['amount'])) {
                echo $_GET['amount'];
            } else {
                echo $salerazorpay->Tamount - $amtsum;
            }
        ?>" />
</p>

        </div>
        <button type="submit" class="do">Countinue to Pay</button>
        </form>
        <?php
        if (isset($_GET['amount'])) {
    $amount = $_GET['amount'];

    ?>
    <style> #formpayment {display: none;}   </style>

    <button class="pay-now-btn" id="razorpay-button">Pay Now</button>
    <p class="footer-note">Powered by Razorpay</p>
    <?php
} else {
    //$amount = $salerazorpay->amount*100;
    $amount= 100;
}

$order = $api->order->create([
    'receipt' => 'order_' . time(),
    'amount' => $amount * 100,
    'currency' => 'INR',
    'payment_capture' => 1,
    'notes' => [
        'sale_id' => $salerazorpay->id,
        'customer_name' => $salerazorpay->customer_name
    ]
]);

$orderId = $order['id'];


$orderId = $order['id'];

?>

</div>




    <script>
//     var options = {
//         "key": "rzp_live_osImyM9ZsLZsBn", // Razorpay Key ID
//         "amount": "<?php echo $amount; ?>", // Amount in paise
//         "currency": "INR",
//         "name": "<?php echo  $company->name ?>",
//         "description": "Payment for your order",
//         "image": "images/logo.png",
//         "order_id": "<?php echo $orderId; ?>", // Order ID generated by Razorpay
//         "handler": function (response) {
//             // Make AJAX request to the given route
//             $.ajax({
//                 type: "GET",
//                 url: "{{ route('paymentsRazorpay') }}",
//                 data: {
//                     payment_id: response.razorpay_payment_id,
//                     order_id: response.razorpay_order_id,
//                     signature: response.razorpay_signature,
//                     sale_id: "<?php echo $salerazorpay->id; ?>",
//                     amount: "<?php echo $amount; ?>",  // Amount to be sent
//                     customer_name: "<?php echo $salerazorpay->customer_name; ?>"
//                 },
//                 success: function(response) {
//                     console.log(response); // For debugging purposes
//                     if(response.success) {
//                         alert("Payment successful!"); // Show a success message
//                         window.close(); // Close the tab
//                         window.history.back(); // Navigate back
//                     }
//                 },
//                 error: function(xhr, status, error) {
//                     console.log("Error: " + error); // Log any errors in the request
//                 }
//             });
//         },
//         "prefill": {
//             "name": "<?php echo $salerazorpay->customer_name; ?>",
//             "email": "<?php echo $salerazorpay->email_id; ?>"
//         },
//         "theme": {
//             "color": "#3399cc"
//         }
//     };

//     var rzp = new Razorpay(options);
//     document.getElementById('razorpay-button').onclick = function(e) {
//         rzp.open();
//         e.preventDefault();
//     };
</script>



    <script>
    var options = {
        
        "key": "rzp_live_osImyM9ZsLZsBn", // Razorpay Key ID
        "amount": "<?php echo $amount; ?>", // Amount in paise
        "currency": "INR",
        "name": "<?php echo  $company->name ?>",
        "description": "Payment for your order",
        "image": "images/logo.png",
        "order_id": "<?php echo $orderId; ?>", // Order ID generated by Razorpay
        "handler": function (response) {
           var payment_id= response.razorpay_payment_id;
           console.log(payment_id);
            // Make AJAX request to the given route
            $.ajax({
                type: "GET",
                url: "{{ route('paymentsRazorpay') }}",
                data: {
                    payment_id: payment_id,
                    order_id: response.razorpay_order_id,
                    signature: response.razorpay_signature,
                    sale_id: "<?php echo $salerazorpay->id; ?>",
                    amount: "<?php echo $amount; ?>",  // Amount to be sent
                    customer_name: "<?php echo $salerazorpay->customer_name; ?>"
                },
                success: function(response) {
                    console.log(response+">>>>>>>"); // For debugging purposes
                    if(response.success) {
                        console.log(response);
                        alert("Payment successful!"); // Show a success message
                        window.close(); // Close the tab
                        window.history.back(); // Navigate back
                    }
                },
                error: function(xhr, status, error) {
                    console.log("Error: " + error); // Log any errors in the request
                }
            });
        },
        "prefill": {
            "name": "<?php echo $salerazorpay->customer_name; ?>",
            "email": "<?php echo $salerazorpay->email_id; ?>"
        },
        "theme": {
            "color": "#3399cc"
        }
    };

    var rzp = new Razorpay(options);
    document.getElementById('razorpay-button').onclick = function(e) {
        rzp.open();
        e.preventDefault();
    };
</script>
<script>
function validateAmount() {
    let inputAmount = document.getElementById("amount").value;
    let maxAmount = <?php echo $salerazorpay->Tamount - $amtsum; ?>; // Get the remaining amount from PHP

    if (inputAmount !== "" && parseFloat(inputAmount) > maxAmount) {
        alert("You cannot enter an amount greater than " + maxAmount);
        document.getElementById("amount").value = maxAmount; // Reset input to max allowed
    }
}
</script>
<!-- <script>
        // Function to display the popup
        function showPopup() {
            document.getElementById("popup").style.display = "flex"; // Show popup
        }

        // Function to save the amount
        function save() {
            // Get the value of the amount input field
            var amountas = document.getElementById('popupAmountInput').value;

            // Show the value in an alert
            alert("Amount saved: ₹" + amountas);

            document.getElementById("amountsss").value=amountas;

            // Hide the popup after saving
            document.getElementById("popup").style.display = "none";
        }

        // Close popup when clicked outside of the content area
        window.onclick = function(event) {
            if (event.target == document.getElementById("popup")) {
                document.getElementById("popup").style.display = "none";
            }
        }
    </script> -->

</body>
</html>
