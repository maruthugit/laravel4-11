<!DOCTYPE html>
<html lang="en-US">
    <body style="font-family: sans-serif; font-size: 14px; max-width: 600px; margin: 0 auto;">
        <p>Dear {{ $delivery_name }},</p>
        <p>Thank you for shopping with us. The payment for your order has been successfully processed, and shipping request was sent to our delivery team for their further action to deliver the goods on time.</p>
        
        <p>
            Delivery To:<br>
            @if (isset($delivery_name))
                {{ $delivery_name }}<br>
            @endif

            @if (isset($delivery_contact_no))
                {{ $delivery_contact_no }}<br>
            @endif

            @if (isset($delivery_addr_1))
                {{ $delivery_addr_1 }}<br>
            @endif

            @if (isset($delivery_addr_2))
                {{ $delivery_addr_2 }}<br>
            @endif

            @if (isset($delivery_city))
                {{ $delivery_city }}<br>
            @endif

            @if (isset($delivery_postcode))
                {{ $delivery_postcode }}<br>
            @endif

            @if (isset($delivery_state))
                {{ $delivery_state }}<br>
            @endif

            @if (isset($delivery_country))
                {{ $delivery_country }}<br>
            @endif
        </p>
        @if (isset($special_msg) && ! empty($special_msg))
            <p>Special Message: {{ $special_msg }}</p>
        @endif
        <table style="border: 1px solid #000; border-collapse: collapse; width: 100%">
            <thead>
                <tr>
                    <th style="border: 1px solid #000; padding: 5px 10px; text-align: left; width: 30%;">SKU</th>
                    <th style="border: 1px solid #000; padding: 5px 10px; text-align: left; width: 50%;">Item Name</th>
                    <th style="border: 1px solid #000; padding: 5px 10px; text-align: left; width: 20%;">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pickup_details as $pickup_detail)
                    <tr>
                        <td style="border: 1px solid #000; padding: 5px 10px; width: 30%;">{{ $pickup_detail['sku'] }}</td>
                        <td style="border: 1px solid #000; padding: 5px 10px; width: 50%;">{{ $pickup_detail['name'] }}</td>
                        <td style="border: 1px solid #000; padding: 5px 10px; width: 20%;">{{ $pickup_detail['qty'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p>Have a nice day!</p>
        <hr style="border-top: 1px solid #000; border-bottom: 0;">
        <p style="font-size: 12px"><a href="http://ebuzzzz.tmgrocer.com/index.php?action=form&id=1064" target="_blank"><b>Subscribe</b></a> to our newsletter today to receive updates on the latest news and special offers!</p>
        <table style="background-color: #f5f5f5; font-size: 12px; line-height: 16px; padding: 5px; width: 100%;">
            <tr>
                <td rowspan="4" style="border-right: 3px solid #00ab46; width: 173px;">
                    <img src="http://tmgrocer.com/email/img/logo.png" width="168" height="80">
                </td>
                <td style="padding-left: 10px;"><b>Customer Service</b></td>
            </tr>
            <tr>
                <td style="padding-left: 10px;">
                    <img src="http://tmgrocer.com/email/img/but_map.png" width="12" height="12" style="margin-right: 5px;">
                    10, Jalan Str 1, Saujana Teknologi Park, Rawang, 48000 Rawang, Selangor, Malaysia</span>
                </td>
            </tr>
            <tr>
                <td style="padding-left: 10px;">
                    <img src="http://tmgrocer.com/email/img/but_tel.png" width="12" height="12" style="margin-right: 5px;">
                    603 2241 6637
                    <img src="http://tmgrocer.com/email/img/but_fax.png" width="12" height="12" style="margin-left: 10px; margin-right: 5px;">
                    603 2242 3837
                </td>
            </tr>
            <tr>
                <td style="padding-left: 10px;">
                    <a href="http://www.tmgrocer.com" target="_blank" style="margin-right: 2px;"><img src="http://tmgrocer.com/email/img/but_web_limegreen.png"></a>
                    <a href="mailto:customersupport@tmgrocer.com" style="margin-right: 2px;"><img src="http://tmgrocer.com/email/img/but_email_limegreen.png"></a>
                    <a href="https://maps.app.goo.gl/QH6yoDBuWaBWTZWu9" target="_blank" style="margin-right: 2px;"><img src="http://tmgrocer.com/email/img/but_map_limegreen.png"></a>
                    <a href="http://www.facebook.com/tmgrocer" target="_blank" style="margin-right: 2px;"><img src="http://tmgrocer.com/email/img/facebook16.png"></a>
                    <a href="http://www.twitter.com/tmgrocer" target="_blank" style="margin-right: 1px;"><img src="http://tmgrocer.com/email/img/twitter16.png"></a>
                </td>
            </tr>
        </table>
    </body>
</html>
