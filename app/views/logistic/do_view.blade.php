@extends('logistic.pdf_header')
@section('content')
  
    <page_header>
    </page_header>
    <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 100%; text-align: right">
                    Page [[page_cu]]/[[page_nb]] ({{$display_details['do_no']}})
                </td>
            </tr>
        </table>
    </page_footer>

    <table style="width: 100%;">
        <tr>
            <td style="width: 50%;">
                <img width="100px;" src="img/invoice_po_logo.jpg" /><br /><br />
            </td>
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 30%;">
                <font size="3"><strong>DELIVERY ORDER</strong></font>
            </td>
        </tr>
    
        <tr>
            <td style="width: 50%;" valign="middle">
                Jocom MShopping Sdn. Bhd.<br />                 
                Unit 9-1, Level 9,<br />
                Tower 3, Avenue 3, Bangsar South,<br />
                No. 8, Jalan Kerinchi,<br />
                59200 Kuala Lumpur.<br />
                Tel: 03-2241 6637 Fax: 03-2242 3837<br />
                <br />
            </td>
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 30%;" valign="middle">
                DO No: {{$display_details['do_no']}}<br />
                DO Date: {{$display_details['do_date']}}<br />
                Payment terms: {{$display_details['payment_terms']}}<br />
                Transaction ID: {{$display_details['transaction_id']}}<br />
                Payment ID: {{$display_details['payment_id']}}<br />
            </td>
        </tr>
        
        <tr>
            <td style="width: 50%;">
                <br />
                {{$display_details['delivery_name']}}<br />
                {{$display_details['delivery_address_1']}}<br />
                <?php if ($display_details['delivery_address_2'] != "") echo $display_details['delivery_address_2']."<br />"; ?>
                {{$display_details['delivery_address_3']}}<br />
                {{$display_details['delivery_contact_no']}}<br />
                Date {{$display_details['transaction_date']}} <br />
            </td>
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 30%;" valign="middle">
                Special instructions:<br />
                {{$display_details['special_instruction']}}<br />
            </td>
        </tr>
    </table>
    
    <br /><br />

    <table border="1" style="width: 100%;">
        <thead>         
            <tr>
                <th style="width: 20%;">Item Code</th>
                <th style="width: 35%;">Description</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 35%;">Remark</th>
            </tr>
        </thead>
        @foreach($display_details['items'] as $product)        
            <tr>
                <?php print_r($product); ?>
                <td style="width: 20%;">{{$product['sku']}}</td>
                <td style="width: 35%;">
                    {{$product['description']}}<br />
                    {{$product['price_label']}}
                </td>
                <td style="width: 10%;" align="center">{{$product['qty']}}</td>
                <td style="width: 35%;">{{$product['remark']}}</td>
            </tr>
        @endforeach
    </table> 
    
    
    <br /><br />
    <table style="width: 100%;">
        <tr>
            <td>
                Remark:<br />
                {{$display_details['remark']}}<br /><br /><br />
                This is a computer generated document. No signature required.
                <br />
                Goods sold are not refundable, returnable or exchangeable.
            </td>
        </tr>    
    </table>
    
    
    <br /><br />
    <br /><br />
    <br /><br />
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%;" valign="bottom">
                <?php
                if (isset($display_details['signature_file']) && $display_details['signature_file'] != "" && file_exists(Config::get('constants.LOGISTIC_SIG_PATH') . '/' . urlencode($display_details['signature_file'])))
                {
                ?>
                <img width="200px;" src="{{Config::get('constants.LOGISTIC_SIG_PATH')}}/{{$display_details['signature_file']}}" /><br />
                <!-- <img width="100px;" src="{{Config::get('constants.LOGISTIC_SIG_PATH')}}/{{$display_details['signature_file']}}" /><br /> -->
                <?php
                }
                ?>
                <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />
            </td>
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 30%;" valign="bottom">
                <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />
            </td>
        </tr>
        <tr>
            <td style="width: 50%;" valign="top">
                Received by Customer<br />
                Name: {{$display_details['sign_name']}}<br />
                IC: {{$display_details['sign_ic']}}<br />
                Date: {{$display_details['sign_date']}}<br />
                Time: {{$display_details['sign_time']}}<br />
            </td>
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 30%;" valign="top">
                Send by JOCOM<br />
                Name: {{$display_details['driver_name']}}<br />
                Date: {{$display_details['sign_date']}}<br />
                Time: {{$display_details['sign_time']}}<br />
            </td>
        </tr>
    </table>
    

@stop


