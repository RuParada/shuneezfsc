<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Shuneez</title>
    </head>
    <body style="border:3px solid #662d91;width:599px;padding:20px; font-family: 'Open Sans', sans-serif;font-size:14px;">
    	<table style="width:600px;background:#fff;border:none;border-radius:6px" cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<td></td>
			<td><a href="{!! URL::to('/') !!}"><img src="{!! URL::to('assets/images/Shuneez-logo-color.png') !!}"></a></td>
			<td><a href="#"><img src="{!! URL::to('images/app-download.gif') !!}" style="float:right;"></a></td>
			<td></td>
		</tr>
	</tbody>

</table>
<hr style="height:1px; background-color: #39b54a;">

<table style="width:600px;background:#fff;border:none;border-radius:6px" cellspacing="0" cellpadding="0" border="0">
    <tbody>
        <tr>
            <td colspan="2">    Hi {{NAME}} ,</td>
        </tr>
        <tr>
            <td colspan="2">    You have a new order , please allow us time our customer support team will call you to confirm the order to start preparing the food</td>
        </tr>
        <tr>
            <td>
                <p style="color:#3e3e3e;font-weight: bold;">ORDER INFORMATION:</p>
                <p style="color:#3e3e3e;font-weight: bold;">Invoice #:<span style="font-weight: normal;margin-left:10px;">{{INVOICE}}</span></p>
                <p style="color:#3e3e3e;font-weight: bold;">Date added:<span style="font-weight: normal;margin-left:10px;">{{ORDERDATE}}</span></p>
                <p style="color:#3e3e3e;font-weight: bold;">Payment method:<span style="font-weight: normal;margin-left:10px;">{{PAYMENT}}</span></p>
                <p style="color:#3e3e3e;font-weight: bold;">CUSTOMER INFORMATION:</p>
                <p style="color:#3e3e3e;font-weight: bold;">Email:<span style="font-weight: normal;margin-left:10px;"><a href="#">{{EMAIL}}</a></span></p>
                <p style="color:#3e3e3e;font-weight: bold;">Mobile:<span style="font-weight: normal;margin-left:10px;">{{MOBILE}}</span></p> 
            </td>
            <td></td>
        </tr>
    </tbody>
</table>



<table style="border: 1px solid #cdcdcd;width:600px;border-spacing: 0;">
    <tbody>
        <tr>
            <th colspan="2" style="background:#39b54a;color:#fff;font-weight:normal;border-right:1px solid #ccc;padding:10px;">Product Details</th>
            <th style="background:#39b54a;color:#fff;font-weight:normal;border-right:1px solid #ccc;padding:10px;">Price</th>
            <th style="background:#39b54a;color:#fff;font-weight:normal;border-right:1px solid #ccc;padding:10px;">Quantity</th>
            <th style="background:#39b54a;color:#fff;font-weight:normal;padding:10px;">Sub Total</th>
        </tr>
        <tr >
            <td style="padding: 10px;border-bottom: 1px solid #cdcdcd;">Choices:Dry</td>
            <td style="padding: 10px;text-align: center;border-bottom: 1px solid #cdcdcd;border-right: 1px solid #cdcdcd;">KSh 20.00</td>
            <td style="padding: 10px;text-align: center;border-bottom: 1px solid #cdcdcd;border-right: 1px solid #cdcdcd;">KSh 150.00</td>
            <td style="padding: 10px;text-align: center;border-bottom: 1px solid #cdcdcd;border-right: 1px solid #cdcdcd;">1</td>
            <td style="padding: 10px;text-align: center;border-bottom: 1px solid #cdcdcd;">KSh 170.00</td>
        </tr>
        <tr>
            <td colspan="3" style="padding: 10px;">Shipping Address</td>
            <td colspan="2" style="padding: 10px;border-left: 1px solid #cdcdcd;border-bottom:1px solid #cdcdcd;">Item(s) Subtotal: KSh 170.00</td>
            
        </tr>
        <tr>
            <td colspan="3" ></td>
            <td colspan="2" style="padding: 10px;border-left: 1px solid #cdcdcd;border-bottom:1px solid #cdcdcd;">Delivery fee: KSh 0.00</td>
            
        </tr>
        <tr>
            <td colspan="3"></td>
            <td colspan="2" style="padding: 10px;border-left: 1px solid #cdcdcd;border-bottom:1px solid #cdcdcd;">    VAT (0%): KSh 0.00</td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td colspan="2" style="padding: 10px;border-left: 1px solid #cdcdcd;border-bottom:1px solid #cdcdcd;">    Service tax (12.5%) : KSh 0.00</td>
        </tr>
        <tr>
            <td colspan="3"></td>
            <td colspan="2" style="padding: 10px;border-left: 1px solid #cdcdcd;">Grand Total : KSh 170.00</td>
        </tr>
    </tbody>
</table>

<table>
    <tbody>
        <tr>
            <td>For any further assistance or queries, please email us at <a href="#">support@shuneez.com</a> or call us on +919999999999</td>
        </tr>
        <tr>
            <td>Best Regards,</td>
        </tr>
        <tr>
            <td>Shuneez Team</td>
        </tr>

    </tbody>
</table>
<hr style="height:1px; background-color: #39b54a;">

<table style="width:599px;">
    <tbody>
        <tr>
            <td style="width:286px;"><img src="images/mail.png" style="margin-right:5px;">Mail us support@swiftshoppa.com</td>
            <td style="width:150px;"><img src="images/COD.png"></td>
            <td style="width:26px;"><a href="#"><img src="images/fb.png"></a></td>
            <td style="width:26px;"><a href="#"><img src="images/twit.png"></a></td>
            <td style="width:26px;"><a href="#"><img src="images/g+.png"></a></td>
            <td style="width:26px;"><a href="#"><img src="images/linked.png"></a></td>
           

        </tr>
    </tbody>
</table>

    </body>
    </html>
