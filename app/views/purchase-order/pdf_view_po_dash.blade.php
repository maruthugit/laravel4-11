<!DOCTYPE html>
<html lang="en" dir="ltr"><head>
    <meta charset="utf-8">
    <title></title>
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> --}}

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <style media="screen">
      /* .table-bordered th,td {border: solid 1px #ccc !important;} */
      td, th {
          border: 1px solid !important;
          width: 7%;
          word-break:break-all;
          word-wrap:break-word;
          font-size: 12px;
      }
      thead{
        background: #D9D9D9;
      }
      .panel-default{
        padding: 10px;
        margin: -20px;
      }
      .center {
        text-align: center;
        /* border: 1px solid #000; */
      }

    </style>

  </head><body>
    <div class="container">
      <div class="panel panel-default">
        <div class="panel-heading text-center center">
          <h2> <b>PO Dashboard</b> </h2>
          @if($time_period == 'Monthly')
            <p> <b>From Date : </b>{{$start_date->format('F')}} {{$start_date->year}} </p>
            <p> <b>From Date : </b>{{$end_date->format('F')}} {{$end_date->year}} </p>
          @else
            <p> <b>From Date : </b>{{$start_date}} </p>
            <p> <b>From Date : </b>{{$end_date}} </p>
          @endif
        </div>
        <div class="panel-body">
          <hr>
          @if(empty($pos))
            <table border="0" cellspacing="0" cellpadding="4" width="100%" style="border:1px solid #000;">
              <thead>
                <tr border="0" cellspacing="0" cellpadding="4" width="100%" style="border:1px solid #000;">
                  <th style="width:12%;">SELLER NAME</th>
                  <th style="width:12%;">SKU</th>
                  <th style="width:12%;">PRODUCT NAME</th>
                  <th style="width:12%;">LABEL</th>
                  <th>UOM</th>
                  <th>BASE UNIT</th>
                  <th>PACKING FACTOR</th>
                  <th>PRICE</th>
                  <th>TOTAL QUANTITY</th>
                  <th>TOTAL AMOUNT</th>
                  <th>FOC</th>
                  <th>STATUS</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="11">No records found</td>
                </tr>
              </tbody>
            </table>
          @else
          
           
            
                      <table border="0" cellspacing="0" cellpadding="4" width="100%" style="border:1px solid #000;table-layout: fixed">
               
                  <tr border="0" cellspacing="0" cellpadding="4" width="100%" style="border:1px solid #000;">
                    <th style="width:12%;">SELLER NAME</th>
                    <th style="width:12%;">SKU</th>
                    <th style="width:12%;">PRODUCT NAME</th>
                    <th style="width:12%;">LABEL</th>
                    <th>UOM</th>
                    <th>BASE UNIT</th>
                    <th>PACKING FACTOR</th>
                    <th>PRICE</th>
                    <th>TOTAL QUANTITY</th>
                    <th>TOTAL AMOUNT</th>
                    <th>FOC</th>
                    <th>STATUS</th>
                  </tr>
                
                <body border="0" cellspacing="0" cellpadding="4" width="100%" style="border:1px solid #000;">
                    @foreach($pos as $key=>$po)
            <?php   
            if($po->status==1){
                      $status="Active";
                      }
                      if ($po->status==4) {
                      $status="Revised";
                      }
                      if($po->status==2){
                      $status="Cancelled"; 
                      }
                      ?>
                  <?php
                    $po_details = PurchaseOrder::orderlists()
                      ->join('jocom_purchase_order_details as po_details', 'po.id', '=', 'po_details.po_id')->where('po.id',$po->id)
                      ->select('po.id','po.po_no','po_details.sku','po_details.product_name','po_details.price_label','po_details.uom','po_details.base_unit','po_details.packing_factor','po_details.price','po_details.quantity','po_details.total','po_details.foc','seller.company_name as company_name')->get();
                    ?>
                  @foreach($po_details as $key=>$po_detail)
                    <tr>
                      <td>{{$po_detail->company_name}}</td>
                      <td>{{$po_detail->sku}}</td>
                      <td>{{$po_detail->product_name}}</td>
                      <td>{{$po_detail->price_label}}</td>
                      <td>{{$po_detail->uom}}</td>
                      <td>{{$po_detail->base_unit}}</td>
                      <td>{{$po_detail->packing_factor}}</td>
                      <td>{{$po_detail->price}}</td>
                      <td>{{$po_detail->quantity}}</td>
                      <td>{{$po_detail->total}}</td>
                      <td>{{$po_detail->foc}}</td>
                      <td>{{$status}}</td>
                    </tr>
                  @endforeach
                  @endforeach
                </body>
              </table>
            
          @endif
        </div>
      </div>
    </div>

  </body></html>
