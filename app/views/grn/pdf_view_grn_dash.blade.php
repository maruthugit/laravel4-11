<!DOCTYPE html>
<html lang="en" dir="ltr"><head>
    <meta charset="utf-8">
    <title></title>
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">-->

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
      head{
        background: #D9D9D9;
      }
      .panel-default{
        padding: 10px;
        margin: -20px;
      }

    </style>

  </head><body>
    <div class="container">
      <div class="panel panel-default">
        <div class="panel-heading text-center">
          <h2> <b>GRN Dashboard</b> </h2>
          @if($time_period == 'Monthly')
            <p> <b>From Date : </b>{{$start_date->format('F')}} {{$start_date->year}} </p>
            <p> <b>From Date : </b>{{$end_date->format('F')}} {{$end_date->year}} </p>
          @else
            <p> <b>From Date : </b>{{$start_date}} </p>
            <p> <b>From Date : </b>{{$end_date}} </p>
          @endif
        </div>
        <div class="panel-body">
          @if(empty($grns))
            <hr>
            <table border="0" cellspacing="0" cellpadding="4" width="100%" style="border:1px solid #000;">
              <head>
                <tr border="0" cellspacing="0" cellpadding="4" width="100%" style="border:1px solid #000;">
                  <th style="width:12%;">SKU</th>
                  <th style="width:12%;">PRODUCT NAME</th>
                  <th style="width:12%;">LABEL</th>
                  <th>UOM</th>
                  <th>BASE UNIT</th>
                  <th>PACKING FACTOR</th>
                  <th>PRICE</th>
                  <th>TOTAL QUANTITY</th>
                  <th>TOTAL STOCK IN QTY</th>
                  <th>TOTAL AMOUNT</th>
                  <th>FOC</th>
                  <th>FOC QUANTITY</th>
                  <th>FOC UOM</th>
                  <th>STATUS</th>
                </tr>
              </head><body>
                <tr>
                  <td colspan="12">No records found</td>
                </tr>
              </body></table>
          @else
            @foreach($grns as $key=>$grn)
              <b>GRN No : {{$grn->grn_no}} | Seller Name : <?php echo strtoupper($grn->company_name); ?> | Status : <?php if($grn->status=="1"){$sts="Completed";}else if($grn->status=="0"){$sts="Pending";}else{
              $sts="Partial"; }?> {{$sts}}</b>
              <table border="0" cellspacing="0" cellpadding="4" width="100%" style="border:1px solid #000;table-layout: fixed">
                <head>
                  <tr border="0" cellspacing="0" cellpadding="4" width="100%" style="border:1px solid #000;">
                    <th style="width:12%;">SKU</th>
                    <th style="width:12%;">PRODUCT NAME</th>
                    <th style="width:12%;">LABEL</th>
                    <th>UOM</th>
                    <th>BASE UNIT</th>
                    <th>PACKING FACTOR</th>
                    <th>PRICE</th>
                    <th>TOTAL QUANTITY</th>
                    <th>TOTAL STOCK IN QTY</th>
                    <th>TOTAL AMOUNT</th>
                    <th>FOC</th>
                    <th>FOC QUANTITY</th>
                    <th>FOC UOM</th>
                    <th>STATUS</th>
                  </tr>
                </head><body border="0" cellspacing="0" cellpadding="4" width="100%" style="border:1px solid #000;">
                  <?php
                    $grn_details = WarehouseGrn::lists()
                    ->join('jocom_warehouse_grn_details as grn_details', 'grn.id', '=', 'grn_details.grn_id')->where('grn.id',$grn->id)
                    ->select('grn.id','grn.grn_no','grn_details.sku','grn_details.product_name','grn_details.price_label','grn_details.uom','grn_details.base_unit','grn_details.packing_factor','grn_details.price','grn_details.quantity','grn_details.stockin_qty','grn_details.total','grn_details.foc','grn_details.foc_qty','grn_details.foc_uom', 'seller.company_name as company_name','grn_details.status')->get();
                    ?>
                  @foreach($grn_details as $key=>$grn_detail)
                 
           
                    <tr>
                      <td style="width:12%;">{{$grn_detail->sku}}</td>
                      <td style="width:12%;">{{$grn_detail->product_name}}</td>
                      <td style="width:12%;">{{$grn_detail->price_label}}</td>
                      <td>{{$grn_detail->uom}}</td>
                      <td>{{$grn_detail->base_unit}}</td>
                      <td>{{$grn_detail->packing_factor}}</td>
                      <td>{{$grn_detail->price}}</td>
                      <td>{{$grn_detail->quantity}}</td>
                      <td>{{$grn_detail->stockin_qty}}</td>
                      <td>{{$grn_detail->total}}</td>
                      <td>{{$grn_detail->foc}}</td>
                      <td>{{$grn_detail->foc_qty}}</td>
                      <td>{{$grn_detail->foc_uom}}</td>
                       @if($grn_detail->status=="2")
                      <td>Partial</td>
                       @else
                    <td>Complete</td>
                       @endif
                    </tr>
                  @endforeach
                </body></table>
              <br>
            @endforeach
          @endif
        </div>
      </div>
    </div>

</body></html>
