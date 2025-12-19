@extends('layouts.master')

@section('title') View Details @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Help Center Management</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> View Details</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url' => 'helpcenter/update', 'class' => 'form-horizontal','files'=>true)) }}
                            <input id="{{$data->id}}" type="hidden" name="id" value="{{$data->id}}">
                            <div class="form-group @if ($errors->has('username')) has-error @endif">
                                {{ Form::label('username', 'Username *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    {{ Form::text('username',$data->username, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                                    {{ $errors->first('user', '<p class="help-block">:message</p>') }}
                                </div>
                
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('order_id')) has-error @endif">
                                {{ Form::label('order_id', 'Order ID *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    {{ Form::text('order_id',$data->order_id, array('class'=> 'form-control', 'autofocus' => 'autofocus','readonly' => 'readonly')) }}
                                    {{ $errors->first('order_id', '<p class="help-block">:message</p>') }}
                            
                                </div>
                            </div>  

                            <hr />
                           <div class="form-group @if ($errors->has('query_topic')) has-error @endif">
                                {{ Form::label('query_topic', 'Topic *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    <input type="text" name="query_topic" class='form-control' autofocus='autofocus' readonly='readonly' value="{{$data->query_topic}}">
                                        
                                        
                                    {{ $errors->first('query_topic', '<p class="help-block">:message</p>') }}
                            
                                </div>
                            </div>  

                            <hr />
                            <div class="form-group @if ($errors->has('description')) has-error @endif">
                            {{ Form::label('description', 'Description *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-10">
                                    {{ Form::textarea('description', $data->description, array('class'=> 'form-control', 'autofocus' => 'autofocus','readonly' => 'readonly')) }}
                                    {{ $errors->first('description', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('email')) has-error @endif">
                                {{ Form::label('email', 'Email ID *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    {{ Form::email('email',$data->email, array('class'=> 'form-control', 'autofocus' => 'autofocus','readonly' => 'readonly')) }}
                                    {{ $errors->first('email', '<p class="help-block">:message</p>') }}
                            
                                </div>
                            </div>  


                            <hr />
                            <div class="form-group @if ($errors->has('contact_number')) has-error @endif">
                                {{ Form::label('contact_number', 'Contact Number*', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    {{ Form::text('contact_number',$data->contact_number, array('class'=> 'form-control', 'autofocus' => 'autofocus','readonly' => 'readonly')) }}
                                    {{ $errors->first('contact_number', '<p class="help-block">:message</p>') }}
                            
                                </div>
                            </div>  


                            <hr />
                            <div class="form-group @if ($errors->has('image')) has-error @endif">
                                {{ Form::label('image', 'Image Upload', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    <!--<input type="file" class="form-control" name="image" onchange="tempload(event)">-->
                                    {{ $errors->first('image', '<p class="help-block">:message</p>') }}
                            
                                </div>
                                
                            </div>  
<div>                             @if($data->image_attached!="")
                                 <img src="../../public/{{$data->image_attached}}" style="height: 200px;width: 200px;" id="tempimg">
                                 @endif
                              </div>

                            <hr />
                        

                            <!--<div class="form-group">-->
                            <!--    <div class="col-lg-10 col-lg-offset-2">-->
                            <!--        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 3, 5, 'AND'))-->
                            <!--        {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}-->
                            <!--        <button type="submit" value="Save" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>-->
                            <!--        @endif-->
                            <!--    </div>-->
                            <!--</div>-->
                        {{ Form::close() }}
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->  

<script>
    var tempload=function(event){
            var output = document.getElementById('tempimg');
            output.src = URL.createObjectURL(event.target.files[0]);
       output.onload = function() {
      URL.revokeObjectURL(output.src)
    }
    }
</script>    
@stop


@section('script')
    
    $('#datepicker').datepicker({ dateFormat: "yy-mm-dd" }).val();

    $('#addProdBtn').colorbox({
        iframe:true, width:"80%", height:"80%",
        onClosed: function() {
            localStorage.clear();
        }
    });

    $('#selectUserBtn').colorbox({
        iframe:true, width:"80%", height:"80%",
        onClosed: function() {
            localStorage.clear();
        }
    });
@stop