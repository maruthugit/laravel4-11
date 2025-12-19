<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
@page { margin: 0px; }
    body{
        max-width: 800px;
        /*border: solid 1px #888080;*/
        /*padding: 20px;*/
        margin: auto;
        background-color: #fff;
        margin-bottom: 10px;
        margin-top: 10px;
        font-size:12px;
        font-family: "Helvetica", Georgia, Serif;
         margin: 20px;
        }
</style>

    <body>
        <table style="width:100%;">
            <tr>
                <td style="text-align:right;font-size: 18px;width: 45%;text-align: left;font-weight:light;" rowspan="2" valign="top">
                    <strong>Shipper :</strong></span>
                    <br>Tien Ming Distribution Sdn Bhd
                    <br>10, Jalan Str 1, Saujana Teknologi Park, 
                    <br>Rawang,
                    <br>59200 Kuala Lumpur, Malaysia.
                </td>
                <td style="text-align:right;font-size: 20px;width: 55%; text-align: right;font-weight: bold;" valign="top"><?php echo $data['barcode']; ?></td>
            </tr>
            <tr>
                <td style="text-align:right;font-size: 25px;width: 50%; text-align: center;font-weight: bold;" valign="top"><?php echo strtoupper($data['data_header']->reference_number); ?></td>
            </tr>
          
        </table>
        <table style="width:100%; border-top: solid 3px #000;padding-top:10px;margin-top:10px;"> 
            <tr>
                <td style="text-align:left;font-size: 20px;font-weight:bolder;">Deliver To : <?php echo strtoupper($data['data_header']->delivery_name); ?></td>
            </tr>
            <tr>
                <td style="text-align:left;font-size: 20px;font-weight:bolder;width:70%">
                   <?php echo $data['data_header']->FullAddress; ?>
                   <br>ID NUMBER : <?php echo $data['data_header']->recipient_id; ?>
                </td>
                <td style="text-align:right;font-size: 35px;padding-right: 20px;font-weight:bolder;">CHN</td>
            </tr>
            <tr>
               
                <td style="text-align:right;font-size: 25px;font-weight:bolder;" colspan="2">Tel :  <?php echo $data['data_header']->delivery_contact_no; ?></td>
            </tr>
        </table>
        
        <table style="width:100%; border-top: solid 3px #000;padding-top:0px;margin-top:10px;"> 
            <tr>
                <td style="text-align:left;font-size: 20px;font-weight: bold;letter-spacing: 9px;">CUSTOMS DECLARATION</td>
            </tr>
            <tr>
                <td style="text-align:left;font-size: 20px;font-weight: bold;">Postal administration (May be opened officially)</td>
                <td style="text-align:left;font-size: 20px;"></td>
            </tr>
        </table>
        <table style="width:100%; border-top: solid 3px #000;padding-top:10px;margin-top:0px;"> 
            <tr>
                <td style="text-align:left;font-size: 20px;font-weight: bold;" valign="top">
                   <div>
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAB+SURBVGhD7dkxDoAgEERRWg9j5f2vZDyButuZKcmAG/JfMo0N/FYaAKCiLXYUWd6l2x57iizv0m3JkDt2Tl6eaQ+58sNkeSYhX4SYEKIIMSFEEWJCiCLEhBBFiAkhihATQhQhJoQoQkwIUYSYDAlZ5if23yMkLfP0BgAYoLUXo7aFPAK8De8AAAAASUVORK5CYII=" style="vertical-align: middle;width: 30px;"/>
                        <span style="vertical-align: middle;">Gift</span>
                    </div>
                </td>
                <td style="text-align:left;font-size: 20px;font-weight: bold;" valign="top">
                   <div>
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAB+SURBVGhD7dkxDoAgEERRWg9j5f2vZDyButuZKcmAG/JfMo0N/FYaAKCiLXYUWd6l2x57iizv0m3JkDt2Tl6eaQ+58sNkeSYhX4SYEKIIMSFEEWJCiCLEhBBFiAkhihATQhQhJoQoQkwIUYSYDAlZ5if23yMkLfP0BgAYoLUXo7aFPAK8De8AAAAASUVORK5CYII=" style="vertical-align: middle;width: 30px;"/>
                        <span style="vertical-align: middle;">Commercial Sample</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="text-align:left;font-size: 20px;font-weight: bold;" valign="top">
                   <div>
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAB+SURBVGhD7dkxDoAgEERRWg9j5f2vZDyButuZKcmAG/JfMo0N/FYaAKCiLXYUWd6l2x57iizv0m3JkDt2Tl6eaQ+58sNkeSYhX4SYEKIIMSFEEWJCiCLEhBBFiAkhihATQhQhJoQoQkwIUYSYDAlZ5if23yMkLfP0BgAYoLUXo7aFPAK8De8AAAAASUVORK5CYII=" style="vertical-align: middle;width: 30px;"/>
                        <span style="vertical-align: middle;">Documents</span>
                    </div>
                </td>
                <td style="text-align:left;font-size: 20px;font-weight: bold;" valign="top">
                   <div>
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAEcSURBVGhD7dlNSsNQGIXhKE6cCLoEB53Vga5I9+WK1JVIixvw57y2gXBt0Nwbv3sbzguHpi00eSYptJ1zzrkWO9fuGhnXkt1a+2xkXEt2i4R8aNvgcc7ZIW+8EBznNGSYITNlSJohEzrVrneHPzoaCIhHbaPd8kLSUUB6RP/5fAHeaMOah6QI9qJdasOahvwVQc1CpiAoFHK2f/ytqQgKg1xpz9r997PxchAUAgHBxfDeuzaGyUVQCIRbJff//v1DmBIEhUCIL7ExzIlWgqAwCB3CPGilCAqFUIpJl4OgcAiNYXIRVAVCKaYEQdUgBOZVe9JKEFQVQivtYndYVHXIXBmSZshMGZI2hCzmR+zaM4QW89ebc865f6jrvgCzudkjAOa2SgAAAABJRU5ErkJggg==" style="vertical-align: middle;width: 30px;"/>
                        <span style="vertical-align: middle;">Others</span>
                    </div>
                </td>
            </tr>
        </table>
        <table style="width:100%; border-top: solid 3px #000;margin-top:10px;min-height: 150px;" cellspacing='0' border='0' > 
            <tr>
                <td style="font-weight: bold; border-right: solid 1px #000;padding-top:10px;font-size:25px;">Product Description of Contents</td>
                <td style="text-align: right;font-weight: bold;padding-top:10px;font-size:25px;">Value (RMB)</td>
            </tr>
            <?php
            
            $totalAmount = 0;
         
            foreach($data['data_items'] as $key => $value) { 
                $totalAmount = $totalAmount + $value->value;
            ?>
            <tr>
                <td style="border-right: solid 1px #000;font-size:20px;"><?php echo $value->product_name." ".$value->brand." ".$value->product_label; ?></td>
                <td style="text-align: right;font-size:20px;"><?php echo $value->value; ?></td>
            </tr>
            <?php } ?>
            
                <?php 
                if($totalLoopEmpty > 0 ) {
                    for($x= 0 ; $x<$totalLoopEmpty ; $x++){
                ?>
                <tr style="min-height:100px;">
                    <td style="border-right: solid 1px #000;font-size:15px;">&#160;</td>
                    <td style="text-align: right;font-size:22px;">&#160;</td>
                </tr>
                <? } } ?>
            <tr style="">
                <td style="text-align: left;font-weight: bold;border-top: solid 1px #000;padding-top:10px;font-size:25px;">Total Weight (KG): <?php echo $data['data_header']->weight; ?></td>
                <td style="text-align: right;font-weight: bold;border-top: solid 1px #000;padding-top:10px;font-size:25px;"><?php echo number_format((float)$totalAmount, 2, '.', '');?></td>
            </tr>
        </table>
        <table style="width:100%; border-top: solid 3px #000;padding-top:10px;margin-top:10px;"> 
            <tr>
                <td style="text-align:left;font-size: 18px;font-weight: bold">
                I, the undersigned, whose name and address are given on the item, certify that the particulars given in this declaration are correct and that this 
                item does not contain dangerous article or articles prohibited by legislation or by postal or customs regulations.
                </td>
            </tr>
        </table>
        <table style="width:100%; border-top: solid 3px #000;padding-top:10px;margin-top:10px;"> 
            <tr>
                <td style="text-align:left;font-size: 20px;font-weight: bold">Date : <?php echo date("d/m/Y"); ?></td>
                <td style="text-align:left;font-size: 20px;font-weight: bold">Signature : </td>
            </tr>
        </table>
        <table style="width:100%; border-top: solid 3px #000;padding-top:10px;margin-top:10px;"> 
            <tr>
                <td style="text-align:left;font-size: 20px;font-weight: bold">Contact : enquiries@tmgrocer.com</td>
                <td style="text-align:left;font-size: 20px;font-weight: bold">+603 6734 8744</td>
            </tr>
        </table>
    </body>
</html>