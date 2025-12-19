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
          width: 6%;
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
          <H2> <b>PO Dashboard - Product Details</b> </H2>
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
          <table border="0" cellspacing="0" cellpadding="4" width="100%" style="border:1px solid #000; table-layout: fixed">
            <head>
              <tr border="0" cellspacing="0" cellpadding="4" width="100%" style="border:1px solid #000;">
                <th>SELLER NAME</th>
                <th style="width:8%;">PO NO</th>
                <th style="width:10%;">SKU</th>
                <th style="width:10%;">PRODUCT NAME</th>
                <th style="width:8%;">LABEL</th>
                <th>UOM</th>
                <th>BASE UNIT</th>
                <th>PACKING FACTOR</th>
                <th>PRICE</th>
                <th>QUANTITY</th>
                <th>TOTAL AMOUNT</th>
                <th>FOC</th>
                <th>STATUS</th>
              </tr>
            </head>
            <body border="0" cellspacing="0" cellpadding="4" width="100%" style="border:1px solid #000;">
              @if(empty($po_details))
                <tr>
                  <td>No records found.</td>
                </tr>
              @else
                @foreach($po_details as $key=>$po_detail)
                  <tr>
                    <td>{{$po_detail->company_name}}</td>
                    <td style="width:8%;">{{$po_detail->po_no}}</td>
                    <td style="width:10%;">{{$po_detail->sku}}</td>
                    <td style="width:10%;">{{$po_detail->product_name}}</td>
                    <td style="width:8%;">{{$po_detail->price_label}}</td>
                    <td>{{$po_detail->uom}}</td>
                    <td>{{$po_detail->base_unit}}</td>
                    <td>{{$po_detail->packing_factor}}</td>
                    <td>{{$po_detail->price}}</td>
                    <td>{{$po_detail->quantity}}</td>
                    <td>{{$po_detail->total}}</td>
                    <td>{{$po_detail->foc}}</td>
                      <?php   if($po_detail->status==1){
                      $status="Active";
                      }
                      elseif ($po_detail->status==4) {
                      $status="Revised";
                      }
                      else{
                      $status="Cancelled"; 
                      }
                      ?>
                    @if($status =="Cancelled")  
                    <td style="color:#fc2626">{{$status}}</td>
                    @else
                    <td>{{$status}}</td>
                    @endif
                  </tr>
                @endforeach
              @endif
            </body>
          </table>
        </div>
      </div>
    </div>

  </body></html>
