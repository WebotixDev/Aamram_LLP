
<html>
<style>
    /* Global styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f2f2f2;
    }

    /* Print-specific styles to hide the button */
    @media print {
        .dontPrint button {
            display: none;

        }
        .button{
            display: none;
        }
    }

    /* Container box styles */
    .rcorners2 {
        background-color: #fff;
        border: 2px solid #ccc;
        border-radius: 15px;
        padding: 20px;
        width: 750px;
        margin: 20px auto;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    /* Header styles */
    .head {
        border: 2px solid #000;
        border-radius: 50%;
        width: 100px;
        height: 100px;
        margin: 0 auto 10px;
        text-align: center;
        line-height: 100px; /* Center content vertically */
        font-size: 18px;
    }

    /* Title styles */
    .head1 {
        border: 2px solid #000;
        border-radius: 10px;
        padding: 5px;
        width: 120px;
        text-align: center;
        font-size: 16px;
    }

    /* Table styles */
    table {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
    }

    table td {
        padding: 10px;
        vertical-align: top;
    }

    /* Receipt number and date styles */
    .receipt-info {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
    }

    /* Footer note styles */
    .footer-note {
        font-size: 12px;
        text-align: center;
        margin-top: 10px;
    }

    /* Note styles */
    .note {
        font-size: 14px;
        padding-top: 60px;
        padding-left: 80px;
        text-align: center;
        font-style: italic;
    }

    /* Signature area styles */
    .signature {
        text-align: center;
    }

    .row {
    display: flex; /* Use flexbox for horizontal alignment */
    justify-content: center; /* Center align items horizontally */
    align-items: center; /* Center align items vertically */
}

.button {
    margin-right: 10px; /* Adjust spacing between buttons if needed */
}

.back, .dontPrint {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    border-radius: 5px; /* Adding border radius for rounded corners */
    text-decoration: none; /* Remove underline for links */
}

.back:hover, .dontPrint:hover {
    background-color: #0056b3; /* Darker background color on hover */
}

.dontPrint {
    display: inline-block; /* Ensure the anchor behaves like a button */
}
.header {
        display: flex;
        justify-content: center; /* Center items horizontally */
        align-items: center; /* Center items vertically */
        text-align: center;
        width: 100%; /* Ensure full width */
    }
</style>
<body>
@php
$company = DB::table('company')->first();
$logo = DB::table('company')->value('logo');
$signature = DB::table('company')->value('sign');


@endphp
<div class="row">
    <div class="button">
        <input id="back" name="back" type="button" class="back" onclick="javascript:window.close()" value="Back" />
    </div>
    <div class="button">
        <a href="javascript:window.print()" class="dontPrint">
            Print
        </a>
    </div>

</div>


    <center>




        <div class="rcorners2">
            <table>
            <tr >
<td  id="color" colspan="5" height="" style="border-bottom:1px solid black;width: 100%;padding:10px;">
<div id="hide"  >
    <div class="header">
        <!-- Company Logo -->
        <div class="logo">
            <img src="<?php echo asset('public/' . $logo) ?>" style="height: 80px; width: auto;" alt="Company Logo">
        </div>
    </div>
<div style="width: 100%; float:right;text-align:center; min-height:20px;color:black; ">
<strong style="font-size:25px; font-family:serif;color:red;"><?php echo $company->name;?></strong> <br>

<font style="font-size:14px; text-align:left;"><?php echo $company->address;?>
<p style="margin-top:2px;text-align:center;margin-bottom:0px;font-size:12px;"> <?php echo $company->phone;?>
</p>

<?php //if($gstno['gst_no']!=''){?>
<b style='padding-top:3px;'><strong><?php //echo $gstno['gst_no'];?></strong></b></br>
<?php //} ?>
</font>
</div>
</div>
</td>


</tr>
                <tr>
                    <td colspan="2" style="border-bottom: 1px solid black;">
                        <div style="display: flex; justify-content: space-between;">
                            <div style="width: 45%;">
                                Receipt No :  <?php echo $Sale_payment->ReceiptNo;  ?>
                            </div>
                            <div style="width: 45%; text-align: right;">
                    Date : <?php echo date("d M, Y", strtotime($Sale_payment->PurchaseDate)); ?>
                </div>

                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div style="width: 45%; float: left;">Received With Thanks From M/s./Shri</div>
                        <div style="text-align: center; width: 45%; float: left; border-bottom: 1px solid black; font-size: 16px;">
                            <?php echo $Sale_payment->customer_name ; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div style="width: 20%; float: left;">a sum of Rs.</div>
                        <div style="text-align: center; width: 70%; float: left; border-bottom: 1px solid black;">
                            <?php echo $Sale_payment->amt_pay; ?> Rupees Only/-
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div style="width: 30%; float: left; padding-top: 10px;">By Cash/Online/Cheque No</div>
                        <div style="text-align: center; width: 20%; float: left; border-bottom: 1px solid black; padding-top: 30px;">
                            <?php echo $Sale_payment->mode; ?>
                        </div>
                    </td>
                </tr>
            </table>

            <table>
                <tr>
                    <td style="width: 50%;">
                        <div class="head1">
                            <h3>RS. <?php echo $Sale_payment->amt_pay; ?></h3>
                        </div>
                        <p class="note">(Cheques subject to Realisation)</p>
                    </td>

                    <td style="width: 50%;" class="signature">
                        <h5>For
                        </h5>
                        <img src="<?php echo asset('public/' . $signature) ?>" style="height: 40px; width: auto;" alt="Company Logo">
                        <br>
                        <h5>(Authorised Signatory)</h5>
                    </td>
                </tr>
            </table>
        </div>
    </center>

</body>
<script>
      function Checkback()
{

window.location='<?php //echo base_url("index.php/Form_controller/admission"); ?>';
}
</script>
</html>
