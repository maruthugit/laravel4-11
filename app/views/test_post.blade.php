@extends('layouts.master')
@section('content')


<div id="page-wrapper">
<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><i class="fa fa-home"></i> Test Post</h1>
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
                    <h3 class="panel-title"><i class="fa fa-list"></i> Add Test</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                    	{{ Form::open(array('url'=>'/feed')) }}
                    	<div class="form-group">
	                        <table class="table table-striped table-bordered table-hover">
	                            <tbody>
	                            	<tr>
						    			<td width="20%"><label>Req</label></td>
						    			<td width="80%">{{Form::text('req', 'trans', array('class'=>'form-control'))}} </td>
                                    </tr>
									<tr>
										<td><label>Buyer</label></td>
										<td>{{Form::text('buyer', 'JessicaWoo', array('class'=>'form-control'))}} </td>
									</tr>
                                    <tr>
                                        <td><label>Password</label></td>
                                        <td>{{Form::text('pass', '123456', array('class'=>'form-control'))}} </td>
                                    </tr>								
                                </tbody>	 		
						    </table>
						   {{ Form::button('Post to Feed: Trans', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}}
						   
						</div>
                        {{ Form::close() }}

                        {{ Form::open(array('url'=>'/checkout')) }}
                        <div class="form-group">
                            <table class="table table-striped table-bordered table-hover">
                                <tbody>
                                    <tr>
                                        <td width="20%"><label>Enc</label></td>
                                        <td width="80%">{{Form::text('enc', 'UTF-8', array('class'=>'form-control'))}} </td>
                                    </tr>
                                    <tr>
                                        <td><label>Buyer</label></td>
                                        <td>{{Form::text('user', 'eugene', array('class'=>'form-control'))}} </td>
                                    </tr>
                                    <tr>
                                        <td><label>Password</label></td>
                                        <td>{{Form::text('pass', '123456', array('class'=>'form-control'))}} </td>
                                    </tr> 
                                    <tr>
                                        <td><label>Deliver Name</label></td>
                                        <td>{{Form::text('delivername', 'eugene', array('class'=>'form-control'))}} </td>
                                    </tr>
                                    <tr>
                                        <td><label>Contact</label></td>
                                        <td>{{Form::text('delivercontactno', '123456', array('class'=>'form-control'))}} </td>
                                    </tr> 
                                    <tr>
                                        <td><label>Special Message</label></td>
                                        <td>{{Form::text('specialmsg', 'special', array('class'=>'form-control'))}} </td>
                                    </tr>
                                    <tr>
                                        <td><label>deliveradd1</label></td>
                                        <td>{{Form::text('deliveradd1', 'deliveradd1', array('class'=>'form-control'))}} </td>
                                    </tr> 
                                    <tr>
                                        <td><label>deliveradd2</label></td>
                                        <td>{{Form::text('deliveradd2', 'deliveradd2', array('class'=>'form-control'))}} </td>
                                    </tr>
                                    <tr>
                                        <td><label>deliverpostcode</label></td>
                                        <td>{{Form::text('deliverpostcode', 'deliverpostcode', array('class'=>'form-control'))}} </td>
                                    </tr> 
                                    <tr>
                                        <td><label>state(KL)</label></td>
                                        <td>{{Form::text('state', '4', array('class'=>'form-control'))}} </td>
                                    </tr>
                                    <tr>
                                        <td><label>delivercountry (M)</label></td>
                                        <td>{{Form::text('delivercountry', '1', array('class'=>'form-control'))}} </td>
                                    </tr> 
                                    <tr>
                                        <td><label>qrcode</label></td>
                                        <td>{{Form::text('qrcode[]', 'JC1076', array('class'=>'form-control'))}} </td>
                                    </tr>
                                    <tr>
                                        <td><label>priceopt</label></td>
                                        <td>{{Form::text('priceopt[]', '1321', array('class'=>'form-control'))}} </td>
                                    </tr> 
                                    <tr>
                                        <td><label>qty</label></td>
                                        <td>{{Form::text('qty[]', '1', array('class'=>'form-control'))}} </td>
                                    </tr>
                                    <tr>
                                        <td><label>qrcode</label></td>
                                        <td>{{Form::text('qrcode[]', 'JC118', array('class'=>'form-control'))}} </td>
                                    </tr>
                                    <tr>
                                        <td><label>priceopt</label></td>
                                        <td>{{Form::text('priceopt[]', '129', array('class'=>'form-control'))}} </td>
                                    </tr> 
                                    <tr>
                                        <td><label>qty</label></td>
                                        <td>{{Form::text('qty[]', '1', array('class'=>'form-control'))}} </td>
                                    </tr>
                                    <tr>
                                        <td><label>Device</label></td>
                                        <td>{{Form::text('devicetype', 'android', array('class'=>'form-control'))}} </td>
                                    </tr> 
                                </tbody>            
                            </table>
                           {{ Form::button('Post to Checkout', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}}                           
                        </div>
                        {{ Form::close() }}


                        {{ Form::open(array('url'=>'http://uat.eugene.jocom.my/payment/feed')) }}
                        <div class="form-group">
                            <table class="table table-striped table-bordered table-hover">
                                <tbody>
                                    <tr>
                                        <td width="20%"><label>Req</label></td>
                                        <td width="80%">{{Form::text('req', 'fees', array('class'=>'form-control'))}} </td>
                                    </tr>
                                                        
                                </tbody>            
                            </table>
                           {{ Form::button('Post to UAT Eugene Feed: Fees', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}}
                           
                        </div>
                        {{ Form::close() }}

                        {{ Form::open(array('url'=>'http://uat.eugene.jocom.my/payment/checkout')) }}
                        <div class="form-group">
                            <table class="table table-striped table-bordered table-hover">
                                <tbody>
                                    <tr>
                                        <td width="20%"><label>Enc</label></td>
                                        <td width="80%">{{Form::text('enc', 'UTF-8', array('class'=>'form-control'))}} </td>
                                    </tr>
                                    <tr>
                                        <td><label>Buyer</label></td>
                                        <td>{{Form::text('user', 'eugene', array('class'=>'form-control'))}} </td>
                                    </tr>
                                    <tr>
                                        <td><label>Password</label></td>
                                        <td>{{Form::text('pass', '123456', array('class'=>'form-control'))}} </td>
                                    </tr> 
                                    <tr>
                                        <td><label>Deliver Name</label></td>
                                        <td>{{Form::text('delivername', 'eugene', array('class'=>'form-control'))}} </td>
                                    </tr>
                                    <tr>
                                        <td><label>Contact</label></td>
                                        <td>{{Form::text('delivercontactno', '123456', array('class'=>'form-control'))}} </td>
                                    </tr> 
                                    <tr>
                                        <td><label>Special Message</label></td>
                                        <td>{{Form::text('specialmsg', 'special', array('class'=>'form-control'))}} </td>
                                    </tr>
                                    <tr>
                                        <td><label>deliveradd1</label></td>
                                        <td>{{Form::text('deliveradd1', 'deliveradd1', array('class'=>'form-control'))}} </td>
                                    </tr> 
                                    <tr>
                                        <td><label>deliveradd2</label></td>
                                        <td>{{Form::text('deliveradd2', 'deliveradd2', array('class'=>'form-control'))}} </td>
                                    </tr>
                                    <tr>
                                        <td><label>deliverpostcode</label></td>
                                        <td>{{Form::text('deliverpostcode', 'deliverpostcode', array('class'=>'form-control'))}} </td>
                                    </tr> 
                                    <tr>
                                        <td><label>state</label></td>
                                        <td>{{Form::text('state', '4', array('class'=>'form-control'))}} </td>
                                    </tr>
                                    <tr>
                                        <td><label>delivercountry</label></td>
                                        <td>{{Form::text('delivercountry', '1', array('class'=>'form-control'))}} </td>
                                    </tr> 
                                    <tr>
                                        <td><label>qrcode</label></td>
                                        <td>{{Form::text('qrcode[]', 'JC2146', array('class'=>'form-control'))}} </td>
                                    </tr>
                                    <tr>
                                        <td><label>priceopt</label></td>
                                        <td>{{Form::text('priceopt[]', '2432', array('class'=>'form-control'))}} </td>
                                    </tr> 
                                    <tr>
                                        <td><label>qty</label></td>
                                        <td>{{Form::text('qty[]', '1', array('class'=>'form-control'))}} </td>
                                    </tr>                                    
                                    <tr>
                                        <td><label>Device</label></td>
                                        <td>{{Form::text('devicetype', 'android', array('class'=>'form-control'))}} </td>
                                    </tr> 
                                </tbody>            
                            </table>
                           {{ Form::button('Post to UAT Eugene Checkout', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}}                           
                        </div>
                        {{ Form::close() }}

                    </div>                            
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    </div>

<form id="remove_frm" name="remove_frm" action="/transaction/remove" method="post">
  <input type="hidden" name="remove_transaction_id" id="remove_transaction_id" value="" />
</form>
 
@stop

