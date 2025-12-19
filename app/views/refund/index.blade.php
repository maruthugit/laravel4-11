@extends('layouts.master')

@section('title') Refund @stop

@section('content')

<style>
    .specialadjust.form-group{marginx; margin-bottom: 15px;}
    .specialadjust.form-group .max-width-adj{max-width: 500px;}
    .inlineblock-middle{display: inline-block; vertical-align: top;}
    #clone-base{display: none;}

    .title-cat {
        color: #2daf73;
    }

    .box-con {
        padding:20px;
        border: solid 1px #ddd;
        cursor: pointer;
    }

    .box-con:hover {
        padding:20px;
        background-color: #f3f3f3;
        border: solid 1px #ddd;
    }

    .child {
    display: inline-block;
    }
        
    a.overall, a.byDate, a.overallSendingPlaceRegion, a.overallstrAllProductSold {
        cursor: pointer;
        font-weight: bold;
        color: #989898;
    }
</style>

<div id="page-wrapper">
    <div class="row">
         <div class="col-lg-12">
            <h1 class="page-header">Refund Management<span class="pull-right">
                 {{-- <input type="hidden" name="import" id="import">
                        {{ Form::submit('Import CSV', ['class' => 'btn btn-large btn-primary btn-success']) }} --}}
            
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}refund"><i class="fa fa-refresh"></i></a>
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/refund/create"><i class="fa fa-plus"></i></a>
            </span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="panel panel-default">
    	@if (Session::has('message'))
            <div class="alert alert-danger">
                <i class="fa fa-exclamation"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">�</button>
            </div>
        @endif
        @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">�</button>
            </div>
        @endif
        
        <div class="panel-heading">
                <h3 class="panel-title">Refund Listing </h3>
            </div>
        <hr>
            <div class="panel-body text-center">
                    <div class="box-con" style="display: inline-block">
                        <div style="display: inline-block">
                            <div><i class="fa fa-money fa-2x"></i></div>
                        </div>
                        <div style="display: inline-block;padding-left: 10px;">
                            <div class="title-cat"><strong> Total Refund Montly </strong></div>
                            <div class="stat-total" id="total-montly"></div>
                        </div>
                    </div>
                    <div class="box-con" style="display: inline-block">
                        <div style="display: inline-block">
                            <div><i class="fa fa-money fa-2x"></i></div>
                        </div>
                        <div style="display: inline-block;padding-left: 10px;">
                            <div class="title-cat"><strong> Total Refund Weekly</strong></div>
                            <div class="stat-total" id="total-weekly"></div>
                        </div>
                    </div>

                    <div class="box-con" style="display: inline-block">
                        <div style="display: inline-block">
                            <div><i class="fa fa-retweet fa-2x"></i></div>
                        </div>
                        <div style="display: inline-block;padding-left: 10px;">
                            <div class="title-cat"><strong> Total Transaction Monthly</strong></div>
                            <div class="stat-total" id="total-trans-monthly"></div>
                        </div>
                    </div>
                    <div class="box-con" style="display: inline-block">
                        <div style="display: inline-block">
                            <div><i class="fa fa-retweet fa-2x"></i></div>
                        </div>
                        <div style="display: inline-block;padding-left: 10px;">
                            <div class="title-cat"><strong> Total Transaction Weekly</strong></div>
                            <div class="stat-total" id="total-trans-weekly"></div>
                        </div>
                    </div>
            </div>
        <hr>
        <?php if (!in_array(Session::get('username'), array('nadzri_account'), true )) {  ?>            
            {{ Form::open(array('url'=>'/refund/import', 'class' => 'form-horizontal', 'files' => true)) }}
            <div class="panel-body">
                <div class="col-lg-12 pull-right">
                    {{-- <div class='form-group'>
                        {{ Form::label('file', 'Upload file', array('class' => 'col-lg-2 control-label')) }} 
                        <div class="col-lg-6">
                            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                                <span class="input-group-addon btn btn-default btn-file">
                                    <span class="fileinput-new">Select file</span>
                                    <span class="fileinput-exists">Change</span>
                                    <input type="file" name="csv" id="csv">
                                </span>
                                <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                            </div>
                            <p class="text-danger">* Save the file as <b>"Windows Comma Seperated (.csv)"</b> before import the CSV.</p>
                        </div>
                    </div> 

                    <div class="form-group">
                        {{ Form::label('trans_id', 'Transaction ID', array('class'=> 'col-lg-2 control-label')) }}
                        <div class="col-lg-4">
                            {{ Form::text('trans_id', Input::old('trans_id'), array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                        </div>
                    </div>  --}}
                    {{-- <div class="specialadjust form-group">
                        {{ Form::label('attachment', 'Supporting Documents', array('class' => 'col-lg-2 control-label')) }} 
                        <div class="col-lg-6">
                            <div class="max-width-adj inlineblock-middle">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">Select file</span>
                                        <span class="fileinput-exists">Change</span>
                                        <input type="hidden"><input type="file" name="remark_doc[]">
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                </div>
                            </div>
                            <div class="input-group-btn inlineblock-middle">
                                <button class="btn btn-success" type="button"><i class="glyphicon glyphicon-plus"></i>Add</button>
                            </div>
                        </div>
                    </div>  --}}

                    {{-- <div class='form-group'>

                        <input type="hidden" name="import" id="import">
                        <div class="col-lg-10 col-lg-offset-2">
                            
                            <span class="pull-left">
                                <a  href="/refund/import/">
                                    {{ Form::submit('Import CSV', ['class' => 'btn btn-primary']) }}
                                </a>
                            </span>
                            </div>        
                        </div>
                    </div>   --}}
                </div>        
            </div>
            {{ Form::close() }}
        <?php }  ?>
        
        <div class="panel-body">
            <div class="table-responsive" style="overflow-x: none;" >
                {{ Form::open(['url' => 'refund/bulkconfirm']) }}
                <table class="table table-bordered table-striped table-hover" id="dataTables-refund">
                    <thead>
                        <tr>
                            <th class="col-sm-1">Refund ID</th>
                            <th class="text-center col-sm-1">Refund Date</th>
                            <th class="text-center col-sm-1">Transaction ID</th>
                            <th class="text-center col-sm-1">Created By</th>
                            <th class="text-center col-sm-1">Approved By</th>
                            <th class="text-center col-sm-1">Platform</th>
                            {{-- <th class="text-center col-sm-1">Transaction Amount ({{Config::get("constants.CURRENCY")}})</th>          --}}
                            <th class="text-center col-sm-1">Refund Amount ({{Config::get("constants.CURRENCY")}})</th>  
                            <th class="text-center col-sm-1">Credit Note</th>       
                            <th class="text-center col-sm-1">Status</th>         
                            <th class="text-center col-sm-1">Finance Confirmation</th>         
                            <th class="text-center col-sm-1">Action</th>
                        </tr>
                    </thead>
                </table>
                <button class="btn btn-primary" type="submit" id="confirmRefund"><i class="fa fa-check"></i> Confirm Refund</button>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <div id="clone-base">
        <div class="specialadjust form-group">
            <label class="col-lg-2 control-label" for="remark_doc[]"></label>
            <div class="col-lg-10">
                <div class="max-width-adj inlineblock-middle">
                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                        <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                        <span class="input-group-addon btn btn-default btn-file">
                            <span class="fileinput-new">Select file</span>
                            <span class="fileinput-exists">Change</span>
                            <input type="hidden"><input type="file" name="remark_doc[]">
                        </span>
                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                    </div>
                </div>
                <div class="input-group-btn inlineblock-middle">
                    <button class="btn btn-danger" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                </div>
            </div>
        </div>
    </div>
</div>


@stop
@section('script')

    <!-- Dashboard/total refund and transaction-->
    getProcessor();

    function getProcessor(){
        $.ajax({
            method: "POST",
            url: "/refund/dashboard",
            dataType:'json',
            success: function(data) {                
                $("#total-montly").html(data.totalRefundMonthly);
                $("#total-weekly").html(data.totalRefundWeekly);
                $("#total-trans-monthly").html(data.totalTransMonthly);
                $("#total-trans-weekly").html(data.totalTransWeekly);
            }
        })
    };
    <!-- Dashboard/total refund and transaction-->

    {{-- v1. uploading excel file and supporting docs --}}
    $('#execute').on('click', function(){ 

        var data = {
            job: $("#job").val(),
        };
        
        $.ajax({
            method: "POST",
            url: "/refund/import",
            datatype: "json",
            data: data,
            beforeSend: function(){
            },
            success: function(data) {
                alert('Succesfully Executed!');
                window.location.href = '/refund';
            }
        })

    });

    //Add supporting document
    $(document).on('click', ".btn-success", function(){ 
        var target = $(this).parents('.specialadjust.form-group');
        var html = $('#clone-base').html();
        target.after(html);
    });
    
    $(document).on('click', ".btn-danger", function(){ 
        var target = $(this).parents('.specialadjust.form-group');
        target.remove();
    });
    {{-- v1. uploading excel file and supporting docs --}}

    $('#dataTables-refund').dataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('refund/refunds') }}",
        "order" : [[0,'desc']],
        "columnsDef" : [{
            "targets" : "_all",
            "defaultContent" : ""
        }],
        "columns" : [
            { "data" : "0", "orderable" : false },
            { "data" : "1", "class" : "text-center", "orderable" : false, "searchable" : false },
            { "data" : "2", "class" : "text-center", "orderable" : false },
            { "data" : "3", "class" : "text-center", "orderable" : false },
            { "data" : "4", "class" : "text-center", "orderable" : false },
            { "data" : "5", "class" : "text-center", "orderable" : false },
            { "data" : "6", "class" : "text-right" },
            { "data" : "7", "class" : "text-center", "orderable" : false, "searchable" : false },
            { "data" : "8", "class" : "text-center" },
            { "data" : "9", "class" : "text-center" },
            { "data" : "10", "class" : "text-center" }
            
        ]
    });

    $(document).on("click", "#deleteRefund", function(e) {
        var link = $(this).attr("href");
        console.log(link);
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete this refund - ID: " + $(this).attr("data-value") + " ?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete refund id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 

    {{-- $(document).on("click", "#confirmRefund", function(e) {
        console.log("in");
        var link = $(this).attr("href");
        var allischecked = $('#createRefund input[type=checkbox]:checked').length

        e.preventDefault();
        bootbox.confirm({
            title: "Confirm Refund",
            message: "Are you sure to confirm the selection refunds?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete refund id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    });  --}}

@stop

<!-- 07/04/2022 - Add checkbox/bulk confirmation fo marcho/finance -->
<!-- 07/04/2022 - Add dashboard/total refund and transaction -->