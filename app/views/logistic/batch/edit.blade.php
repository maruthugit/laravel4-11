@extends('layouts.master')
@section('title') Logistic Batch @stop
@section('content')

<?php 
$tempcount = 1;
?>

<div id="page-wrapper">

	<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Logistic Batch Management</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="row">
        <div class="col-lg-12">            
        @if (Session::has('message'))
            <div class="alert alert-danger">
                <i class="fa fa-exclamation"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
        @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif

			{{ Form::open(array('url' => array('batch/update/' . $display_batch['batch_id']) , 'class' => 'form-horizontal', 'method' => 'PUT')) }}

			<div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title"><i class="fa fa-user"></i> Batch Details</h2>
		        </div>
				<div class="panel-body">
					<div class="col-lg-12">
						<div class='form-group'>
							{{ Form::label('batch_id', 'Batch ID', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							<p class="form-control-static">{{$display_batch['batch_id']}}</p>{{Form::input('hidden', 'id', $display_batch['batch_id'])}}
							</div>
						</div>

						<div class='form-group'>
							{{ Form::label('batch_date', 'Batch Date', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							<p class="form-control-static">{{$display_batch['batch_date']}}</p>
							</div>
						</div>

						<div class='form-group'>
							{{ Form::label('transaction_id', 'Transaction ID', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							<p class="form-control-static">{{$display_batch['transaction_id']}}</p>
							</div>
						</div>

						<div class='form-group'>
							{{ Form::label('transaction_date', 'Transaction Date', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							<p class="form-control-static">{{$display_batch['transaction_date']}}</p>
							</div>
						</div>

						<hr/>
						<div class='form-group'>
							{{ Form::label('shipping_method', 'Shipping Method', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							<p class="form-control-static">{{$display_batch['shipping_method_name']}}</p>
							</div>
						</div>

						<div class='form-group'>
							{{ Form::label('username', 'Driver Name', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							<p class="form-control-static"> {{ HTML::link('driver/edit/'.$display_batch['driver_id'], $display_batch['driver_name'], array('target'=>'_blank')) }}</p>
							</div>
						</div>

						<div class='form-group'>
							{{ Form::label('delivery_name', 'Delivery Name', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							<p class="form-control-static">{{$display_batch['delivery_name']}}</p>
							</div>
						</div>

						<div class='form-group'>
							{{ Form::label('delivery_contact_no', 'Delivery Contact No', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							<p class="form-control-static">{{$display_batch['delivery_contact_no']}}</p>
							</div>
						</div>

						<div class='form-group'>
							{{ Form::label('buyer_email', 'Buyer Email', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							<p class="form-control-static">{{$display_batch['buyer_email']}}</p>
							</div>
						</div>

						<div class='form-group'>
							{{ Form::label('delivery_addr_1', 'Delivery Address', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							<p class="form-control-static">{{$display_batch['delivery_addr_1']}}</p>
							<p class="form-control-static">{{$display_batch['delivery_addr_2']}}</p>
							</div>
						</div>

						<div class='form-group'>
							{{ Form::label('delivery_city', 'Delivery City', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							<p class="form-control-static">{{$display_batch['delivery_city']}}</p>
							</div>
						</div>

						<div class='form-group'>
							{{ Form::label('delivery_postcode', 'Delivery Postcode', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							<p class="form-control-static">{{$display_batch['delivery_postcode']}}</p>
							</div>
						</div>

						<div class='form-group'>
							{{ Form::label('delivery_state', 'Delivery State', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							<p class="form-control-static">{{$display_batch['delivery_state']}}</p>
							</div>
						</div>

						<div class='form-group'>
							{{ Form::label('delivery_country', 'Delivery Country', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							<p class="form-control-static">{{$display_batch['delivery_country']}}</p>
							</div>
						</div>

						<div class='form-group'>
							{{ Form::label('special_msg', 'Special Message', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							<p class="form-control-static">{{$display_batch['special_msg']}}</p>
							</div>
						</div>

						<div class='form-group'>
							{{ Form::label('remark', 'Remark', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-5">
								<p class="form-control-static">{{$display_batch['remark']}}</p>
							</div>
						</div>

						
						@if ( Permission::CheckAccessLevel(Session::get('role_id'), 14, 3, 'AND'))
						<div class="form-group">
		                {{ Form::label('remark', 'Add Remark', array('class'=> 'col-lg-2 control-label')) }}
		                    <div class="col-lg-5">
		                        {{ Form::textarea('remark', "", array('class'=> 'form-control')) }}
		                    </div>
		                </div>

		                <div class='form-group'>
		                	{{ Form::label('', '', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-5">
								<span class="pull-left">
								{{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
		                		{{ Form::button('Add Remark', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}}
		                		</span>
							</div>
						</div>
						@endif

		                

						<div class="form-group">
		                    {{ Form::label('status', 'Status', array('class'=> 'col-lg-2 control-label')) }}
		                    <div class="col-lg-4">
							<p class="form-control-static">{{$display_batch['status']}}</p>
							</div>
		                </div>

		                @if ( Permission::CheckAccessLevel(Session::get('role_id'), 14, 3, 'AND'))
		                <?php
		                
		                if ($display_batch['status2'] == '0' OR $display_batch['status2'] == '1')
		                {
		                	?>
							<div class="form-group">
			                    {{ Form::label('action', 'Change Status', array('class'=> 'col-lg-2 control-label')) }}
			                    <div class="col-lg-7">
			                       <?php
			                       	$tempurl = asset('/')."batch/update/";

				                	echo ' <a class="btn btn-danger" title="" data-toggle="tooltip" href="' . $tempurl . $display_batch["batch_id"] . '?status=2"> Returned </a>';

				                	echo ' <a class="btn btn-danger" title="" data-toggle="tooltip" href="' . $tempurl . $display_batch["batch_id"] . '?status=3"> Undelivered </a>';

				                	echo ' <a class="btn btn-danger" title="" data-toggle="tooltip" href="' . $tempurl . $display_batch["batch_id"] . '?status=5"> Cancelled </a>';

				                	echo ' <a class="btn btn-success" title="" data-toggle="tooltip" href="' . $tempurl . $display_batch["batch_id"] . '?status=4"> Sent </a>';
			                       ?>
			                    </div>
			                </div>
			            <?php
			            }
		               
			            ?>
			            @endif

						<div class='form-group'>
							{{ Form::label('do_no', 'Do No', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							<p class="form-control-static">
								<!-- {{$display_batch['do_no']}}</p> -->
		                     	<?php
		                            $file = asset('/') . Config::get('constants.LOGISTIC_DO_PATH') . '/' . $display_batch['do_no'] . '.pdf';
		                            // $file = Config::get('constants.LOGISTIC_DO_PATH') . '/' . urlencode($display_batch['do_no']) . '.pdf';
		                            // $encrypted = Crypt::encrypt($file);
		                            // $encrypted = urlencode(base64_encode($encrypted)); 
		                            ?>
		                            {{ HTML::link($file, $display_batch['do_no'], array('target'=>'_blank')) }}
		                            <?php
		                        ?>
		                    </p>
		                	</div>
						</div>

						<div class='form-group'>
							{{ Form::label('accept_date', 'Accept Date', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
								<p class="form-control-static">{{$display_batch['accept_date']}}</p><!-- {{Form::input('hidden', 'remark', $batch->accept_date)}} -->
							</div>
						</div>

						<div class='form-group'>
							{{ Form::label('sign_name', 'Recipient Name', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							<p class="form-control-static">
								{{$display_batch['sign_name']}}</p>
		                    </p>
		                	</div>
						</div>

						<div class='form-group'>
							{{ Form::label('sign_ic', 'Recipient IC No', array('class' => 'col-lg-2 control-label')) }}
							<div class="col-lg-4">
							<p class="form-control-static">
								{{$display_batch['sign_ic']}}</p>
		                    </p>
		                	</div>
						</div>

		                <hr/>

		                <div class="row">
		                <div class="col-lg-12">
		                    <div class="panel panel-default">
		                        <div class="panel-heading">
		                            Batch Item Details
		                        </div>
		                        <!-- /.panel-heading -->
		                        <div class="panel-body">
		                            <div class="dataTable_wrapper">
		                                <table class="table table-striped table-bordered table-hover" id="dataTables-logistic_item">
		                                    <thead>
		                                       <tr>
		                                            <th>No</th>
		                                            <th>Batch ID</th>
		                                            <th>Sku</th>
		                                            <th>Product</th>
		                                            <th>Label</th>
		                                            <th>Qty Assign</th>
		                                            <th>Qty Pickup</th>
		                                            <th>Qty Sent</th>
		                                            <th>Remark</th>
		                                        </tr>
		                                    </thead>
		                                    <tbody>
		                                    	<?php
		                                    	if (count($display_batch['item'])>0)
		                                    	 {
		                                    	 ?>  
		                                        @foreach($display_batch['item'] as $batch_item)
		                                            <tr class="odd gradeX">
		 		
		                                                <td>{{$tempcount++}}</td>
		                                                <td>{{$batch_item['item_id']}}</td>
		                                                <td>{{$batch_item['sku']}}</td>
		                                                <td>{{$batch_item['name']}}</td>
		                                                <td>{{$batch_item['label']}}</td>
		                                               	<td>{{$batch_item['qty_assign']}}</td>
		                                               	<td>{{$batch_item['qty_pickup']}}</td>
		                                               	<td>{{$batch_item['qty_sent']}}</td>
		                                              	<td>{{$batch_item['remark']}}</td>
		                                             </tr>
		                                        @endforeach   
		                                       <?php
		                                       }
		                                       ?>                                                             
		                                    </tbody>
		                                </table>
		                            </div>

		                            <!-- /.table-responsive -->
		                            
		                        </div>
		                        <!-- /.panel-body -->
		                    </div>
		                    <!-- /.panel -->
		                </div>
		          	      <!-- /.col-lg-12 -->
		            	</div>
						
						</div>
		    		</div>

				</div>
				<div class='form-group'>
				<!-- <div class="col-lg-10">
					{{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
					{{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
				</div> -->
			</div>
			</div>

		 	<hr/>

			
			
			{{ Form::close() }}
		</div>
	</div>

</div>

@stop
