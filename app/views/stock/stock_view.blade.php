<?php if ($htmlview == true) { ?>

<style type="text/css">
    
    body{
        max-width: 900px;
        border: solid 1px #888080;
        padding: 10px;
        margin: auto;
        background-color: #fff;
        }
        @media print{
        
        #printpagebutton { 
            display:none; 
        }
        
        .address_container{
            font-size:11px
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
        .tb{
            float: right;
        }
        
        }

        .w3-badge,.w3-tag{
            background-color:#000;
            color:#fff;
            display:inline-block;
            padding-left:8px;
            padding-right:8px;
            font-weight:bold;
            text-align:center;

        }
        /*.tb{
            float: right;
        }*/

        .w3-badge{border-radius:50%}

        .w3-red,.w3-hover-red:hover{color:#fff!important;background-color:#f44336!important}

        .w3-black,.w3-hover-black:hover{color:#fff!important;background-color:#000!important}
        .w3-border-black,.w3-hover-border-black:hover{border-color:#000!important}

        .w3-table-all{border:1px solid #ccc}
</style>


<?php }else{?>
<style>
.tb{

    position: relative;
    float:right;

}
</style>
<?php }?>
@extends('stock.pdf_header')
@section('content')
<?php 

$file = (Config::get('constants.DO_PDF_FILE_PATH') . '/' . urlencode($display_details['st_no']) . '.pdf')."#".($display_details['id']);
$encrypted = Crypt::encrypt($file);
$encrypted = urlencode(base64_encode($encrypted));

?> 
    
    <?php if (!$multiPDF) { ?>
    <page_header>
    </page_header>
    <?php } ?>
    <?php if ($htmlview == false) { ?>
    <page_footer>
        <table class="page_footer">
            <tr>
                <td style="width: 100%; text-align: right">
                    Page [[page_cu]]/[[page_nb]] ({{$display_details['st_no']}})
                </td>
            </tr>
        </table>
    </page_footer>
    <?php } ?>
    <table style="width: 100%;">
    <?php 
    if ($display_details['qr_code']!='') {

    ?>
        <tr >
            <td><br>&nbsp;</td>
            <td>&nbsp;</td>
            <td>
                <?php if ($htmlview == false) { ?>
                    <img width="80px;" style="float: right;padding: 5px;" src="images/qrcode/{{$display_details['qr_code']}}" />
                <?php }else{ ?>
                 <img width="80px;" style="float: right;padding: 5px;" src="/images/qrcode/{{$display_details['qr_code']}}" />
                 <?php } ?>
            </td>                   
        </tr>
<?php } ?>
        

        <tr>
            <td style="width: 50%;">
              <?php if ($htmlview == false) { ?>
                            <img width="100px;" src="img/invoice_po_logo.jpg" /><br /><br />
                            <?php }else{ ?>
                            <img width="100px;" src="/img/invoice_po_logo.jpg" /><br /><br />
                            <?php } ?>
            </td>
            <td style="width: 20%;">&nbsp;</td>
            <td style="width: 30%;">
                <font size="3"><strong>STOCK TRANSFER FORM</strong></font>
            </td>
        </tr>
    
        <tr>
            <td style="width: 50%;" class="address_container" valign="middle">
                Jocom MShopping Sdn Bhd (1119648-X)<br />                 
                Unit 9-1, Level 9,<br />
                Tower 3, Avenue 3, Bangsar South,<br />
                No. 8, Jalan Kerinchi,<br />
                59200 Kuala Lumpur.<br />
                Tel: 03-2241 6637 Fax: 03-2242 3837<br />
                <br />
            </td>
            <td style="width: 20%;" class="address_container" >&nbsp;</td>
            <td style="width: 30%;" class="address_container"  valign="middle">
                ST No: {{$display_details['st_no']}}<br />
                ST Date: {{$display_details['st_date']}}<br />

                 <font size="4"><strong>Transfer Date:</strong></font>
              {{$display_details['transfer_date']}}<br />
            </td>
        </tr>
        
       

        <tr>
            <td style="width: 50%;" class="address_container" >
                <br />
                <font size="4"><strong>Deliver From:</strong></font><br/>

        
                {{$display_details['deliver_from']}}<br />
            </td>
            <td style="width: 20%;" class="address_container" >&nbsp;</td>
            <td style="width: 30%;" class="address_container"  valign="middle">
               <font size="4"><strong>Deliver To:</strong></font><br/>
                {{$display_details['deliver_to']}}<br />
            </td>
        </tr><br><br>

         <tr>
            <td style="width: 50%;" class="address_container" >
                <br />
                  <font size="4"><strong>Requested By:</strong></font><br/>
          
                {{$display_details['created_by']}}<br />
            </td>
            <td style="width: 20%;" class="address_container" >&nbsp;</td>
            <td style="width: 30%;" class="address_container"  valign="middle">
                &nbsp;
            </td>
        </tr>
    </table>
    
    <br /><br />
    
   <table border="1" style="width: 100%;">
        <thead>         
            <tr>
               
                <th style="width: 35%;">Description</th>
                 <th style="width: 21.666666667%;">SKU</th>
                   <th style="width: 21.666666667%;">Expired Date</th>
                   <th style="width: 21.666666667%;">Quantity Box</th>
             
            </tr>
        </thead>

      

            @foreach($display_stocks as $stuks)

             <tr>
                <td style="width: 35%;">{{$stuks->name}}</td>
                <td style="width: 21.666666667%;">
                    {{$stuks->sku}}
                </td>
                <td style="width: 21.666666667%; ">
                    {{$stuks->expired_date}}
                </td>
                <td style="width: 21.666666667%;" valign="center">
                    {{$stuks->qty}}
                </td>
              
            </tr>


@endforeach
       
        
    </table>  
    
    
    
    <br /><br />
    
    <strong>Purpose of Transferring:</strong><br/><br/>

      <u>{{$display_details['purposeoftransfer']}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br><br><br><br><br><br><br>
     <table style="width: 100%;">
              
            <tr>
                <td style="width:  33.333333333%;font-weight:bold"  valign="left"> Send By</td>
                 <td style="width:  33.333333333%;font-weight:bold"  valign="left"> Received By</td>
                   <td style="width:  33.333333333%;font-weight:bold"   valign="left"> Approved By</td>
             
            </tr>
      
      
            </table><br><br><br>
                 <table style="width: 100%;">
                    <tr>
            <td style="width: 33.333333333%;" valign="middle">
                <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />
               
                Name:<br />
               
                Date:<br />
               
            </td>
            <td style="width: 33.333333333%;" valign="middle"> 
             <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />
              
                Name:<br />
               
                Date:<br />
                
            </td>
            <td style="width: 33.333333333%;" valign="middle">
                <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br />
              
                Name:<br />
                Date:<br />
               
            </td>
        </tr>
    </table>
   
<!--    <br /><br />
    <strong>Term and Condition:</strong>
    <ul>
        <li style="font-size:10px;">Please produce IC for signatory of buyer or if it is another person sign on behalf please fill in their IC and phone number.</li>
        <li style="font-size:10px;">JOCOM have the rights to not give this item to the buyer if it is not the actual buyer based from IC except called and allowing second person to collect on behalf.</li>
        <li style="font-size:10px;">Photos are to be taken on the products delivered to the buyer for proof of delivery and also items are no broken..</li>
    </ul>-->
    
    

@stop
<a href="<?php echo '/stock/download'.$encrypted?>"><button id="download">Download</button></a>
<input id="printpagebutton" type="button" value="Print this page" onclick="window.print()"/>
<script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
<script>

   $(document).ready(function() { 


        $("#printpagebutton").click(function(){
     // alert('In');
            var idPrint = parseFloat($('#idPrintcount').val())+1;
            var stockID = $('#idstock').val();
            $('#LabelidPrintcount').addClass('w3-tag w3-red w3-border-black w3-table-all');
            $('#LabelidPrintcount').html(idPrint);
            $('#idPrintcount').val(idPrint);

            // alert(idPrint);
            $.ajax({
                method: "GET",
                url: "/stock/updatedoprinting",
                dataType:'json',
                data: {
                    'stockID':stkID,
                    'printcount':idPrint
                },
                beforeSend: function(){
                    
                },
                success: function(data) {
                    // alert(data);
                    if(data ==1){
                         window.print();
                    }
                }
            });

        
           

        });


   });

</script>