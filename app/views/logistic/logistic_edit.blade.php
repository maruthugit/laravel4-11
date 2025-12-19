@extends('layouts.master')
@section('title') Logistic @stop
@section('content')



<?php 
$tempcount = 1;
$tempcount2 = 1;
$tempcount3 = 1;
?>

<style type="text/css">
    button.close{
      position: relative;
      z-index: 1;
      right: 20px;
      opacity: 1;
    }

    img.cms-image {
      position: relative;
    }

    .imagewrap {display:inline-block;position:relative;}
</style>

<!-- For datepicker in create new transaction -->
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
 <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
 <script src="../../../js/fileinput.min.js"></script>   


<script>
$(function() {
$( "#datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
});
</script>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"> Logistic Transaction Management 
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}jlogistic/edit/{{$logistic->id}}"><i class="fa fa-refresh"></i></a>
                </span>
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
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
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Logistic Transaction Details </h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url'=>'jlogistic/edit/'.$logistic->id, 'class' => 'form-horizontal','files' => true, 'method'=>'POST', 'enctype' => "multipart/form-data")) }}
                        <div class="form-group">
                        {{ Form::label('id', 'Logistic ID', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-5">
                                <p class="form-control-static">{{$logistic->id}}</p>{{Form::input('hidden', 'id', $logistic->id)}}
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('transaction_id', 'Transaction ID', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-5">
                                <p class="form-control-static"> {{ HTML::link('transaction/edit/'.$logistic->transaction_id, $logistic->transaction_id, array('target'=>'_blank')) }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('transaction_date', 'Transaction Date', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-5">
                                <p class="form-control-static">{{$logistic->transaction_date}}</p>
                            </div>
                        </div>

                        <hr />

                        <div class="form-group">
                        {{ Form::label('delivery_name', 'Delivery Name', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-5">
                                 <p class="form-control-static">{{$logistic->delivery_name}}</p>

                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('delivery_contact_no', 'Delivery Contact No', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-5">
                                 <p class="form-control-static">{{$logistic->delivery_contact_no}}</p>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('buyer_email', 'Buyer Email', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-5">
                                 <p class="form-control-static">{{$logistic->buyer_email}}</p>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('delivery_addr_1', 'Delivery Address', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-5">
                                 <p class="form-control-static">{{$logistic->delivery_addr_1}}</p>
                                 <p class="form-control-static">{{$logistic->delivery_addr_2}}</p>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('delivery_city', 'Delivery City', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-5">
                                 <p class="form-control-static">{{$logistic->delivery_city}}</p>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('delivery_postcode', 'Delivery Postcode', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-5">
                                 <p class="form-control-static">{{$logistic->delivery_postcode}}</p>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('delivery_state', 'Delivery State', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-5">
                                 <p class="form-control-static">{{$logistic->delivery_state}}</p>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('delivery_country', 'Delivery Country', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-5">
                                 <p class="form-control-static">{{$logistic->delivery_country}}</p>
                            </div>
                        </div>

                        <hr />

                        <div class="form-group">
                        {{ Form::label('special_msg', 'Special Message', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-5">
                                 <p class="form-control-static">{{$logistic->special_msg}}</p>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('do_no', 'DO No.', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-5">
                                 <!-- <p class="form-control-static">{{$logistic->do_no}}</p>{{Form::input('hidden', 'do_no', $logistic->do_no)}} -->
                        
                          <p class="form-control-static">
                                    <?php
                                    $file = ($logistic->transaction_id)."#".($logistic->do_no);
                                    // $file= ($logistic->transaction_id).($logistic->do_no);
                                    $encrypted = Crypt::encrypt($file);
                                    $encrypted = urlencode(base64_encode($encrypted)); 
                                    ?>
                                    {{ HTML::link('transaction/files/'.$encrypted, $logistic->do_no, array('target'=>'_blank')) }}
                                </p>

                            </div>
                        </div>
                        
                         <div class="form-group">
                        {{ Form::label('remark', 'Remark', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-5">
                                <p class="form-control-static">
                                    <?php 
                                     echo nl2br($logistic->remark);
                                     ?>
                                </p>
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('remark', 'Add Remark', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-5">
                                {{ Form::textarea('remark', "", array('class'=> 'form-control')) }}
                            </div>
                        </div>

                        <div class="form-group">
                        {{ Form::label('status', 'Status', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                            <select class="form-control" name="status">
                                @foreach($statusList as $status=>$value)
                                     <option value="{{$status}}"<?php if ($status == $logistic->status) echo 'selected="selected"'?>>{{ ucwords($value)}}</option>
                                @endforeach
                            </select>
                                {{Form::input('hidden', 'ori_status', $logistic->status)}}
                            </div>
                        </div>
                        
                         <div class="form-group increment @if ($errors->has('attachment')) has-error @endif">
                              
                                  <label  class="col-lg-2 control-label">Attachment</label>
                                 <div class="col-lg-3">
                                     
                             <input type="file" id="newfile" name="attachment[]" class="form-control" accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps"  required = 'required'>

                             </div>

                          <div class="input-group-btn"> 
                            <button class="btn btn-success" type="button"><i class="glyphicon glyphicon-plus"></i>Add</button>
                         </div>
      
                     </div>

                     <div class="clone hide">
                              <div class="form-group control-group  @if ($errors->has('attachment')) has-error @endif" style="margin-top:10px">
                                <label  class="col-lg-2 control-label"></label>
                                 <div class="col-lg-3">
                                <input type="file" id="newfile" name="attachment[]" class="form-control"  accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps"  required = 'required'>
                            </div>
                                <div class="input-group-btn"> 
                                  <button class="btn btn-danger" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                                </div>
                              </div>
                    </div>

                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 14, 7, 'AND'))

                        <div class='form-group'>
                            {{ Form::label('', '', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <span class="pull-left">
                                {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                {{ Form::button('Save', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}}
                                </span>
                            </div>
                        </div>

                        @endif

                        {{ Form::close() }}

                    </div> 
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
            
             <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Logistic Images By CMS
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                    @if (count($attachment_batch) > 0)
                                        @foreach ($attachment_batch as $key => $att)
                                            @if ($att->mime == 'application/pdf')
                                         <a href="/download{{$att->attachment}}" target="_blank" data-toggle="tooltip" title="<?php echo $att->attachment ?>">
                                            <img style="width:150px; height: 150px;cursor: pointer;" src="/logistic/images/acrobatpdf.jpg" class="img-thumbnail" alt="">
                                            </a>
                                         
                                            @else
                                            <div class="imagewrap" id="imagewrap{{$att->id}}">
                                            <img style="width:150px; height: 150px;cursor: pointer;" src="/logistic/images/{{ $att->attachment}}" class="img-thumbnail cms-image" alt="">
                                            <button type="submit" class="close" data-id="{{$att->id}}">
                                              <span>&times;</span>
                                            </button>
                                            </div>
                                            @endif

                                            @endforeach
                                        
                                        @endif
                                           
                                    </div>
                                    <!-- /.panel-body -->
                                </div>
                                <!-- /.panel -->
                            </div>
                            <!-- /.col-lg-12 -->
                        </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-truck"></i> Batch Assigning </h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="col-lg-12">

                        {{ Form::open(array('url'=>'apilogistic/transactiondetail', 'class' => 'form-horizontal')) }}
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Logistic Details
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <div class="dataTable_wrapper">
                                            <table class="table table-striped table-bordered table-hover" id="dataTables-logistic_item">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>SKU</th>
                                                        <th>Product</th>
                                                        <th style="width:300px;">Label</th>
                                                        <th>Delivery Time</th>
                                                        <th>Qty Order</th>
                                                        <th>Qty To Assign</th>
                                                        <th>Qty To Send</th>
                                                        <th>Assign</th>
                                                        <?php if( in_array(Session::get('region_access'),array(0)) ){ ?><th>Shipping Method</th><?php } ?>
                                                    </tr>
                                                </thead>
                                                <tbody> 
                                                    <?php
                                                    if (count($display_logistic_item['item'])>0)
                                                    {
                                                    ?>
                                                    @foreach($display_logistic_item['item'] as $logistic_item)
<!--                                                    {{print_r($logistic_item);}}-->
                                                        <tr class="odd gradeX">
                                                            
                                                            <td>{{$tempcount++}}</td>
                                                            <td>{{$logistic_item['sku']}}</td>
                                                            <td>{{$logistic_item['name']}}</td>
                                                            <td>{{$logistic_item['label']}}</td>
                                                            <td>{{$logistic_item['delivery_time']}}</td>
                                                            <td>{{$logistic_item['qty_order']}}</td>
                                                            <td>{{$logistic_item['qty_to_assign']}}
                                                            </td>
                                                            <td>{{$logistic_item['qty_to_send']}}</td>
                                                            <td>{{ Form::text('qty_assign[]', $logistic_item['qty_to_assign'], array('id'=>$logistic_item['item_id'], 'class'=> "form-control " )) }}</td>
                                                            <?php if( in_array(Session::get('region_access'),array(0)) ){ ?>
                                                            <td>
                                                                <?php if($logistic_item['qty_to_assign'] > 0) {?>
                                                                <div class="input-group">
                                                                    <select class="form-control shipper-list" >
                                                                        <?php  foreach ($shipper as $key => $value) { ?>
                                                                            <option id="<?php echo $value->id; ?>" value="<?php echo $value->id; ?>"><?php echo $value->courier_name ?></option>
                                                                        <?php  }?>                                                 
                                                                    </select>
                                                                    <span class="input-group-btn">
                                                                        <button data-qty="<?php  echo $logistic_item['qty_to_assign']?>" log-item-id="<?php  echo $logistic_item['item_id']?>" class="btn btn-default assign-shipper" type="button">Assign</button>
                                                                    </span>
                                                                </div>
                                                                <?php } ?>
                                                            </td>
                                                            <?php } ?>
                                                            {{Form::input('hidden', 'item_id[]', $logistic_item['item_id'])}}
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
                        <!-- /.row -->

                        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 14, 7, 'AND'))

                        <?php if(!$isInternationalLogistic){ ?>
                        <h4 class="ui horizontal divider header"><i class="tag icon"></i>For Local Delivery </h4>
                        <div class="form-group">
                        {{ Form::label('driver', 'Assign to Driver', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <select name="driver_id" class="form-control">
                                    @foreach($display_driver as $driver)
                                        <option value="{{ $driver['id'] }}">{{ ucwords($driver['username']) }}</option>
                                    @endforeach
                                    {{Form::input('hidden', 'cms', '1')}}
                                </select>
                            </div>
                        </div>
                        <div class='form-group'>
                            {{ Form::label('', '', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <span class="pull-left">
                                {{Form::input('hidden', 'logistic_id', $logistic->id)}}

                                {{ Form::button('Assign', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}}
                                </span>
                            </div>
                        </div>
                        <?php }?>
                        <?php if($isInternationalLogistic){ ?>
                        <h4 class="ui horizontal divider header"><i class="tag icon"></i>For international Delivery </h4>
                        <div class="form-group">
                        {{ Form::label('driver', 'Assign to Courier', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <select name="international_courier_id" class="form-control">
                                    <?php 
                                    foreach ($InternationalCourier as $keyIC => $valueIC) { ?>
                                        <option value="<?php echo $valueIC->id; ?>"><?php echo $valueIC->courier_name; ?></option>
                                    <?php } ?>
                                    {{Form::input('hidden', 'cms', '1')}}
                                </select>
                                 {{Form::input('hidden', 'is_international_logistic', 1)}}
                            </div>
                        </div>
                        <div class='form-group'>
                            {{ Form::label('', '', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <span class="pull-left">
                                {{Form::input('hidden', 'logistic_id', $logistic->id)}}
                                {{ Form::button('Assign', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}}
                                </span>
                            </div>
                        </div>
                        <?php }?>

                        @endif


                        <br>                        

                        {{ Form::close() }}

                        <hr>

                         <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Batch Details
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <div class="dataTable_wrapper">
                                            <table class="table table-striped table-bordered table-hover" id="dataTables-logistic_item">
                                                <thead>
                                                   <tr>
                                                        
                                                        <th>No</th>
                                                        <th>Batch ID</th>
                                                        <th>Batch Date</th>
                                                        <th>Transaction ID</th>
                                                        <th>Shipping Method</th>
                                                        <th>Tracking Number</th>
                                                        <th>Driver</th>
                                                        <th>Status</th>
                                                        <th>Remark</th>
                                                        <th>Do No</th>
                                                        
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if (count($display_batch['batch'])>0)
                                                    {
                                                    ?>  
                                                        @foreach($display_batch['batch'] as $logistic_batch)
                                                            <tr class="odd gradeX line-batch">
                                                                
                                                                <td>{{$tempcount2++}}</td>
                                                                <td>{{ HTML::link('batch/edit/'.$logistic_batch['id'], $logistic_batch['id'], array('target'=>'_blank')) }} </td>
                                                                <td>{{$logistic_batch['batch_date']}}</td>
                                                                <td>{{$logistic_batch['transaction_id']}}</td>
                                                                <td>{{$logistic_batch['shipping_courier']}}</td>
                                                                <td>{{$logistic_batch['tracking_number']}}</td>
                                                                <td>{{$logistic_batch['username']}}</td>
                                                                <td>{{$logistic_batch['status']}}</td>
                                                                <td>{{$logistic_batch['remark']}}</td>
                                                                <td>{{ HTML::link(asset('/') . Config::get('constants.LOGISTIC_DO_PATH') . '/' . $logistic_batch['do_no'] . '.pdf', $logistic_batch['do_no'], array('target'=>'_blank')) }}</td>
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
                        <!-- LOGISTIC RETURNED STATUS --> 
                        <hr>
                        
                         <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Returned Batch Details
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <div class="dataTable_wrapper">
                                            <table class="table table-striped table-bordered table-hover" id="dataTables-logistic_item">
                                                <thead>
                                                   <tr>
                                                        <th>No</th>
                                                        <th>Batch ID</th>
                                                        <th>Transaction ID</th>
                                                        <th>Driver</th>
                                                        <th>Return Status</th>
                                                        <th>Other Status</th>
                                                        <th>Return Date & Time</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                        @foreach($returned_batch as $return_batch)
                                                            <tr class="odd gradeX line-batch">
                                                                
                                                                <td>{{$tempcount3++}}</td>
                                                                <td>{{$return_batch->batch_id}}</td>
                                                                <td>{{$return_batch->transaction_id}}</td>
                                                                <td>{{$return_batch->name}}</td>
                                                                <td>{{$return_batch->reason}}</td>
                                                                <td>{{$return_batch->return_others}}</td>
                                                                <td>{{$return_batch->created_at}}</td>
                                                            </tr>
                                                        @endforeach  
                                                                                                                
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
                        <!-- LOGISTIC RETURNED STATUS --> 
                        <hr>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Logistic Images
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <?php foreach ($logistic_image as $key => $vImage) { ?>
                                            <img style="width:150px; height: 150px;cursor: pointer;" src="<?php  echo $vImage['filename'];?>" class="img-thumbnail view-img" alt="">
                                        <?php } ?>
                                    </div>
                                    <!-- /.panel-body -->
                                </div>
                                <!-- /.panel -->
                            </div>
                            <!-- /.col-lg-12 -->
                        </div>
                        <!-- /.row -->
                        <!-- /.row -->
                    </div> 
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    </div>
     <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Delivery Image</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
            <div class="modal-body" style="padding: 1px;">
              <img id="dlvr-viewer" src="" style="max-height:600px;width: 100%;" >
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <div id="assign-courier" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Shipping Method</h4>
            </div>
            <div class="modal-body">
              <p id="assign-courier-msg"></p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" id="close-assign-mdl" data-dismiss="modal">Close</button>
              <a type="button" id="view-slip" target="_blank" href="" class="btn btn-primary">Print Delivery Ticket</a>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->

    <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">              
          <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <img src="" class="imagepreview" style="width: 100%;" >
          </div>
        </div>
      </div>
    </div>
    
    <!-- Ninjavan -->
    
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="ninjavanModalLongTitle">Ninjavan Shipping Method</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form>
                  
                  <!-- ========= -->
                  <div class="form-group row">
                    <label for="parcelsize" class="col-sm-3 col-form-label">Size: <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                      <select class="custom-select form-control" id="parcelsize" name="parcelsize">
                            <option value="" selected>Choose Parcel Dimension</option>
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                            <option value="XXL">XXL</option>
                        </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="parcelweight" class="col-sm-3 col-form-label">Weight (k.g.):</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="parcelweight" name="parcelweight" placeholder="Weight in kilograms (k.g.).">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="parcellength" class="col-sm-3 col-form-label">length (cm):</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="parcellength" name="parcellength" placeholder="Length in centimeters (c.m.).">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="parcelwidth" class="col-sm-3 col-form-label">Width (cm):</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="parcelwidth" name="parcelwidth" placeholder="Width in centimeters (c.m.).">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="parcelheight" class="col-sm-3 col-form-label">Height (cm):</label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="parcelheight" name="parcelheight" placeholder="Height in centimeters (c.m.).">
                    </div>
                  </div>
                  <input type="hidden" class="form-control" id="trans_id" value="{{$logistic->transaction_id}}">
                  <input type="hidden" class="form-control" id="logistic_id" value="{{$logistic->id}}">

                  <input type="hidden" class="form-control" id="logistic_log_id_ninja">
                  <input type="hidden" class="form-control" id="logistic_quantity_ninja">

                  <!-- ========= -->
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary ninjaassign">Assign</button>
              </div>
            </div>
          </div>
        </div>

        <div id="assign-ninjavan" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Shipping Method</h4>
            </div>
            <div class="modal-body">
              <p id="assign-ninjavan-msg"></p>
              <input type="hidden" class="form-control" id="logistic_ninja_tracking_number">
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" id="close-assign-ninja" data-dismiss="modal">Close</button>
              <a type="button" id="view-bill" target="_blank" href="" class="btn btn-primary">Download & Print Airway Bill</a>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

     <!-- Ninjavan -->
    


@stop
@section('inputjs')

<script>
    
$(document).ready(function() {
    
    
    $(".view-img").click(function(){
        
        var imageLink = $(this).attr("src");
        
        console.log(imageLink);
        $("#dlvr-viewer").attr("src",imageLink)
        $("#exampleModalCenter").modal("show");
         
    })
    
    $(".assign-shipper").click(function(){
        
        
       
        var log_item_id = $(this).attr("log-item-id");
        var quantity = $("#"+log_item_id).val();
        //var quantity = $(this).attr("data-qty");
        var shipper = $(this).parent().parent().find('.shipper-list').val();
        
        
        console.log(log_item_id);
        console.log(quantity);
        console.log(shipper);
        // assign(log_item_id,quantity,shipper);
        if(shipper == 5){
            $('#logistic_log_id_ninja').val(log_item_id);
            $('#logistic_quantity_ninja').val(quantity);
            $("#exampleModal").modal("show");
         }
         else {
            assign(log_item_id,quantity,shipper);
         }
        
    })
    
    $('#view-bill').click(function(){

        var trackingID = $("#logistic_ninja_tracking_number").val(); 
        var url = "/ninjavan/getwaybill/"+trackingID;
            $(location).attr('href',url);

    })


    $('.assign-download').click(function(){

        var trackingID = $(this).attr("ninja-id");
        var url = "/ninjavan/getwaybill/"+trackingID;
            $(location).attr('href',url);

    })

    $(".ninjaassign").click(function(){

        var trans_id = $("#trans_id").val();
        var logistic_id = $("#logistic_id").val();
        var parcelsize = $("#parcelsize").val();
        var parcelweight = $("#parcelweight").val();
        var parcellength = $("#parcellength").val();
        var parcelwidth = $("#parcelwidth").val();
        var parcelheight = $("#parcelheight").val();
        var logistic_log_id = $("#logistic_log_id_ninja").val();
        var quantity = $("#logistic_quantity_ninja").val();



        if(parcelsize == ""){
            alert('Please Choose Parcel Dimension');
        } else {

                $.ajax({
                    method: "POST",
                    url: "/ninjavan/assign",
                    data: {
                       'trans_id':trans_id,
                       'logistic_id':logistic_id,
                       'parcelsize':parcelsize,
                       'parcelweight':parcelweight,
                       'parcellength':parcellength,
                       'parcelwidth':parcelwidth,
                       'parcelheight':parcelheight,
                       'logistic_log_id':logistic_log_id,
                       'quantity':quantity

                    },
                    beforeSend: function(){
                        console.log('assign....');
                    },
                    success: function(data) {
                         console.log(data.data.response.is_assign);
                         console.log('assigned....');
                        console.log(data);
                        console.log('assigned....');
                       
                        if(data.data.response.is_assign == 1){
                            
                            $('#logistic_ninja_tracking_number').val(data.data.response.tracking);
                            $("#assign-ninjavan-msg").html('Batch succesfully assigned!');
                            $("#assign-ninjavan").modal("show");
                            $("#exampleModal").hide()
                       
                       
                        }else{
                            // alert(data.data.response.result.api_response);
                        }
                        
                    }
                  })

        }


    })
    
    $("#close-assign-mdl").click(function(){
        location.reload();
    })
    
    $("#close-assign-ninja").click(function(){
        location.reload();
    })
    
    function assign(log_item_id,quantity,shipper){
        $.ajax({
            method: "POST",
            url: "/shipper/assign",
            data: {
               'logistic_item_id':log_item_id,
               'quantity':quantity ,
               'shipper':shipper 
            },
            beforeSend: function(){
                console.log('assign....');
            },
            success: function(data) {
                console.log(data);
                
                if(data.data.response.is_assign == 1){
                    console.log(data.data);
                    if(data.data.response.courier_code == 'TAQB'){
                    $("#assign-courier-msg").html(data.data.response.result.api_response);
                    $("#assign-courier").modal("show")
                        $("#view-slip").show()
                    $("#view-slip").attr("href",'/courier/slip/'+data.data.response.result.courier_order_id)
                    }else{
                        $("#assign-courier-msg").html('Batch succesfully assigned!');
                        $("#assign-courier").modal("show");
                        $("#view-slip").hide()
                    }
               
               
                }else{
                    alert(data.data.response.result.api_response);
                }
                
            }
          })
    }
    
});

$(function() {
        $('.pop').on('click', function() {
            $('.imagepreview').attr('src', $(this).find('img').attr('src'));
            $('#imagemodal').modal('show');   
        });     
        
        $('img.cms-image').on('click', function() {
            $('.imagepreview').attr('src', $(this).attr('src'));
            $('#imagemodal').modal('show');   
        }); 
});

 $(document).ready(function() {

      $(".btn-success").click(function(){ 
          var html = $(".clone").html();
          $(".increment").after(html);
      });

      $("body").on("click",".btn-danger",function(){ 
          $(this).parents(".control-group").remove();
      });
      
      $("div.imagewrap button.close").on("click", function() {

        var result = confirm("Are you sure to delete?");
        if (result) {
            var attachmentId = $(this).attr("data-id");

            $.ajax({
                method: "POST",
                url: `/jlogistic/deleteattachment/${attachmentId}`,
                success: function(data) {
                    if (data.status) {
                        $(`#imagewrap${attachmentId}`).remove();
                    } else {
                        alert("Error");
                    }
                }
            });
        }
      });

    });
    
   
    $(document).ready(function(){
        
    $('[data-toggle="tooltip"]').tooltip();
});

</script>

@stop

