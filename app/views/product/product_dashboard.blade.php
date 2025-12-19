<!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Product Dashboard</title>
{{ HTML::style('css/bootstrap.min.css') }}
{{ HTML::script('js/jquery.js') }}
{{ HTML::script('js/bootstrap.min.js') }}
{{ HTML::style('font-awesome/css/font-awesome.min.css') }}

<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>

    <link href="https://nightly.datatables.net/css/jquery.dataTables.css" rel="stylesheet" type="text/css" />
    <script src="https://nightly.datatables.net/js/jquery.dataTables.js"></script>
    
<style type="text/css">
  
  .table>tbody>tr>th {
    background: #C9D8ED !important;
  }

  .table-striped>tbody>tr:nth-child(odd){
    background: #DFE7F2;
  }

  .table-striped>tbody>tr:nth-child(even) {
    background-color: #E4EBF3;
}

  .graph-wrapper{
    width: 100%
  max-width: 750px
  margin: 0 auto}
  
.graph{
  height: 250px
  width: 100%
  max-width: 750px
  padding: 25px
  background-color: #FFF
  border-radius: 5px
  box-shadow: 0 2px 5px rgba(0,0,0,.2)
}

svg{
  width: 100% !important
}

/*.body_bg{*/
  /*background: #fff; #DFE7F2;*/
/*}*/

.body_bg{
  background: #fff; 
}
.firstCol{
  background: #a3b2d3d6 !important; 
  border-right: 1px solid white;
}
.secondCol{
  background: #a3b2d3d6;
}

.donutMargin{
  margin-bottom: 30px;
}

.lineGraphMargin{
  margin-bottom: 30px;
}

.tableStyle{
  border: 1px solid #d0cece80;
}
</style>

</head>
<body class="body_bg" onLoad="redirTimer()">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css">
  <div id="page-wrapper">
   
      <div class="container-fluid">
        <div class="col-lg-12 firstCol">
          <div class='graph-wrapper lineGraphMargin'>
            <h3><center>Top 5 Weekly Products</center></h3>
            <div class='graph' id='pushups'></div>
          </div>
        </div>
        <div class="col-lg-12">
            <h2 style="color:red;">Total Sold Item : <span id="total-sales-item"></span><?php //echo $totalAll; ?></h2>
       
          <table class="table table-striped tableStyle" style="font-size:22px;font-weight:bolder;" id="table_id">
            <thead>
            <tr>
              <!--<th>JC CODE</th>-->
              <th>NAME</th>
              <!--<th>LABEL</th>-->
              <th>TOTAL</th>
             
            </tr> 
           </thead>
           <tbody>
            <?php 
            
            $item_total = 0;
              foreach ($collection as $key => $value) {
                  
             
                // if (!empty($value['0']->total)) {                
            ?>
            <tr>
              <!--<td>{{$value['qrcode']}}</td>-->
              <td>{{$value['name']}}</td>
              <!--<td>{{$value['price_label']}}</td>-->
              <?php //$value['total'] ?>
               <td>  
              <?php switch ($value['sku']) {
                    case 'JC-0000000008550':
                        $totalFinal = ( $value['total'] * 6 ) * 0.5 ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000008744':
                        $totalFinal = ( $value['total'] * 6 ) * 0.5 ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010341':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010342':
                        $totalFinal = ( $value['total'] * 4 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010568':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010569':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010570':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010571':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010572':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010573':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010574':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010575':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010576':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010577':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010578':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010579':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010580':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010581':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010582':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010583':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010584':
                        $totalFinal = ( $value['total'] * 6 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010585':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010586':
                        $totalFinal = ( $value['total'] * 6 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010587':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010588':
                        $totalFinal = ( $value['total'] * 6 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010589':
                        $totalFinal = ( $value['total'] * 6 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010590':
                        $totalFinal = ( $value['total'] * 6 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010591':
                        $totalFinal = ( $value['total'] * 6 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010592':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010593':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010594':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010595':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010596':
                        $totalFinal = ( $value['total'] * 6 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010597':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010598':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010599':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010600':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010601':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                    case 'JC-0000000010602':
                        $totalFinal = ( $value['total'] * 3 )  ;
                        $item_total = $item_total + $totalFinal ; break; 
                  
                    
                    default:
                        $totalFinal = $value['total'];
                } 
                
                echo $totalFinal;
                
                ?>
                </td>
            </tr>
             <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
        
      <div class="container-fluid">
 
        
      </div>
</div>


</body>

<script>

new Morris.Line({
  element: 'pushups',
  data: [
  <?php foreach ($collection3 as $key => $value) { 
    $date = $value['0']->transaction_date;
    $data =  $value['0']->total;
    $name =  $value['0']->name.'('.$value['0']->price_label.')';
    if (!empty($data)) {
      echo'{ b: '."'".$name."'".', a: '.$data.'}'.',';  
    }           
  }?>
  ],
  xkey: ['b'],
  parseTime: false,
  ykeys: ['a'],
  // gridTextSize: 2,
  labels: ['Total Sold'],
  lineColors: ['#373651','#E65A26'], 
});

$(document).ready(function() {

$("#total-sales-item").html(<?php echo $item_total; ?>)

  $.noConflict();
    var table = $('#table_id').DataTable(
{searching: false,pageLength: 10,
    "order": [[ 1, "desc" ]]
}
      );

    var pageInfo = table.page.info();

  // Start an interval to go to the "next" page every 3 seconds
  var interval = setInterval(function(){
    // "Next" ...
    table.page( 'next' ).draw( 'page' );
    
    // If were on the last page, clear the interval
    if ( table.page()+1 === pageInfo.end ) // +1 the current page, since it starts at 0
        clearInterval(interval);
        // alert('test');
  }, 10000);
} );


redirTime = "60000";
redirURL = "https://api.jocom.com.my/product/productdashboard";
function redirTimer() { self.setTimeout("self.location.href = redirURL;",redirTime); }
</script>

</html>

