<html>
    
<style type="text/css">
    
    body{
        max-width: 800px;
        border: solid 1px #888080;
        padding: 10px;
        margin: auto;
        background-color: #fff;
        margin-bottom: 10px;
        margin-top: 10px;
        }
        
    html{
        background-color: #f3f3f3;
        }
    @media print{
        
        #printpagebutton { 
            display:none; 
        }

        .address_container{
            font-size:12px
        }

        body{
            border: solid 1px white;
            max-width: 100%;
            font-size: 12px;
            font-family: "Arial", Georgia, Serif;

        }
        #download{
            display: none;
        } 
    }
</style>

    <body>
        <table style="width:100%;">
            <tr>
                <td style="text-align:right;font-size: 10px;width: 80%;"></td>
                <td style="text-align:right;font-size: 10px;"></td>
            </tr>
            <tr>
                <td style="text-align:right;font-size: 10px;width: 80%;"></td>
                <td style="text-align:right;font-size: 10px;"></td>
            </tr>
        </table>
        <table>
            <tr>
                 <td style="text-align: left;font-weight: bold;font-size: 25px;"><img width="100px;" src="/img/buyday_logo.png"></td>
                 <td>
                     浙江省杭州市萧山区经济技术开发区<br>金一路37号A101-5室<br>电话号码 : +86-18806661446
                     
                 </td>
            </tr>
        </table>
        <table style="width:100%; "> 
            <tr>
                <td style="text-align: center;font-weight: bold;font-size: 25px;">TAX INVOICE</td>
            </tr>
            <tr>
                <td style="text-align: center;font-weight: 100;font-size: 25px;">发票</td>
            </tr>
            <tr>
                <td style="text-align:left;font-size: 10px;"></td>
            </tr>
            <tr>
                <td style="text-align:left;font-size: 10px;"></td>
            </tr>
        </table>
        <table style="width:100%;">
            <tr>
                <td style="text-align:right;font-size: 12px;width: 65%;"></td>
                <td style="text-align:left;font-size: 12px;">Invoice No 发票号码 : <?php echo $invoiceInfo['invoice_no'];?></td>
            </tr>
            <tr>
                <td style="text-align:right;font-size: 12px;width: 65%;"></td>
                <td style="text-align:left;font-size: 12px;">Invoice Date 发票日期 : <?php echo $invoiceInfo['invoice_date'];?></td>
            </tr>
            <tr>
                <td style="text-align:right;font-size: 12px;width: 65%;"></td>
                <td style="text-align:left;font-size: 12px;">Preference No 优先权号码 : None</td>
            </tr>
            <tr>
                <td style="text-align:left;font-size: 12px;width: 65%;">HAWB:</td>
                <td style="text-align:left;font-size: 12px;">Payment Term 付款条约 : Cash/cc</td>
            </tr>
            <tr>
                <td style="text-align:left;font-size: 12px;width: 65%;">运单号码</td>
                <td style="text-align:left;font-size: 12px;">Transaction ID 交易编号 : <?php echo $invoiceInfo['invoice_transaction_id'];?></td>
            </tr>
        </table>
        <table style="width:100%; padding: 0px;border-top: double;margin-top: 10px;">
            <tr style="">
                <td style="width: 50% ;border-right: solid 1px #000;">
                    <table>
                        <tr>
                            <td colspan="2">FROM : <span style="font-size:10px;"></span></td>
                        </tr>
                        <tr>
                            <td style="text-align:left;font-size: 12px;vertical-align: text-top;">Company Name : <br><span style="font-size:9px;">(公司)</span></td>
                            <td style="text-align:left;font-size: 12px;vertical-align: text-top;"><?php echo $issuer['issuer_name'];?></td>
                        </tr>
                        <tr>
                            <td style="text-align:left;font-size: 12px;vertical-align: text-top;">Address : <br><span style="font-size:9px;">(地址)</span></td>
                            <td style="text-align:left;font-size: 12px;vertical-align: text-top;"><?php echo $issuer['issuer_address_1'];?>
                                <br> <?php echo $issuer['issuer_address_2'];?>
                                <br> <?php echo $issuer['issuer_address_3'];?>
                                <?php echo $issuer['issuer_address_4'];?>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:left;font-size: 12px;">Tel No : <br><span style="font-size:9px;">(电话)</span></td>
                            <td style="text-align:left;font-size: 12px;vertical-align: text-top;"><?php echo $issuer['issuer_tel'];?></td>
                        </tr>
<!--                        <tr>
                            <td style="text-align:left;font-size: 12px;">Serial Number of ID Number: <br><span style="font-size:9px;">(统编号或号码)</span></td>
                            <td style="text-align:left;font-size: 12px;vertical-align: text-top;"><?php echo $invoiceInfo['invoice_transaction_id'];?></td>
                        </tr>-->
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top;" >
                    <table>
                        <tr>
                            <td colspan="2">TO : <span style="font-size:10px;">(Receiver 收件方)</span></td>
                        </tr>
                        <tr>
                            <td style="text-align:left;font-size: 12px;vertical-align: text-top;">Company Name : <br><span style="font-size:9px;">(公司)</span></td>
                            <td style="text-align:left;font-size: 12px;vertical-align: text-top;"><?php echo $invoiceTo['invoice_to_name'];?></td>
                        </tr>
                        <tr>
                            <td style="text-align:left;font-size: 12px;vertical-align: text-top;">Address : <br><span style="font-size:9px;">(地址)</span></td>
                            <td style="text-align:left;font-size: 12px;vertical-align: text-top;"><?php echo $invoiceTo['invoice_to_address_1'];?>
                                <?php if($invoiceTo['invoice_to_address_2'] != '') {echo "<br>".$invoiceTo['invoice_to_address_2']; }?>
                                <br><?php echo $invoiceTo['invoice_to_address_3'];?>.
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align:left;font-size: 12px;">Contact Name : <br><span style="font-size:9px;">(联络人姓名)</span></td>
                            <td style="text-align:left;font-size: 12px;vertical-align: text-top;"><?php echo $invoiceTo['invoice_to_recipient'];?></td>
                        </tr>
                        <tr>
                            <td style="text-align:left;font-size: 12px;">Tel No : <br><span style="font-size:9px;">(电话)</span></td>
                            <td style="text-align:left;font-size: 12px;vertical-align: text-top;"><?php echo $invoiceTo['invoice_to_tel'];?></td>
                        </tr>
<!--                        <tr>
                            <td style="text-align:left;font-size: 12px;">Serial Number of ID Number : <br><span style="font-size:9px;">(统编号或号码)</span></td>
                            <td style="text-align:left;font-size: 12px;vertical-align: text-top;"></td>
                        </tr>-->
                    </table>
                </td>
            </tr>
        </table>
        <table style="width:100%; padding: 0px;margin-top:30px;" border="1" cellpadding="0" cellspacing="0">
            <tr style="font-size:12px;text-align: center;">
                <td style="padding: 10px;">DESCRIPTION OF GOODS <br>托寄物名称 </td>
                <td style="padding: 10px;">WEIGHT <br>重量 (KG)</td>
                <td style="padding: 10px;">PIECE <br>件数</td>
                <td style="padding: 10px;">QUANTITY <br>数量</td>
                <td style="padding: 10px;">UNIT PRICE <br>单价</td>
                <td style="padding: 10px;">VALUE <br>金额</td>
            </tr>
            <?php foreach ($productItems as $key => $value) { ?>
                <tr style="font-size:12px;text-align: left;">
                    <td style="padding: 10px;"><?php echo $value['product_name']."<br>".$value['product_label']; ?></td>
                    <td style="padding: 10px;"><?php echo $value['product_weight']; ?></td>
                    <td style="padding: 10px;text-align: center;"><?php echo $value['product_piece']; ?></td>
                    <td style="padding: 10px;text-align: center;"><?php echo $value['product_quantity']; ?></td>
                    <td style="padding: 10px;"><?php echo $BusinessCurrency; ?> <?php echo number_format($value['product_unit_price'], 2, '.', ''); ?>
<!--                        <br>
                        <span style="font-size:9px;">(<?php echo $AlternativeCurrency; ?> <?php echo number_format($value['product_alternative_unit_price'], 2, '.', ''); ?>)</span>-->
                    </td>
                    <td style="padding: 10px;"><?php echo $BusinessCurrency; ?> <?php echo number_format($value['product_total_price'], 2, '.', ''); ?>
                        <!--<br><span style="font-size:9px;">(<?php echo $AlternativeCurrency; ?> <?php echo number_format($value['product_alternative_total_price'], 2, '.', ''); ?>)</span>-->
                    </td>
                </tr>
            <?php } ?>
<!--            <tr style="font-size:12px;text-align: left;">
                <td style="padding: 6px;" rowspan="2"></td>
                <td style="padding: 6px;" colspan="5">Total Declared Value 申报总价值 (USD <?php //echo $alternativeAmount; ?>)</td>
            </tr>-->
            <tr style="font-size:12px;text-align: left;">
                <td style="padding: 4px 4px 4px 10px; " rowspan="4"></td>
                <td style="padding: 4px 4px 4px 10px;  text-align: right;" colspan="4">Total Sales 总销售量</td>
                <td style="padding: 4px 4px 4px 10px; " ><?php echo $BusinessCurrency; ?> <?php echo number_format($TotalBusinessCurrency, 2, '.', ''); ?>
                    <!--<br><span style="font-size:9px;">(<?php echo $AlternativeCurrency; ?> <?php echo number_format($TotalAlternativeCurrency, 2, '.', ''); ?>)</span>-->
                </td>
            </tr>
            <tr style="font-size:12px;text-align: left;">
                <td style="padding: 4px 4px 4px 10px;  text-align: right;" colspan="4">Delivery Charges 总销售量</td>
                <td style="padding: 4px 4px 4px 10px; " ><?php echo $BusinessCurrency; ?> <?php echo number_format($TotalBusinessCurrencyDeliveryCharges, 2, '.', ''); ?>
                    <!--<br><span style="font-size:9px;">(<?php echo $AlternativeCurrency; ?> <?php echo number_format($TotalAlternativeCurrencyDeliveryCharges, 2, '.', ''); ?>)</span>-->
                </td>
            </tr>
            
            <tr style="font-size:12px;text-align: left;">
                <td style="padding: 4px 4px 4px 10px; text-align: right;" colspan="4">Total Amount 总金额</td>
                <td style="padding: 4px 4px 4px 10px; " ><?php echo $BusinessCurrency; ?> <?php echo number_format($TotalBusinessCurrency + $TotalBusinessCurrencyDeliveryCharges, 2, '.', ''); ?>
                    <!--<br><span style="font-size:9px;">(<?php echo $AlternativeCurrency; ?> <?php echo number_format($TotalAlternativeCurrency + $TotalAlternativeCurrencyDeliveryCharges, 2, '.', ''); ?>)</span>-->
                </td>
            </tr>
        </table>
        <table style="width:100%; padding: 0px;margin-top:30px;font-size:12px" border="0" cellpadding="0" cellspacing="0">
            <tr><td>REMARKS:</td></tr>
            <tr><td></td></tr>
            <tr><td>备注:</td></tr>
            <tr><td style="text-align: left;">This is a computer generated document. No signature required. Goods sold are not refundable, returnable or exchangeable. </td></tr>
            <tr><td style="text-align: left;">这是一个电脑系统所发出的文件。无需签字。所售商品不予退款，不可退还与不可互换。</td></tr>
        </table>
    </body>
</html>