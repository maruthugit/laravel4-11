@extends('layouts.master')

@section('title') Apps Version Logistic @stop

@section('content')

<div id="page-wrapper">
    @if ($errors->has())
        @foreach ($errors->all() as $error)
            <div class='bg-danger alert'>{{ $error }}</div>
        @endforeach
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Manage Apps Version Logistic</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            {{ $message }}
        </div>
    @endif

    

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Apps Version Setting</h2>
        </div>
        {{ Form::open(array('url' => array('sysadmin/appnewlogistic/update') , 'class' => 'form-horizontal', 'method' => 'POST', 'enctype' => 'multipart/form-data')) }}
        <div class="panel-body">
            
            <div class="row">
                <div class="col-md-12">
                    <div class="panel with-nav-tabs panel-default">
                        <div class="panel-heading">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tabandroid" data-toggle="tab"><i class="fa fa-android fa-2x" aria-hidden="true"></i> Android Version</a></li>
                                <li><a href="#tabiphone" data-toggle="tab"><i class="fa fa-mobile fa-2x" aria-hidden="true"></i> iPhone Version</a></li>
                                <li><a href="#tabipad" data-toggle="tab"><i class="fa fa-tablet fa-2x" aria-hidden="true"></i> iPad Version</a></li>
                                <li><a href="#tabtablet" data-toggle="tab"><i class="fa fa-tablet fa-2x" aria-hidden="true"></i> Tablet Version</a></li>
                            </ul>
                        </div>
                        <div class="panel-body">
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="tabandroid">
                                        <div class="col-lg-12">
                                            <div class='form-group'>
                                                {{ Form::label('', '', array('class' => 'col-lg-2 control-label' )) }}
                                                <div class="col-lg-3">
                                               <span class="text-success fa-2x">(Active Version : 
                                                     @foreach($appnew as $android)
                                                      @if ($android->apptype == 'android') {{$android->version}} @endif 
                                                     @endforeach
                                                )
                                                </span>
                                                </div>
                                            </div>
                                            <div class='form-group'>
                                                {{ Form::label('android', 'Android Version', array('class' => 'col-lg-2 control-label' )) }}
                                                <div class="col-lg-3">
                                                {{ Form::text('android', '', ['placeholder' => '', 'class' => 'form-control']) }}
                                                
                                                <input type="hidden" name="app_android" id="app_android" value="android">
                                                </div>
                                            </div>
                                            <div class='form-group'>
                                                {{ Form::label('Installer', 'Installer', array('class' => 'col-lg-2 control-label')) }}
                                                <div class="col-lg-6">
                                                <input type="file" name="installer_android" class="form-control" >
                                                </div>
                                            </div>
                                            <div class='form-group'>
                                                {{ Form::label('androidfeatures', 'New Features', array('class' => 'col-lg-2 control-label')) }}
                                                <div class="col-lg-6">
                                                {{ Form::textarea('androidfeatures', '', ['placeholder' => '', 'class' => 'form-control']) }}
                                                </div>
                                            </div>
                                           
                                        </div>

                                </div>
                                <div class="tab-pane fade" id="tabiphone">
                                        <div class="col-lg-12">
                                            <div class='form-group'>
                                                {{ Form::label('', '', array('class' => 'col-lg-2 control-label' )) }}
                                                <div class="col-lg-3">
                                               <span class="text-success fa-2x">(Active Version : 
                                                    @foreach($appnew as $iphone)
                                                      @if ($iphone->apptype == 'iphone') {{$iphone->version}} @endif 
                                                     @endforeach
                                                )
                                                </span>
                                                </div>
                                            </div>
                                            
                                            <div class='form-group'>
                                                {{ Form::label('iphone', 'IPhone Version', array('class' => 'col-lg-2 control-label')) }}
                                                <div class="col-lg-3">
                                                {{ Form::text('iphone', '', ['placeholder' => '', 'class' => 'form-control']) }}
                                                
                                                <input type="hidden" name="app_iphone" id="app_iphone" value="iphone">
                                                </div>
                                            </div>
                                            <div class='form-group'>
                                                {{ Form::label('Installer', 'Installer', array('class' => 'col-lg-2 control-label')) }}
                                                <div class="col-lg-6">
                                                <input type="file" name="installer_iphone" class="form-control" >
                                                </div>
                                            </div>
                                            <div class='form-group'>
                                                {{ Form::label('iphonefeatures', 'New Features', array('class' => 'col-lg-2 control-label')) }}
                                                <div class="col-lg-6">
                                                {{ Form::textarea('iphonefeatures', '', ['placeholder' => '', 'class' => 'form-control']) }}
                                                </div>
                                            </div>
                                           
                                        </div>

                                </div>
                                <div class="tab-pane fade" id="tabipad">
                                        <div class="col-lg-12">
                                            
                                            <div class='form-group'>
                                                {{ Form::label('', '', array('class' => 'col-lg-2 control-label' )) }}
                                                <div class="col-lg-3">
                                               <span class="text-success fa-2x">(Active Version : 
                                                    @foreach($appnew as $ipad)
                                                      @if ($ipad->apptype == 'ipad') {{$ipad->version}} @endif 
                                                     @endforeach
                                                )
                                                </span>
                                                </div>
                                            </div>
                                            
                                            <div class='form-group'>
                                                {{ Form::label('ipad', 'IPad Version', array('class' => 'col-lg-2 control-label')) }}
                                                <div class="col-lg-3">
                                                {{ Form::text('ipad', '', ['placeholder' => '', 'class' => 'form-control']) }}
                                              
                                                <input type="hidden" name="app_ipad" id="app_ipad" value="ipad">
                                                </div>
                                            </div>
                                            <div class='form-group'>
                                                {{ Form::label('Installer', 'Installer', array('class' => 'col-lg-2 control-label')) }}
                                                <div class="col-lg-6">
                                                <input type="file" name="installer_ipad" class="form-control" >
                                                </div>
                                            </div>
                                            <div class='form-group'>
                                                {{ Form::label('ipadfeatures', 'New Features', array('class' => 'col-lg-2 control-label')) }}
                                                <div class="col-lg-6">
                                                {{ Form::textarea('ipadfeatures', '', ['placeholder' => '', 'class' => 'form-control']) }}
                                                </div>
                                            </div>
                                           
                                        </div>
                                    

                                </div>
                                <div class="tab-pane fade" id="tabtablet">
                                        <div class="col-lg-12">
                                            <div class='form-group'>
                                                {{ Form::label('', '', array('class' => 'col-lg-2 control-label' )) }}
                                                <div class="col-lg-3">
                                               <span class="text-success fa-2x">(Active Version : 
                                                     @foreach($appnew as $tablet)
                                                      @if ($tablet->apptype == 'tablet') {{$tablet->version}} @endif 
                                                     @endforeach
                                                )
                                                </span>
                                                </div>
                                            </div>
                                            <div class='form-group'>
                                                {{ Form::label('tablet', 'Tablet Version', array('class' => 'col-lg-2 control-label')) }}
                                                <div class="col-lg-3">
                                                {{ Form::text('tablet', '', ['placeholder' => '', 'class' => 'form-control']) }}
                                            
                                                <input type="hidden" name="app_tablet" id="app_tablet" value="tablet">
                                                </div>
                                            </div>
                                            <div class='form-group'>
                                                {{ Form::label('Installer', 'Installer', array('class' => 'col-lg-2 control-label')) }}
                                                <div class="col-lg-6">
                                                <input type="file" name="installer_tablet" class="form-control" >
                                                </div>
                                            </div>
                                            <div class='form-group'>
                                                {{ Form::label('tabletfeatures', 'New Features', array('class' => 'col-lg-2 control-label')) }}
                                                <div class="col-lg-6">
                                                {{ Form::textarea('tabletfeatures', '', ['placeholder' => '', 'class' => 'form-control']) }}
                                                </div>
                                            </div>
                                            <!-- <div class='form-group'>
                                                {{ Form::label('tablet_updated_by', 'Last updated by', array('class' => 'col-lg-2 control-label')) }}
                                                <div class="col-lg-3">
                                                {{ Form::text('tablet_updated_by', '', ['placeholder' => '', 'class' => 'form-control', 'disabled']) }}
                                                </div>
                                            </div>
                                            <div class='form-group'>
                                                {{ Form::label('tablet_updated_at', 'Last updated at', array('class' => 'col-lg-2 control-label')) }}
                                                <div class="col-lg-3">
                                                {{ Form::text('tablet_updated_at', '', ['placeholder' => '', 'class' => 'form-control', 'disabled']) }}
                                                </div>
                                            </div> -->
                                           
                                        </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
        </div>
        @if(Permission::CheckAccessLevel(Session::get('role_id'), 10, 3, 'AND'))
        <div class='form-group'>
            <div class="col-lg-10 col-lg-offset-2">
            {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
            {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
            </div>
        </div>
        @endif
      </div>
     
    {{ Form::close() }}
    </div>

     <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Manage App History</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-apphistory">
                            <thead>
                                <tr>
                                    <th width="2%">ID</th>
                                    <th width="5%">App Type</th>
                                    <th width="5%">Version</th>
                                    <th width="5%">Installer</th>
                                    <th width="15%">Features</th>                                    
                                    <th width="5%" class="text-center">Update By</th>
                                    <th width="5%" class="text-center">Update At</th>
                                    <th width="2%" class="text-center">Status</th>
                                    <!-- <th width="2%" class="text-center" >Update At</th> -->
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>

    </div>

@stop
@section('inputjs')
<script>

    $(document).ready(function(){


            $('#dataTables-apphistory').dataTable({
                    "autoWidth" : false,
                    "processing": true,
                    "serverSide": true,
                    "ajax": "{{ URL::to('sysadmin/appnewlogistic/apphistory?'.http_build_query(Input::all())) }}",
                    "order" : [[ 0, 'desc' ]],
                    "columnDefs" : [{
                        "targets" : "_all",
                        "defaultContent" : "",
                        "orderable": false, "targets": [2,3],
                    }],
                    "columns" : [
                        { "data" : "0", "className" : "text-center" },
                        { "data" : "1" },
                        { "data" : "2" },
                        { "data" : "8" },
                        { "data" : "4" },
                        { "data" : "5", "className" : "text-center" },
                        { "data" : "6", "className" : "text-center" },
                        { "data" : "7", "className" : "text-center" }
                        // { data: function ( row, type, val, meta ) {
                        //     return '<button style="text-align:center;"  class="btn btn-default triggerAdd" data-transaction-id="'+row[0]+'" type="button" title="Add to Inventory">Add to Inventory <i class="fa fa-angle-double-right"></i> </button>';
                        //     }
                        // }
                    ]
                });

    });
</script>
@stop