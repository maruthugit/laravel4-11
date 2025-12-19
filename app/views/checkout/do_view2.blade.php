@extends('checkout.pdf_header')
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
                Tien Ming Distribution Sdn Bhd (1537285-T)<br />                 
                10, Jalan Str 1, Saujana Teknologi Park, <br />
                48000 Rawang, Selangor, Malaysia.<br />
                Tel: 03-6734 8744<br />
                Email: enquiries@tmgrocer.com<br>
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
                {{$display_details['delivery_address_4']}}<br />
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
                <th style="width: 22%;">Item Code</th>
                <th style="width: 35%;">Description</th>
                <th style="width: 30%;">Label</th>
                <th style="width: 10%;">Quantity</th>
                <th style="width: 3%;"></th>
            </tr>
        </thead>

        <?php
        $subtotal = 0;
        $group_product_price = array();

        $tempcat = '';
        ?>

        @foreach($display_product as $product)
        <?php       
        
        // for package product
        if ($product->product_group != '')
        {
            if(!isset($group_product_price[$product->product_group]))
            {
                $group_product_price[$product->product_group] = 0;
            }
            $group_product_price[$product->product_group] += $product->total;           
        }
        else // for normal product
        {
            $subtotal = $subtotal + $product->total;

            if ($tempcat != $product->do_cat)
            {
                $tempcat = $product->do_cat;
            ?>

            <tr class="item-row<?php echo ($loopCount > 1) ? ' no-top' : '';?>">
                <td colspan="5"><?php if($tempcat=='') { ?>OTHERS<?php } else { ?> {{$tempcat}} <?php } ?></td>
            </tr>

            <?php
            }

        ?>  
            <tr>
                <td style="width: 22%;">{{$product->sku}}</td>
                <td style="width: 35%;">
                    {{$product->name}}
                </td>
                <td style="width: 30%;">{{$product->price_label}}</td>
                <td style="width: 10%;" align="center">{{$product->unit}}</td>
                <td style="width: 3%;" align="center"><img src="img/checkboxpic.png" /></td>
            </tr>
        
        <?php
        }
        ?>          
        @endforeach

        @foreach($display_group as $group)
            <tr>
                <td style="width: 22%;">{{$group->sku}}</td>
                <td style="width: 35%;">
                    {{$group->name}}
                </td>
                <td style="width: 30%;">-</td>
                <td style="width: 10%;" align="center">{{$group->unit}}</td>
                <td style="width: 3%;" align="center"><img src="img/checkboxpic.png" /></td>
            </tr>
        <?php
            $subtotal = $subtotal + $group_product_price[$group->sku];
        ?>
        @endforeach
        
    </table>  
    
    
    <br /><br />
    <table style="width: 100%;">
        <tr>
            <td>
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
            <td style="width: 50%;" valign="middle">
                <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />
                Received by Customer<br />
                Name:<br />
                IC:<br />
                Date:<br />
                Time:<br />
            </td>
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 30%;" valign="middle">
                <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />
                Send by tmGrocer<br />
                Name:<br />
                Date:<br />
                Time:<br />
            </td>
        </tr>
    </table>
    

@stop
