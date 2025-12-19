@extends('layouts.master')

@section('title') Inventory @stop

@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"> Top Pending Products
            <span class="pull-right">
            <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}inventory"><i class="fa fa-refresh"></i></a>
            </span></h1>
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
                    <h3 class="panel-title"><i class="fa fa-list"></i>Top Pending Products</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                       <div class="col-lg-12">
                        {{ Form::open(array('url'=>'report/toptransactionsproducts', 'id' => 'add', 'class' => 'form-horizontal', 'files' => true)) }}
                      

                        <div class="form-group">
                            {{ Form::label('', 'Limits', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-2">
                               <select class="form-control" id="limit_display" name="limit_display">
                                   <option value="5000" selected="selected">Now - All pending</option>
                                   <option value="10">Top 10</option>
                                   <option value="20">Top 20</option>
                                   <option value="50">Top 50</option>
                                   <option value="100">Top 100</option>
                               </select>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('', '[Optional] Product Name', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-6">
                                {{Form::text('product_name', '', array('placeholder' => 'Product Name', 'class'=>'form-control'))}}
                            </div>
                        </div>
                        <div class="form-group @if ($errors->has('seller')) has-error @endif">
                        {{ Form::label('seller_name', '[Optional] Seller', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-3">
                                {{ Form::select('seller', ['all' => 'All'] + $sellersOptions, "all", ['class' => 'form-control']) }}
                                <p class="help-block" for="inputError">{{$errors->first('seller')}}</p>
                            </div>

                        </div>

                        <div class="form-group">
                        {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                             <div class="col-lg-10">                                
                                <button class="btn btn-primary" type="submit">Export</button>
                            </div>
                        </div>

                        <hr />

                        

                     
                    </div>                     
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    </div>


@stop
@section('inputjs')
<script>



$(document).ready(function() {
    
   $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    
});

</script>
@stop




