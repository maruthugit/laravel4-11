<!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Logistic Dashboard : Overall Statistics </title>
{{ HTML::style('css/bootstrap.min.css') }}
<!-- Scripts are placed here -->
{{ HTML::script('js/jquery.js') }}
{{ HTML::script('js/bootstrap.min.js') }}

<!-- Latest compiled and minified JavaScript -->
<!-- <script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script> -->
<!-- <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'> -->




<style type="text/css">
/*html, body {    width: 100%;    height: 100%;}*/
/*Bootstrap-overlay*/


@media screen and (max-width: 767px) {
  .alt-table-responsive {
    width: 90%;
    margin-bottom: 15px;
    overflow-y: hidden;
    overflow-x: auto;
    -ms-overflow-style: -ms-autohiding-scrollbar;
    border: 1px solid #434a54;
    -webkit-overflow-scrolling: touch;
    padding: 26px;
  }
  .table {

    width:100%!important;
    background-color: #000;
    position: static;

      }



 .gray td[class*=col-], .gray th[class*=col-] {
    position: static;
    display: table-cell;
    background-color: #000;
 }

 

.quick-actions_homepage .quick-actions li{ min-width:100%; width: 100%;}
/*#user-nav{ margin:0px auto; top:-31px; border-top:none;  left:-3%; text-align:center; float:none;}*/
#user-nav > ul > li:last-child{ border-right:1px solid #363E48}
#user-nav > ul > li{ border-top:1px solid #363E48}
#sidebar{ top:-30px;}
#content{ margin-top:-30px;}
#sidebar{ clear:both;}
}
@media (min-width: 768px) and (max-width: 970px) {.quick-actions_homepage .quick-actions li{ min-width:20.5%; }}
@media (min-width: 481px) and (max-width: 767px) {
    .quick-actions_homepage .quick-actions li{ min-width:47%;}
}
.quick-actions1 li{ min-width:100%; width: 100%;}
.quick-actions1 li{ min-width:47%;}
/* Stat boxes and quick actions */
.quick-actions_homepage {
    width:100%;
    text-align:center; position:relative;
    float:left;
    margin-top:10px;
}

.quick-actions_homepage1 {
    width:100%;
    text-align:center; position:relative;
    float:left;
    margin-top:-41px;

}
.quick-actions, .quick-actions-horizontal{
    display: block;
    list-style: none outside none;
    /*margin: 15px 0;*/
    text-align: center;
    font-weight: bold;
    border-top-left-radius: 3px;
    border-top-right-radius: 3px;
    
}
.quick-actions1{
    display: block;
    list-style: none outside none;
    /*margin: 1px 0;*/
    text-align: center;
    font-weight: bold;
    border-top-left-radius: 0px;
    border-top-right-radius: 0px;
    
}

.quick-actions_homepage .quick-actions li{ position:relative;}
.quick-actions_homepage .quick-actions li .label{ position:absolute; padding:5px; top:-10px; right:-5px;}
.stats-plain {
    width: 100%;
}
.stat-boxes li, .quick-actions li, .quick-actions-horizontal li {
    float: left;
    line-height: 18px;
    margin: 0 1px 10px 0px;
    /*padding: 0 10px;*/
    width: 16%;
}
.quick-actions1 li{
    float: left;

    /*background: #FF0;*/

    /*margin: 0 10px 0px 0px;*/
    /*padding: 0 10px;*/
    width: 12%;
    margin-right: -15px;
}
.stat-boxes li a:hover, .quick-actions li a:hover, .quick-actions-horizontal li a:hover, .stat-boxes li:hover, .quick-actions li:hover, .quick-actions-horizontal li:hover {
    background: #2E363F;
}
.stat-boxes1 li a:hover, .quick-actions1 li a:hover, .quick-actions-horizontal1 li a:hover, .stat-boxes1 li:hover, .quick-actions1 li:hover, .quick-actions-horizontal1 li:hover {
    background: #000;
}
.quick-actions li {
    min-width:19.3%;
    min-height:70px;
}

.quick-actions1 li {
    min-width:7.3%;
    min-height:60px;
    float: right;
}

.quick-actions_homepage .quick-actions .span3{ width:100%;}
.quick-actions li, .quick-actions-horizontal li {
    padding: 0;
}
.stats-plain li {
    padding: 0 30px;
    display: inline-block;
    margin: 0 10px 20px;
}
.quick-actions li a {
    padding:10px 30px; 
}
.stats-plain li h4 {
    font-size: 40px;
    margin-bottom: 15px;
}
.stats-plain li span {
    font-size: 14px;
    color: #fff;
}
.quick-actions-horizontal li a span {
    padding: 10px 12px 10px 10px;
    display: inline-block;
}
.quick-actions li a, .quick-actions-horizontal li a {
    display: block;
    color: #fff; font-size:14px;
    font-weight:lighter;
}
.quick-actions1 li a, .quick-actions-horizontal1 li a {
    display: block;
    color: #fff; ;
    font-weight:lighter;
}

.quick-actions li a i[class^="icon-"], .quick-actions li a i[class*=" icon-"] {
    font-size:30px;
    display: block; 
    margin: 0 auto 5px;
    width: 100%;
}



.quick-actions-horizontal li a i[class^="icon-"], .quick-actions-horizontal li a i[class*=" icon-"] {
    background-repeat: no-repeat;
    background-attachment: scroll;
    background-position: center;
    background-color: transparent;
    width: 16px;
    height: 16px;
    display: inline-block;
    margin: -2px 0 0 !important;
    border-right: 1px solid #dddddd;
    margin-right: 10px;
    padding: 10px;
    vertical-align: middle;

}




.quick-actions li:active, .quick-actions-horizontal li:active {
    background-image: -webkit-gradient(linear, 0 0%, 0 100%, from(#EEEEEE), to(#F4F4F4));
    background-image: -webkit-linear-gradient(top, #EEEEEE 0%, #F4F4F4 100%);
    background-image: -moz-linear-gradient(top, #EEEEEE 0%, #F4F4F4 100%);
    background-image: -ms-linear-gradient(top, #EEEEEE 0%, #F4F4F4 100%);
    background-image: -o-linear-gradient(top, #EEEEEE 0%, #F4F4F4 100%);
    background-image: linear-gradient(top, #EEEEEE 0%, #F4F4F4 100%);
    box-shadow: 0 1px 4px 0 rgba(0,0,0,0.2) inset, 0 1px 0 rgba(255,255,255,0.4);

}
/*Metro Background color class*/
.bg_lb{ background:#434a54;}
.bg_lg{ background:#434a54;}
.bg_ly{ background:#434a54;}
.bg_ls{ background:#434a54;}
.bg_lo{ background:#FFF;}

.icon-pending {background-image: url('images/icon-pending.png');}

.panelheading {
  padding: 15px 15px;
  border-bottom: 1px solid transparent;
  border-top-left-radius: 3px;
  border-top-right-radius: 3px;
  color: #FFF;
  min-height: 60px;
}

.listwidth {

    width: 20%;
}

.listheight {
    height: 70px;
}

.circle-icon {
    background: #ffc0c0;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    text-align: center;
    line-height: 60px;
    /*vertical-align: middle;*/
    /*padding: 20px;*/
    position: absolute;
     color:#000;
     vertical-align:middle;

     margin-right: auto;

   /*float: none;*/

/*margin-left: 14.2%;*/
}

element {

-webkit-font-smoothing: antialiased;
-moz-osx-font-smoothing: grayscale;
}



.col-centered{
    float: none;
    margin: 0 auto;
    /*margin-right: 0px;*/
}

.font-weight-bold{
    font-weight: bold;
    font-size: 26pt;

}

.font-weight-bold1{
    font-weight: bold;
    font-size: 14pt;

}
.font-weight-bold2{
    font-weight: bold;
    font-size: 16pt;

}

.font-weight{
  font-size: 20pt;
}

.icon-box{
        width: 250px;
        
    }

.vcenter {
    /*display: inline-block;*/
    vertical-align: middle;
    float: none;
}


 </style>
<style type="text/css">
        /*h2,h3,h4,h5{
    font-family: 'Exo 2', sans-serif;
    font-weight: bold;
}*/
/*.box{
    border: solid 1px #FFF;
    outline: solid 1px #e0e0e0;
}*/

.box {
    width: 1450px;
    padding: 20px;
    background: #f2f2f2;
    box-sizing: border-box;
} 

p,b{
    float: left;
    display: block;
    font-family: 'Lato', sans-serif;
}

.carousel{
    /*margin-top: 30px;*/
    position: relative;
 /*   padding-bottom: 50px;
    background-color: #D8D8D8;*/
}

.item{
    /*min-height: 325px;*/
    padding-left: 30px;
    padding-right: 30px;
}

.item h2{
    width: 80%;
    color: #FFF;
    padding: 15px;
    font-size: 38px;
    margin-bottom: 30px;
    text-align: center;
    text-shadow: 1px 2px 3px #444;
}

.item .block{
    width: 100%;
    float: left;
    color: #FFF;
    min-height: 200px;
    text-align: center;
    padding: 15px;
    margin-bottom: 20px;
    box-shadow: 1px 2px 3px #444;
}


.item h4{
    width: 100%;
    padding: 15px;
    font-size: 30px;
    font-weight: 900;
    text-align: center;
    text-shadow: 2px 2px 2px #555;
}


.item p{
    text-align: center;
    font-size: 18px;
}

.item .input-group{
    margin-bottom: 10px;
}

.block-1{
    background-color: #763EC9;
}

.block-2{
    background-color: #1D3EC9;
}

.block-3{
    background-color: #3498DB;
}

.block-4{   
    background-color: #FF4583;
}

.carousel-indicators {
    bottom: 0px;
}

td .outer {
  position: relative;
  height: 30px;
}
td .inner {
  overflow: hidden;
  white-space: nowrap;
  position: absolute;
  width: 100%;
}

.mySlides {
  display:none;
  font-size: 14pt;
}

.titlepanel {
    font-size: 17pt;
    font-weight: bold;
    color: #F9C134;
    text-transform: uppercase;
    /*background: #8C000C;*/
    position: absolute;
}

.absolute {
    position: absolute;
    top: 10px;
    left:27%;
    width: 250pt;
    height: 100px;
    color: #F9C134;
    text-transform: uppercase;
    font-size: 20pt;
}

.tablewidth {
  width: 100%;

}

    </style>

   <!--  <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script> -->
{{ HTML::style('font-awesome/css/font-awesome.min.css') }}
 <!-- <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script> -->


</head>
<body>
<div id="page-wrapper">
 <div class="container-fluid">
             <div class="panel panel-default">
                <div class="panelheading bg_lb"><span class="font-weight-bold1" style="width:50%; float:left;">Logistic Dashboard : Overall Statistics
                  &nbsp; &nbsp; &nbsp;<!-- <span id='lblRegion' class="titlepanel">Malaysia - HQ</span> -->
                  </span>
                      <span class="font-weight-bold1" style="vertical-align: top; text-align:right;">Overall Batch Pending 
                        <span class="quick-actions_homepage1" style=" padding:0px; vertical-align: top;">
                          <ul class="quick-actions1">
                              <li class="block-4"><a><span id="total-batchpending3" class="font-weight-bold2"></span><br><span class="font-weight-bold2">Over 3 Days</span></a></li>
                              <li class="block-2"><a><span id="total-batchpending2" class="font-weight-bold2"></span><br><span class="font-weight-bold2">2 Days</span></a></li>
                              <li class="block-1"><a><span id="total-batchpending1" class="font-weight-bold2"></span><br><span class="font-weight-bold2">1 Day</span></a> </li>
                              
                              
                          </ul>
                        </span>
                      </span>
                </div>
               
                <div class="quick-actions_homepage" style="width:100%;padding:0px;">
                  <ul class="quick-actions"  style="width:103.5%; padding:0;">
                    <li class="bg_lb"><a><span id="total-pending" class="font-weight-bold"></span><br><img src="../images/icon-pending.png"><br><span class="font-weight-bold1">TOTAL PENDING</span></a> </li>
                    <li class="bg_lg"><a><span id="total-sending" class="font-weight-bold"></span><br><img src="../images/icon-sending.png"><br><span class="font-weight-bold1">TOTAL SENDING</span></a></li>
                    <li class="bg_ly"><a><span id="total-outfordelivery" class="font-weight-bold"></span><br><img src="../images/icon-for-delivery.png"><br><span class="font-weight-bold1">OUT FOR DELIVERY</span></a></li>
                    <li class="bg_lb"><a><span id="total-sent" class="font-weight-bold"></span><br><img src="../images/icon-shipment-complete.png"><br><span class="font-weight-bold1">SHIPMENT COMPLETE</span></a></li>
                    <li class="bg_ls"><a><span id="total-undelivered" class="font-weight-bold"></span><br><img src="../images/icon-undelivered.png"><br><span class="font-weight-bold1">UNDELIVERED</span></a></li>
                
                  </ul>
                </div>
                 
             </div>
               <div class="panel panel-default" tyle="width:100%;">
                  <table class="able bg_lb table-bordered panelheading" style="width:100%; color:#FFF;">
                          <tbody class="mySlides" style="width:100%;" id='block1'>
                              <tr id="headingrow" style="align:center; width:100%;" >
                              </tr>
                              <tr id="contentrow" style="background:#FFFFFF; width:100%;">
                              
                              </tr>
                              <!-- <tr id="contentpendingrow1" style="background:#FFFFFF; width:100%;">

                              </tr> -->

                            </tbody>
                            
                            <tbody class="mySlides" style="width:100%;" id='block2'>
                              <tr id="headingrow1" style="align:center; width:100%;" width="100%">
                              </tr>
                              <tr id="contentrow1" style="background:#FFFFFF; width:100%;" width="100%">
                              </tr>  
                            </tbody>

                            <tbody class="mySlides" style="width:100%;" id='block3'>
                              <tr id="headingrow2" style="align:center; width:100%;" width="100%">
                              </tr>
                              <tr id="contentrow2" style="background:#FFFFFF; width:100%;" width="100%">
                              </tr>  
                             </tbody>
                             
                             <!-- Unassigned Transactions Start -->
                             <tbody class="mySlides" style="width:100%;" id='block4'>
                              <tr id="headingrow3" style="align:center; background:#FF0000; width:100%;">
                              </tr>
                              <tr id="contentrow3" style="background:#FFFFFF; width:100%;">
                              </tr>  
                             </tbody>

                             <tbody class="mySlides" style="width:100%;" id='block5'>
                              <tr id="headingrow4" style="align:center; background:#FF0000; width:100%;">
                              </tr>
                              <tr id="contentrow4" style="background:#FFFFFF; width:100%;">
                              </tr>  
                             </tbody>

                             <tbody class="mySlides" style="width:100%;" id='block6'>
                              <tr id="headingrow5" style="align:center; background:#FF0000; width:100%;">
                              </tr>
                              <tr id="contentrow5" style="background:#FFFFFF; width:100%;">
                              </tr>  
                             </tbody>
                             <!-- Unassigned Transactions End -->


                              <!-- <tbody class="panel-default panelheading bg_lb" style="width:100%; height:40px;">

                                <tr>
                                    <td>
                                        <table>
                                            <tr>
                                              <td>PENDING</td>
                                              <td>SENT</td>
                                              <td>RETURNED/CANCELLED</td>
                                            </tr>

                                        </table>  
                                    </td>
                                </tr>
                              </tbody> -->


                  </table>
                  <div class="panelheading bg_lo vcenter" style="color:#000;"><span class="font-weight-bold1" >
                  <i class="fa fa-square fa-2x" style="color:#8A2BE2; vertical-align:middle;" aria-hidden="true"></i>  PENDING &nbsp;&nbsp;&nbsp;&nbsp;
                  <i class="fa fa-square fa-2x" style="color:#000; vertical-align:middle;" aria-hidden="true"></i>  SENDING &nbsp;&nbsp;&nbsp;&nbsp;
                  <i class="fa fa-square fa-2x" aria-hidden="true" style="color:#458224; vertical-align:middle;"></i> SENT  &nbsp;&nbsp;&nbsp;&nbsp; 
                  <i class="fa fa-square fa-2x" style="color:#ED2536; vertical-align:middle;" aria-hidden="true"></i> RETURNED/CANCELLED      

                  </span>
                  </div>
                </div>

                 
                
            
 </div>

</div>

</body>
<script>
$(document).ready(function() {


    getStatistic();

    function getStatistic(){
        // alert('ok');
        var headinglist = '';
        var headinglist1 = '';
        var headinglist2 = '';
        var itneary = 0;
       
        
        
        $("#headingrow").html("");
        $("#headingrow1").html("");
        $("#headingrow2").html("");
        $("#headingrow3").html("");
        $("#headingrow4").html("");
        $("#headingrow5").html("");
        $("#contentrow").html("");
        $("#contentrow1").html("");
        $("#contentrow2").html("");
        $("#contentrow3").html("");
        $("#contentrow4").html("");
        $("#contentrow5").html("");

        $.ajax({
            method: "POST",
            url: "/jlogistic/dashboardstatisticregion",
            dataType:'json',
            data: {

            },
            beforeSend: function(){
                
            },
            success: function(data) {
                 $("#total-pending").html(data.data.TransactionLogistic.TotalPending);
                 $("#total-sending").html(data.data.TransactionLogistic.TotalSending);
                 $("#total-outfordelivery").html(data.data.TransactionLogistic.TotalBatchPending);
                 $("#total-sent").html(data.data.TransactionLogistic.TotalBatchSent);
                 $("#total-undelivered").html(data.data.TransactionLogistic.TotalUndelivered);

                 $("#total-batchpending1").html(data.data.TransactionLogistic.TotalBatchPending1day);
                 $("#total-batchpending2").html(data.data.TransactionLogistic.TotalBatchPending2day);
                 $("#total-batchpending3").html(data.data.TransactionLogistic.TotalBatchPending3day);
                 
                 var blockcount = data.data.TransactionLogistic.DriverDetails.length; 
                 gcount = blockcount;
                 // alert(data.data.TransactionLogistic.DriverDetails.length);
                 if(blockcount<=3){
                   $('#block2').removeClass('mySlides');
                   $('#block3').removeClass('mySlides');
                   $('#block2').find('tbody').hide();
                   $('#block3').find('tbody').hide();
                 }
                 else if(blockcount==6){
                  $('#block3').removeClass('mySlides');
                  $('#block3').find('tbody').hide();
                 }
                 

                 $.each(data.data.TransactionLogistic.DriverDetails, function (index, value) {
                    // alert(data.data.TransactionLogistic.DriverDetails.length);

                    itneary=itneary+1;
                    if(itneary<=3){
                    headinglist = headinglist + "<th class='text-uppercase text-center' align='center'><span class='absolute'>"+ (itneary==1?value.regionname:'')+"</span><table width='100%' border=0><tr style='background:"+value.colorlight+"'><th align='center' class='text-uppercase text-center listheight col-centered' width='33%' style='align:center;'><span class='font-w eight'>TEAM "+value.drivername+"</span><br><span class='circle-icon' aria-hidden='true' style='background:"+value.colordark+"; color:#fff;'><span class='font-weight'>"+value.teamseque+"</span></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th></tr>";
                   headinglist = headinglist +  "<tr><th align='center' valign='bottom' class='text-uppercase text-center listheight'>TRANSACTION ID</th></th></table></th>";              
                    }
                    else if(itneary>=4 && itneary<=6){
                      headinglist1 = headinglist1 + "<th class='text-uppercase text-center' align='center'><span class='absolute'>"+ (itneary==4?value.regionname:'')+"</span><table width='100%' border=0><tr style='background:"+value.colorlight+"'><th align='center' class='text-uppercase text-center listheight col-centered' width='33%' style='align:center;'><span class='font-weight'>TEAM "+value.drivername+"</span><br><span class='circle-icon' aria-hidden='true' style='background:"+value.colordark+"; color:#fff;'><span class='font-weight'>"+value.teamseque+"</span></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th></tr>";
                   headinglist1 = headinglist1 +  "<tr><th align='center' valign='bottom' class='text-uppercase text-center listheight'>TRANSACTION ID</th></th></table></th>";   
                    }
                    else if(itneary>=7){
                      headinglist2 = headinglist2 + "<th class='text-uppercase text-center' align='center'><span class='absolute'>"+ (itneary==7?value.regionname:'')+"</span><table width='100%' border=0><tr style='background:"+value.colorlight+"'><th align='center' class='text-uppercase text-center listheight col-centered' width='33%' style='align:center;'><span class='font-weight'>TEAM "+value.drivername+"</span><br><span class='circle-icon' aria-hidden='true' style='background:"+value.colordark+"; color:#fff;'><span class='font-weight'>"+value.teamseque+"</span></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th></tr>";
                   headinglist2 = headinglist2 +  "<tr><th align='center' valign='bottom' class='text-uppercase text-center listheight'>TRANSACTION ID</th></th></table></th>";   
                    }
                 });
                 $("#headingrow").html(headinglist);  
                 $("#headingrow1").html(headinglist1);  
                 $("#headingrow2").html(headinglist2);  


                  // alert(data.data.TransactionLogistic.BatchDetails.length);
                  tneary1=0;
                  
                var contentlist='';
                var contentlist1='';
                var contentlist2='';
                
                var rowlist1 = 0;

                 $.each(data.data.TransactionLogistic.DriverDetails, function (index1, value1) {
                          
                            
                            var rowlist = 0;
                            var rowbreak = 0;
                            tneary1=tneary1+1;
                           
                            console.log(value1.driverid);

                            if(tneary1<=3){
                                    contentlist = contentlist + "<td valign='top' class='text-uppercase text-center'  style='border:1px solid #000;' height='600px;' width='550px' align='center'><table width='100%' border=0><tr><td valign='top' align='center' class='text-uppercase text-center' style='align:center;'>";

                                    $.each(data.data.TransactionLogistic.BatchDetails, function(indexBat, valbat){

                                        // console.log(value1.driverid+'inner');

                                        if(value1.driverid == valbat.driver_id)
                                        {
                                          rowbreak = 1;
                                          rowlist=rowlist+1;

                                            contentlist = contentlist + "<span style='color:"+valbat.transcolor+"; font-size:20pt;'><strong>"+valbat.transactionid+"</strong></span> <br>";
                                            // contentlist = contentlist + "<span style='color:#FF0015'>*</span> <br>";

                                            if(rowlist==16){
                                                 rowlist = 0;
                                                 contentlist = contentlist + "</td><td valign='top' align='center' class='text-uppercase text-center' style='align:center; border-left:1px dashed #333'>";
                                            }


                                        }

                                    });

                                    if(rowbreak ==0){

                                       contentlist = contentlist + "<br><span style='color:#000;'>No Records Found</span>";

                                    }
                                     contentlist = contentlist + "</td></tr></table></td>"; 
                                     $("#contentrow").html(contentlist);  

                            }

                            else if(tneary1>=4 && tneary1<=6){
                                    contentlist1 = contentlist1 + "<td valign='top' class='text-uppercase text-center'  style='border:1px solid #000;' height='600px;' width='550px' align='center'><table width='100%' border=0><tr><td valign='top' align='center' class='text-uppercase text-center' style='align:center;'>";

                                    $.each(data.data.TransactionLogistic.BatchDetails, function(indexBat, valbat){

                                        // console.log(value1.driverid+'inner');

                                        if(value1.driverid == valbat.driver_id)
                                        {
                                          rowbreak = 1;
                                          rowlist=rowlist+1;

                                            contentlist1 = contentlist1 + "<span style='color:"+valbat.transcolor+"; font-size:20pt;'><strong>"+valbat.transactionid+"</strong></span> <br>";

                                            if(rowlist==16){
                                                 rowlist = 0;
                                                 contentlist1 = contentlist1 + "</td><td valign='top' align='center' class='text-uppercase text-center' style='align:center; border-left:1px dashed #333'>";
                                            }


                                        }

                                    });

                                    if(rowbreak ==0){

                                       contentlist1 = contentlist1 + "<br><span style='color:#000;'>No Records Found</span>";

                                    }
                                     contentlist1 = contentlist1 + "</td></tr></table></td>"; 

                                     $("#contentrow1").html(contentlist1);  

                            }
                            else if(tneary1>=7){
                                    contentlist2 = contentlist2 + "<td valign='top' class='text-uppercase text-center'  style='border:1px solid #000;' height='600px;' width='550px' align='center'><table width='100%' border=0><tr><td valign='top' align='center' class='text-uppercase text-center' style='align:center;'>";

                                    $.each(data.data.TransactionLogistic.BatchDetails, function(indexBat, valbat){

                                        // console.log(value1.driverid+'inner');

                                        if(value1.driverid == valbat.driver_id)
                                        {
                                          rowbreak = 1;
                                          rowlist=rowlist+1;

                                            contentlist2 = contentlist2 + "<span style='color:"+valbat.transcolor+"; font-size:20pt;'><strong>"+valbat.transactionid+"</strong></span> <br>";

                                            if(rowlist==16){
                                                 rowlist = 0;
                                                 contentlist2 = contentlist2 + "</td><td valign='top' align='center' class='text-uppercase text-center' style='align:center; border-left:1px dashed #333'>";
                                            }


                                        }

                                    });

                                    if(rowbreak ==0){

                                       contentlist2 = contentlist2 + "<br><span style='color:#000;'>No Records Found</span>";

                                    }
                                     contentlist2 = contentlist2 + "</td></tr></table></td>"; 

                                     $("#contentrow2").html(contentlist2);  

                            }


                           


                            var drivID = "";
                            
                            drivID = value1.driverid; 


                            // alert(drivID);
                            $.ajax({
                            method: "POST",
                            url: "/jlogistic/dashboarddriverbatch",
                            dataType:'json',
                            data: {
                                'driverid': drivID 
                            },
                            beforeSend: function(){
                                
                            },
                            success: function(datasub) {
                                    // console.log(datasub);
                                    // console.log(value1.driverid+'Main');

                                   

                                       
                                    tneary1=tneary1+1;
                                    if(tneary1<=3){
                                    contentlist = contentlist + "<td valign='top' class='text-uppercase text-center'  style='border:1px solid #000;' height='600px;' width='550px' align='center'><table width='100%' border=0><tr><td valign='top' align='center' class='text-uppercase text-center' style='align:center;'>";
                                    $.each(datasub.driverdata.TransactionLogisticdriver.BatchDetailsNew, function(index2, val){

                                          // console.log(val.transactionid);
                            
                                           rowlist=rowlist+1;

                                            contentlist = contentlist + "<span style='color:"+val.transcolor+"; font-size:20pt;'><strong>"+val.transactionid+"</strong></span> <br>";

                                            if(rowlist==25){
                                                 rowlist = 0;
                                                 contentlist = contentlist + "</td><td valign='top' align='center' class='text-uppercase text-center' style='align:center; border-left:1px dashed #333'>";
                                            }
                                         

                                    });

                                      if(datasub.driverdata.TransactionLogisticdriver.BatchDetailsNew.length == 0){

                                         contentlist = contentlist + "<br><span style='color:#000;'>No Records Found</span>";
                                      }
                                     contentlist = contentlist + "</td></tr></table></td>"; 

                                    // alert(contentlist);
                                    // $("#contentrow").html('');
                                    // $("#contentrow").append(contentlist);
                                    // contentlist="";  
                                  /////  $("#contentrow").html(contentlist);  

                                   }
                                   else if(tneary1>=4){
                                      // contentlist1="";    
                                      contentlist1 = contentlist1 + "<td valign='top' class='text-uppercase text-center' style='border:1px solid #000;' height='600px;'  width='550px' align='center'><table width='100%' border=0><tr><td valign='top' align='center' class='text-uppercase text-center' style='align:center;'>";
                                      $.each(datasub.driverdata.TransactionLogisticdriver.BatchDetailsNew, function(index3, val1){

                                            // console.log(val.transactionid);
                              
                                             rowlist1=rowlist1+1;

                                              contentlist1 = contentlist1 + "<span style='color:"+val1.transcolor+"; font-size:20pt;'><strong>"+val1.transactionid+"</strong></span> <br>";

                                              if(rowlist1==25){
                                                   rowlist1 = 0;
                                                   contentlist1 = contentlist1 + "</td><td valign='top' align='center' class='text-uppercase text-center' style='align:center; border-left:1px dashed #333'>";
                                              }
                                           

                                      });
                                        if(datasub.driverdata.TransactionLogisticdriver.BatchDetailsNew.length == 0){

                                           contentlist1 = contentlist1 + "<br><span style='color:#000;'>No Records Found</span>";
                                        }
                                       contentlist1 = contentlist1 + "</td></tr></table></td>"; 

                                      // alert(contentlist1);

                                       // alert($("#contentrow1").html());

                                       // contentlist1 = $("#contentrow1").html() + contentlist1;

                                        // $("#contentrow1").html('');
                                        // $("#contentrow1").append(contentlist1);
                                        /////$("#contentrow1").html(contentlist1);  
                                        // contentlist1="";   

                                   }





                                }


                           }); 

                            // alert(contentlist);

                 });
                

                

                  // $("#contentrow").html('');
                  // $("#contentrow1").html('');
              
                  // $("#contentrow").html(contentlist); 
                  // $("#contentrow1").html(contentlist1); 
                
                // contentlist="";  
                // contentlist1=""; 
                 // alert(contentlist);

                  // contentlist = contentlist + "</tr>"; 

                 // $("#contentrow").html(contentlist);  


            }

        });
        
        //Pending Transaction Start 

            $.ajax({
              method: "POST",
              url: "/jlogistic/unassignedtransaction",
              dataType:'json',
              data: { 
      
              },
              beforeSend: function(){
                  
              },
              success: function(data) {
                  // console.log(data.tdata.Transactionpending.Transactinlist); 
                   headinglist3 = "";
                   headinglist4 = "";
                   headinglist5 = "";
                   contentlist3 = "";
                   contentlist4 = "";
                   contentlist5 = "";
                   headitneary = 0;
                   rowitneary = 0;
                   rowlist3 = 0;
                   rowlist4 = 0;
                   rowlist5 = 0;

                   var regioncount = data.tdata.Transactionpending.Regionlist.length; 
                     rcount = regioncount;
                     // alert(data.tdata.Transactionpending.Regionlist.length);
                     if(regioncount==1){
                       $('#block5').removeClass('mySlides');
                       $('#block6').removeClass('mySlides');
                       $('#block5').find('tbody').hide();
                       $('#block6').find('tbody').hide();
                     }
                     
                     

                    $.each(data.tdata.Transactionpending.Regionlist, function (index, value) {
                      headitneary = headitneary +1;
                      if(regioncount == 1){
                       headinglist3 = headinglist3 + "<th class='text-uppercase text-center tablewidth' align='center'><table border=0 class='tablewidth'><tr><th align='center' class='text-uppercase text-center listheight col-centered'  style='align:center;'><span class='font-weight'>&nbsp;&nbsp;&nbsp;"+value.regionname+" :: Unassigned Transactions <span style='color:#CEEFF3'>("+value.countpending+")</span> &nbsp;&nbsp;&nbsp;</span></th>";
                      }

                      if(regioncount == 3){
                        if(headitneary == 1){
                          headinglist3 = headinglist3 + "<th class='text-uppercase text-center tablewidth' align='center'><table border=0 class='tablewidth'><tr><th align='left' class='text-uppercase listheight'  style='align:left;'><span class='font-weight'>&nbsp;&nbsp;&nbsp;"+value.regionname+" :: Unassigned Transactions <span style='color:#CEEFF3'>("+value.countpending+")</span> &nbsp;&nbsp;&nbsp;</span></th>";
                        }
                        else if(headitneary == 2){
                          headinglist4 = headinglist4 + "<th class='text-uppercase text-center tablewidth' align='center'><table border=0 class='tablewidth'><tr><th align='center' class='text-uppercase text-center listheight col-centered'  style='align:center;'><span class='font-weight'>&nbsp;&nbsp;&nbsp;"+value.regionname+" :: Unassigned Transactions <span style='color:#CEEFF3'>("+value.countpending+")</span> &nbsp;&nbsp;&nbsp;</span></th>";
                        } 
                        else if(headitneary == 3){
                          headinglist5 = headinglist5 + "<th class='text-uppercase text-center tablewidth' align='center'><table border=0 class='tablewidth'><tr><th align='center' class='text-uppercase text-center listheight col-centered'  style='align:center;'><span class='font-weight'>&nbsp;&nbsp;&nbsp;"+value.regionname+" :: Unassigned Transactions <span style='color:#CEEFF3'>("+value.countpending+")</span> &nbsp;&nbsp;&nbsp;</span></th>";
                        }
                        

                      }  


                    });

                    if(regioncount == 1){
                      $("#headingrow3").html(headinglist3);  
                    } 
                    else if(regioncount == 3) {
                      $("#headingrow3").html(headinglist3);  
                      $("#headingrow4").html(headinglist4);  
                      $("#headingrow5").html(headinglist5);  
                    }

                    contentlist3 = contentlist3 + "<td valign='top' class='text-uppercase text-center' style='border:1px solid #000; width:100%;' height='600px' align='center'><table style='width:100%; min-height:600px;' height='600px' border=0><tr><td valign='top' align='center'  class='text-uppercase text-center' style='align:center;'>";

                    // console.log(data.tdata.Transactionpending.Transactinlist);

                    $.each(data.tdata.Transactionpending.Transactinlist, function (index, value5) {

                        // console.log('EE'+value5.regionid+'EE');  

                         if(regioncount == 1) {
                          rowlist3=rowlist3+1;
                           contentlist3 = contentlist3 + "<span style='color:#000; font-size:15pt;'><strong>"+value5.transactionid+"</strong></span> <br>";

                                    if(rowlist3==25){
                                       rowlist3 = 0;
                                       contentlist3 = contentlist3 + "</td><td valign='top' align='center' class='text-uppercase text-center' style='align:center; border-left:1px dashed #333'>";
                                  }


                           }

                           if(regioncount == 3) {
                              
                             if(value5.regionid == 1 ){

                              rowlist3=rowlist3+1;
                              contentlist3 = contentlist3 + "<span style='color:#000; font-size:15pt;'><strong>"+value5.transactionid+"</strong></span> <br>";

                                    if(rowlist3==25){
                                       rowlist3 = 0;
                                       contentlist3 = contentlist3 + "</td><td valign='top' align='center' class='text-uppercase text-center' style='align:center; border-left:1px dashed #333'>";
                                  }

                              }
                              else if(value5.regionid == 2 ){

                                rowlist4 = rowlist4 +1;
                              contentlist4 = contentlist4 + "<span style='color:#000; font-size:15pt;'><strong>"+value5.transactionid+"</strong></span> <br>";

                                    if(rowlist4==25){
                                       rowlist4 = 0;
                                       contentlist4 = contentlist4 + "</td><td valign='top' align='center' class='text-uppercase text-center' style='align:center; border-left:1px dashed #333'>";
                                  }

                              }
                              else if(value5.regionid == 3 ){
                                rowlist5 = rowlist5 +1;
                              contentlist5 = contentlist5 + "<span style='color:#000; font-size:15pt;'><strong>"+value5.transactionid+"</strong></span> <br>";

                                    if(rowlist5==25){
                                       rowlist5 = 0;
                                       contentlist5 = contentlist5 + "</td><td valign='top' align='center' class='text-uppercase text-center' style='align:center; border-left:1px dashed #333'>";
                                  }

                              }
                           }   
                                  

                                  

                    });
                    

                    if(regioncount == 1) {

                      if(rowlist3 ==0){

                           contentlist3 = contentlist3 + "<br><span style='color:#000; text-align:center;'>No Records Found</span><br><br>";

                        }

                       contentlist3 = contentlist3 + "</td></tr></table></td>"; 
                       $("#contentrow3").html(contentlist3);  
                    }
                    else if(regioncount == 3) {
                        if(rowlist3 ==0){

                           contentlist3 = contentlist3 + "<br><span style='color:#000; text-align:center;'>No Records Found</span><br><br>";

                        }
                        if(rowlist4 ==0){

                           contentlist4 = contentlist4 + "<br><span style='color:#000; text-align:center;'>No Records Found</span><br><br>";

                        }
                        if(rowlist5 ==0){

                           contentlist5 = contentlist5 + "<br><span style='color:#000; text-align:center;'>No Records Found</span><br><br>";

                        }
                      contentlist3 = contentlist3 + "</td></tr></table></td>"; 
                      contentlist4 = contentlist4 + "</td></tr></table></td>"; 
                      contentlist5 = contentlist5 + "</td></tr></table></td>"; 

                      // console.log('LIST1'+contentlist3+'LIST1');
                      // console.log('LIST2'+contentlist4+'LIST2');
                      // console.log('LIST3'+contentlist5+'LIST3');

                      $("#contentrow3").html(contentlist3);  
                      $("#contentrow4").html(contentlist4);  
                      $("#contentrow5").html(contentlist5);  

                    }
                  
              }
          });

          //Pending Transaction End 
      setTimeout(function() {getStatistic() }, 60000 );
    }



});
</script>
<script>
var myIndex = 0;
carousel();
function carousel() {
    var i;
    var x = document.getElementsByClassName("mySlides");
    // console.log(x);
    for (i = 0; i < x.length; i++) {
       x[i].style.display = "none";  
    }
    myIndex++;
    if (myIndex > x.length) {myIndex = 1}    
    x[myIndex-1].style.display = "block";  
    setTimeout(carousel, 12000); // Change image every 2 seconds
}
</script>

</html>

