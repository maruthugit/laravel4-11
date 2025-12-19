@extends('layouts.master')

@section('title') Product Update @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Product Update - Export Product Details</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    @if ($message = Session::get('message'))
    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="fa fa-thumbs-up"></i> {{ $message }}
    </div>
    @endif
    {{ Form::open(['id'=>'export-form','role' => 'form', 'url' => '/product_update/export/'.$type, 'class' => 'form-horizontal']) }}
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-list"></i> Select Seller</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class="form-group @if ($errors->has('seller_name')) has-error @endif">
                    {{ Form::label('seller', 'Seller Name', array('class'=> 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        {{ Form::select('seller', array('' => '- - - Select Seller Name - - -') + $sellers, null, ['class' => 'form-control']) }}
                        {{ $errors->first('seller', '<p class="help-block">:message</p>') }}
                    </div>
                        
                {{ Form::button('Export CSV', ['class' => 'btn btn-large btn-primary btn-success','id'=>'submit-export-form']) }}
                
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-lg-12">
             <a id="execute" name="execute" class="btn btn-large btn-primary pull-right">Execute</a>
        </div>
    </div>    
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-list"></i> Process Listing</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
               <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="col-sm-1 text-center">ID</th>
                            <th class="hidden-xs hidden-sm col-sm-3 text-center">Seller Name</th>
                            <th class="hidden-xs hidden-sm col-sm-2 text-center">Requested Date Time</th>
                            <th class="hidden-xs hidden-sm col-sm-1 text-center">Status</th>
                            <th class="hidden-xs hidden-sm col-sm-3 text-center">File name</th>
                        </tr>
                    </thead>
                    <tbody id="ptb">
                        @if(count($jobs) > 0)
                        @foreach ($jobs as $job)
                            <tr class="jobOption">
                                <input type="hidden" value="{{$job->id}}" name="job" id="job[]">
                                <td class="col-sm-1 text-center"> {{ $job->id }} </td>
                                <td> {{ $job->company_name }} </td>
                                <td class="text-center"> {{ $job->request_at }} </td>
                                <td class="text-center"> 
                                <?php
                                    $status = $job->status;
                                    switch ($status) {
                                        case '0'    : echo "<p class=\"text-danger\">In Queue</p>";
                                            break;

                                        case '1'    : echo '<p class="text-danger">In Process</p>';
                                            break;

                                        case '2'    : echo '<p class="text-success">Complete</p>';
                                            break;
                                    }
                                ?>
                                </td>
                                <td>
                                    @if ($job->out_file)
                                        {{ HTML::link('/product_update/files?out='.$job->out_file, $job->out_file, array('target'=>'_blank')) }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
    $("#submit-export-form").click(function(){
       
       if($("#seller").val() === ""){
           alert("Please choose seller name")
       }else{
           $("#export-form").submit();
       }
       
       
    });
    $('#execute').on('click', function(){ 

        var data = {
            job: $("#job").val(),

        };
        
        $.ajax({
            method: "POST",
            url: "/product_update/exportjob",
            datatype: "json",
            data: data,
            beforeSend: function(){
            },
            success: function(data) {
                 alert('Succesfully Executed!');
                 window.location.href = '/product_update/export';
            }
       })

    });

@stop