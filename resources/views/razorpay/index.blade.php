<?php
use Illuminate\Support\Facades\DB;

$company = DB::table('company')->first();
$name = DB::table('customers')->where('id', $salerazorpay->customer_name)->first();
$amtsum = DB::table('purchase_payment_info')->where('Invoicenumber', $salerazorpay->id)->sum('amount');
$logo = $company->logo;
$remainingAmount = $salerazorpay->Tamount - $amtsum;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #3399cc, #f5f7fa);
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

        .pay-now-btn,
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

        .pay-now-btn:hover,
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
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus {
            border-color: #3399cc;
            outline: none;
        }
    </style>
</head>

<body>

    <div class="payment-container">
        <img src="<?php echo asset('public/' . $logo); ?>" style="height:80px;width:auto;" alt="Company Logo">
        <h2>Complete Your Payment</h2>

        <div class="payment-details">
            <h3><strong>Name:</strong> <?php echo $name->customer_name; ?></h3>
            <div style="display:flex; gap:40px;">
                <p><strong>Total Amount:</strong> <?php echo $salerazorpay->Tamount; ?></p>
                <p><strong>Amount Paid:</strong> <?php echo $amtsum; ?></p>
            </div>
            <p><strong>Amount:</strong>
                <input type="text" id="amount" value="<?php echo $remainingAmount; ?>" onkeyup="validateAmount()">
            </p>
        </div>

        <button class="do" onclick="showPayButton()">Continue to Pay</button>
        <button class="pay-now-btn" id="payBtn" style="display:none;">Pay Now</button>
        <p class="footer-note">Powered by Razorpay</p>
    </div>
    <script>
        let maxAmount = <?php echo $remainingAmount; ?>;

        function validateAmount() {
            let input = document.getElementById("amount");
            let inputAmount = parseFloat(input.value);

            if (inputAmount > maxAmount) {
                alert("You cannot enter more than " + maxAmount);
                input.value = maxAmount;
            }
        }

        // 🔥 Continue to Pay → Direct Razorpay
        function showPayButton() {
            let amountField = document.getElementById("amount");
            let enteredAmount = parseFloat(amountField.value);

            if (!enteredAmount || enteredAmount <= 0) {
                alert("Enter valid amount");
                return;
            }

            $.ajax({
                url: "{{ url('/dynamic-razorpay-order') }}",
                type: "GET",
                data: {
                    _token: "{{ csrf_token() }}",
                    amount: enteredAmount,
                    sale_id: "<?php echo $salerazorpay->id; ?>",
                    customer_name: "<?php echo $salerazorpay->customer_name; ?>"

                },
                success: function(data) {

                    let options = {
                        key: "{{ env('RAZORPAY_KEY') }}",
                        amount: (enteredAmount * 100).toFixed(0),
                        currency: "INR",
                        name: "<?php echo $company->name; ?>",
                        description: "Partial Payment",
                        order_id: data.order_id,

                        handler: function(response) {
                            $.ajax({
                                type: "GET",
                                url: "{{ route('paymentsRazorpay') }}",
                                data: {
                                    payment_id: response.razorpay_payment_id,
                                    order_id: response.razorpay_order_id,
                                    signature: response.razorpay_signature,
                                    sale_id: "<?php echo $salerazorpay->id; ?>",
                                    amount: enteredAmount,
                                    customer_name: "<?php echo $salerazorpay->customer_name; ?>"
                                },
                                success: function() {
                                    alert("Payment Successful!");
                                    window.close();
                                }
                            });
                        },

                        prefill: {
                            name: "<?php echo $salerazorpay->customer_name; ?>",
                            email: "<?php echo $salerazorpay->email_id; ?>"
                        },
                        theme: {
                            color: "#3399cc"
                        }
                    };

                    let rzp = new Razorpay(options);
                    rzp.open();
                },
                error: function() {
                    alert("Unable to create order");
                }
            });
        }
    </script>

</body>

</html>
