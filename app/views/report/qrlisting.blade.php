<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1">
		<title>CMS</title>
	    <!-- Bootstrap Core CSS -->
	    {{-- HTML::style('//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css') --}}
	    {{ HTML::style('css/bootstrap.min.css') }}
	    </head>
            <style>
                .desc-b-right{
                    border-right:none !important;
                }
                .qr-b-left{
                    border-left:none !important;
                }
            </style>
    <body>
    	<!-- /.panel-heading -->
        <div class="panel-body">
            <div class="" style="overflow-x: none">
                <?php 

                    if(count($display_listing) > 2) {
                        $headerColumn = 3;
                    }else{
                        $headerColumn = count($display_listing);
                    }
                ?>
                <table class="table table-responsive table-striped table-bordered table-hover " id="" style="table-layout: fixed;">
                    <thead>
                        <?php for($x=1;$x<=$headerColumn;$x++) { ?>
                        <th class="desc-b-right" >Product</th>
                        <th class="qr-b-left" style="width: 120px;"></th>
                         <?php } ?>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 1;
                    	foreach ($display_listing as $listing){ 
                            if ($count == 1) { echo "<tr>"; }
                    			
                    	?>
                            <td>
                                <div><img src="{{ url('images/data/'.$listing->img_1) }}" style="width:100px;height:100px;"></div>
                                <div><?php echo $listing->product_id; ?> </div>
                                <div>
                                    <p>
                                        <span><?php echo $listing->name; ?>&nbsp;&nbsp; </span>
                                    </p>
                                </div>
                            </td>
                            
                            <td><img src="{{ url('images/qrcode/'.$listing->qrcode_file) }}" style="width:100px;height:100px;"></td>
                            
                        <?php   
                        if ($count == 3)
                    		{
                    			echo "</tr>";
                    			$count = 1;
                    		}
                    		else{
                    			$count++;
                                }
                        }
                                ?>
                      
                    </tbody>
                </table>
            </div>                            
        <?php // echo $display_listing->appends(array($param => $value))->links(); ?>
        </div>
        <!-- /.panel-body -->
    </body>
</html>