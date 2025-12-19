@extends('layouts.master')
@section('title') Logistic @stop
@section('content')

<style>
    
    .steps-bar {
 margin: 0 auto;
 width: 630px;
}
ul.steps-indicator {
 bottom: 0;
 display: flex;
 flex-direction: row;
 justify-content: center;
 left: 0;
 list-style: outside none none;
 margin: 0;
 right: 0;
 width: 100%;
 padding: 24px 0 10px;
}
ul.steps-indicator.steps-3:before {
 left: 16.6667%;
 right: 16.6667%;
}
ul.steps-indicator li {
 padding: 20px 0 0;
 margin: 0;
 position: relative;
 width: 20%;
 pointer-events: none;
}
ul.steps-indicator li:after {
 background-color: #a6a6a6;
 box-shadow: 0 0 0 6px #e7eaf1, 0 0 0 7px #fff;
 height: 10px;
 top: -12px;
 width: 10px;
 border-radius: 100%;
 content: "";
 left: calc(50% - 7px);
 line-height: 14px;
 position: absolute;
 text-align: center;
 transition: all 0.25s ease 0s;
 vertical-align: middle;
}
ul.steps-indicator li:not(:last-child):before {
 background-color: #a6a6a6;
 content: "";
 height: 2px;
 left: calc(50% + 7px);
 position: absolute;
 top: -7px;
 width: calc(100% - 14px);
}
ul.steps-indicator li div {
 align-items: center;
 display: flex;
 flex-direction: column;
}
ul.steps-indicator li div a {
 color: #a6a6a6;
 cursor: pointer;
 font-family: arial;
 font-size: 11px;
 font-weight: normal;
 line-height: 14px;
 text-align: center;
 text-decoration: none;
 text-transform: uppercase;
 transition: all 0.25s ease 0s;
}

ul.steps-indicator li.done:after,
ul.steps-indicator li.current:after,
ul.steps-indicator li.done:not(:last-child):before {
 background-color: #179a1d;
}

ul.steps-indicator li.current:after {
 background-color: #ff0808;
}
ul.steps-indicator li.current div a {
 color: #3e4350;
}

.modal {
  text-align: center;
  padding: 0!important;
}

.modal:before {
  content: '';
  display: inline-block;
  height: 100%;
  vertical-align: middle;
  margin-right: -4px;
}

.modal-dialog {
  display: inline-block;
  text-align: left;
  vertical-align: middle;
}
    
</style>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"> International Logistic</h1>
        </div>
    </div>
   
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12">
            <div>

            <!-- Nav tabs -->
            <ul class="nav nav-tabs " role="tablist">
                <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">New Delivery</a></li>
                <li role="presentation" ><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Delivery Confirmed</a></li>
                <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Ready for Shipping</a></li>
                <li role="presentation"><a href="#shipped" aria-controls="shipped" role="tab" data-toggle="tab">Shipped</a></li>
                <li role="presentation"><a href="#manifest" aria-controls="manifest" role="tab" data-toggle="tab"><i class="fa fa-file-text"></i> Manifest</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
               
                <div role="tabpanel" class="tab-pane active" id="home" style="padding-top:50px;">
                    <div class="row">
                        <div class="col-lg-12">    
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-hover" id="dataTables-pending-list">
                            <thead>
                                <tr>
                                    <th class="col-lg-1">
                                        <div class="btn-group">
                                          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></button>
                                          <ul class="dropdown-menu">
                                                <li><a href="#" id="list1_verify_all" ><i class="fa fa-check"></i> Verify All Marked</a></li>
                                                <li><a href="#" id="list1_mark_all"><i class="fa fa-check"></i> Mark All</a></li>
                                          </ul>
                                        </div>
                                    </th>
                                    <th class="col-lg-2">Tran. Date</th>
                                    <th class="col-lg-2">Recipient</th>   
                                    <th class="col-lg-4" style="text-align:center;">Status</th>
<!--                                    <th class="col-lg-1">Ref. Number</th>
                                    <th class="col-lg-1">Manifest ID</th>-->
                                    <!--<th class="col-lg-1" style="text-align:center;">Action</th>-->
                                </tr>
                            </thead>
                            <tbody>  </tbody>
                        </table>
                    </div>                            
               
     
        <!-- /.col-lg-12 -->
    </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane " id="profile" style="padding-top:50px;">
                    <div class="row">
                        <div class="col-lg-12">            
           
             
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-hover" id="dataTables-confirmed-list">
                            <thead>
                                <tr>
                                    <th class="col-lg-1">
                                        <div class="btn-group">
                                          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></button>
                                          <ul class="dropdown-menu">
                                                <li><a href="#" id="list1_verify_all" ><i class="fa fa-check"></i> Set all weighed for Shipping</a></li>
                                                <li><a href="#" id="list1_verify_all" ><i class="fa fa-check"></i> Set Selected weighed for Shipping</a></li>
                                          </ul>
                                        </div>
                                    </th>
                                    <th class="col-lg-2">Tran. Date</th>
                                    <th class="col-lg-2">Recipient</th>   
                                    <th class="col-lg-4" style="text-align:center;">Status</th>
<!--                                    <th class="col-lg-1">Ref. Number</th>
                                    <th class="col-lg-1">Manifest ID</th>-->
                                    <!--<th class="col-lg-1" style="text-align:center;">Action</th>-->
                                </tr>
                            </thead>
                            <tbody>  </tbody>
                        </table>
                    </div>                            
               
     
        <!-- /.col-lg-12 -->
    </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="messages" style="padding-top:50px;">
                    <div class="row">
                        <div class="col-lg-12">      
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-hover" id="dataTables-weighed-list">
                            <thead>
                                <tr>
                                    <th class="col-lg-1">
                                        <div class="btn-group">
                                          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></button>
                                          <ul class="dropdown-menu">
                                                <li><a href="#" id="list3_set_shipped" ><i class="fa fa-check"></i> Set as shipped</a></li>
                                                <li><a href="#" id="list3_mark_all" ><i class="fa fa-check"></i> Mark All</a></li>
                                          </ul>
                                        </div>
                                    </th>
                                    <th class="col-lg-2">Tran. Date</th>
                                    <th class="col-lg-3">Recipient</th>   
                                    <th class="col-lg-1">Ref. Number</th>
                                    <th class="col-lg-1">Manifest ID</th>
                                    <th class="col-lg-4" style="text-align:center;">Status</th>
                                    <!--<th class="col-lg-1" style="text-align:center;">Action</th>-->
                                </tr>
                            </thead>
                            <tbody>  </tbody>
                        </table>
                    </div>                            
               
     
        <!-- /.col-lg-12 -->
    </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="shipped" style="padding-top:50px;">
                    <div class="row">
                        <div class="col-lg-12">            
           
             
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table  table-hover" id="dataTables-shipped-list">
                            <thead>
                                <tr>
                                    <th class="col-lg-1">
                                        <div class="btn-group">
                                          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></button>
                                          <ul class="dropdown-menu">
                                                <li><a href="#" id="list4_set_delivered" ><i class="fa fa-check"></i> Set as delivered</a></li>
                                                <li><a href="#" id="list4_mark_all" ><i class="fa fa-check"></i> Mark All</a></li>
                                          </ul>
                                        </div>
                                    </th>
                                    <th class="col-lg-2">Tran. Date</th>
                                    <th class="col-lg-3">Recipient</th>   
                                    <th class="col-lg-1">Ref. Number</th>
                                    <th class="col-lg-1">Manifest ID</th>
                                    <th class="col-lg-4" style="text-align:center;">Status</th>
                                    <!--<th class="col-lg-1" style="text-align:center;">Action</th>-->
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>                            
               
     
        <!-- /.col-lg-12 -->
    </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="manifest" style="padding-top:50px;">
                    <div class="row">
                        <div class="col-lg-12">            
           
             
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table  table-hover" id="dataTables-manifest-list">
                            <thead>
                                <tr>
<!--                                    <th class="col-lg-1"></th>-->
                                    <th class="col-lg-2">Batch Datetime</th>
                                    <th class="col-lg-3">Manifest Number</th>   
                                    <th class="col-lg-3">Country</th>  
                                    <!--<th class="col-lg-1" style="text-align:center;">Action</th>-->
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>                            
               
     
        <!-- /.col-lg-12 -->
    </div>
                    </div>
                </div>
            </div>

        </div>
        </div>
    </div>
    <!-- MODAL -->
    <div class="modal fade " style="" id="weightModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><i class="fa fa-dashboard"></i> Enter Packaging Weight</h4>
          </div>
          <div class="modal-body">
                  <div class="form-group">
                    <label for="">Enter the weight (KG)</label>
                    <div class="input-group">
                        <input type="number" step="0.01" id="weight-total" class="form-control" placeholder="" aria-describedby="basic-addon1" style="height: 70px;font-weight: bold;font-size: 40px;">
                        <input type="hidden" id="weight-item" value="">
                        <span class="input-group-addon" id="basic-addon1">KG</span>
                    </div>
                    <small style="color:red;"><i class="fa fa-exclamation-triangle"></i> Once weight confirmed item will be lock for weigh. Weight must be enter in KG.</small>
                  </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="confirmedWeight">Confirmed Weight</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
@section('inputjs')

<script src="/js/asset/int_log.js" ></script>

@stop


@stop

