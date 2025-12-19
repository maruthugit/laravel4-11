@extends('layouts.master')

@section('title') JocomMy Template @stop

@section('content')

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<style>
    .table-responsive2{
        max-width: 80%;
    }

    .nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus{
        border-bottom-color: #ddd !important; 
    }

    .borderless > tbody > tr > td,
.borderless > tbody > tr > th,
.borderless > tfoot > tr > td,
.borderless > tfoot > tr > th,
.borderless > thead > tr > td,
.borderless > thead > tr > th {
    border: none;
}
</style>

<div id="page-wrapper">
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

            <h1 class="page-header">JocomMy Template Management
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="/jocommy/template"><i class="fa fa-refresh"></i></a>
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="/jocommy/layout"><i class="fa fa-plus"></i></a>
                </span>
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(array('url' => 'jocommy/templateupdate/' , 'class' => 'form-horizontal', 'files' => true, 'method'=>'POST', 'enctype' => "multipart/form-data")) }}

    <div class="row" style="margin-top: 15px;">
        <div class="col-lg-12">
            <ul class="nav nav-tabs" style="background: rgba(221, 221, 221, 0.38);border: 1px solid #ddd;border-top-right-radius: 9px;border-top-left-radius: 9px;">
                <li class="active"><a data-toggle="tab" href="#home"><center><i class="fa fa-picture-o fa-lg fa-lg" aria-hidden="true"></i></center>JocomMY</a></li>
                <li><a data-toggle="tab" href="#menu1"><center><i class="fa fa-picture-o fa-lg fa-lg"></i></center>Festive Combo</a></li>
                <li><a data-toggle="tab" href="#menu2"><center><i class="fa fa-picture-o fa-lg fa-lg"></i></center>Cross Border</a></li>
                <li><a data-toggle="tab" href="#menu3"><center><i class="fa fa-picture-o fa-lg fa-lg"></i></center>Jocom Voucher</a></li>
            </ul> 
            <div class="tab-content" style="padding-left: 5px;padding-right: 5px;">
                <div id="home" class="tab-pane fade in active">
                    <br>
                    <!-- START HQ-->
                    <?php 
                        foreach ($first as $key => $value) {
                            $active_status = $value->active_status;
                    ?>
                    <div class="panel panel-default test" id="B001_hq_temp">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-pencil"></i> Jocom Main Slider
                                <span class="pull-right"><input class="form-control" type="number" min="1" value="{{$value->seq}}" name="seq1[]" style="width: 70px;  margin-top: -8px;"></span>
                            </h2>
                        </div>
                        <div class="panel-body ">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <div class="col-lg-2 pull-right">
                                        <input type="hidden" name="bannerID1[]" class="form-control input-sm" value="{{$value->banner_id}}">
                                        <select class="form-control" name="status1[]">
                                            <option></option>
                                            <?php foreach ($status as $key => $val) { ?>
                                                <option value="<?php echo $key; ?>" <?php if ($key == $active_status) echo 'selected="selected"'?> ><?php echo $val;?></option>
                                            <?php } ?>
                                        </select> 
                                        <a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="{{$value->banner_id}}" href="/jocommy/templatedelete/{{$value->banner_id}}"><i class="fa fa-times"></i></a>
                                    </div>
                                </div>
                                <center>
                                    <table class="table table-responsive table-responsive2 borderless">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <h2> Header </h2>
                                                    <textarea name="heading1[]" class="form-control">{{$value->heading}}</textarea>
                                                    <br>
                                                    <h3> Sub-Header </h3>
                                                    <textarea name="sub_heading1[]" class="form-control">{{$value->sub_heading}}</textarea>
                                                </td>
                                                <td >
                                                    <img src='/images/jocommy/{{$value->file_name}}' alt='' class='img-responsive center-block' style="height: 55%;">
                                                    <br>
                                                    <input type='file' name='image1[]' class='form-control center-block' style="width: 70%;" >
                                                    <input type='hidden' name='id1[]' class='form-control input-sm' value='{{$value->id}}' >
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </center>                              
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <!-- END HQ-->
                <div id="menu1" class="tab-pane fade">
                    <br>
                    <!-- START HQ-->
                    <?php 
                        foreach ($second as $key => $value) {
                            $active_status = $value->active_status;
                    ?>
                    <div class="panel panel-default test" id="B001_hq_temp">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-pencil"></i> Festive Combo Slider
                                <span class="pull-right"><input class="form-control" type="number" min="1" value="{{$value->seq}}" name="seq2[]" style="width: 70px;  margin-top: -8px;"></span>
                            </h2>
                        </div>
                        <div class="panel-body " >
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <div class="col-lg-2 pull-right">
                                        <input type="hidden" name="bannerID2[]" class="form-control input-sm" value="{{$value->banner_id}}">
                                        <select class="form-control" name="status2[]">
                                            <option></option>
                                            <?php foreach ($status as $key => $val) { ?>
                                                <option value="<?php echo $key; ?>" <?php if ($key == $active_status) echo 'selected="selected"'?> ><?php echo $val;?></option>
                                            <?php } ?>
                                        </select> 
                                        <a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="{{$value->banner_id}}" href="/jocommy/templatedelete/{{$value->banner_id}}"><i class="fa fa-times"></i></a>
                                    </div>
                                </div>
                                <center>
                                    <table class="table table-responsive borderless">
                                        <tbody>
                                            <tr>
                                                <td >
                                                    <img src='/images/jocommy/{{$value->file_name}}' alt='' class='img-responsive center-block'>
                                                    <br>
                                                    <input type='file' name='image2[]' class='form-control center-block' style="width: 70%;" >
                                                    <input type='hidden' name='id2[]' class='form-control input-sm' value='{{$value->id}}' >
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </center>                              
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <div id="menu2" class="tab-pane fade">
                    <br>
                    <!-- START HQ-->
                    <?php 
                        foreach ($third as $key => $value) {
                            $active_status = $value->active_status;
                    ?>
                    <div class="panel panel-default test" id="B001_hq_temp">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-pencil"></i> Cross Border Slider
                                <span class="pull-right"><input class="form-control" type="number" min="1" value="{{$value->seq}}" name="seq3[]" style="width: 70px;  margin-top: -8px;"></span>
                            </h2>
                        </div>
                        <div class="panel-body " >
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <div class="col-lg-2 pull-right">
                                        <input type="hidden" name="bannerID3[]" class="form-control input-sm" value="{{$value->banner_id}}">
                                        <select class="form-control" name="status3[]">
                                            <option></option>
                                            <?php foreach ($status as $key => $val) { ?>
                                                <option value="<?php echo $key; ?>" <?php if ($key == $active_status) echo 'selected="selected"'?> ><?php echo $val;?></option>
                                            <?php } ?>
                                        </select> 
                                        <a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="{{$value->banner_id}}" href="/jocommy/templatedelete/{{$value->banner_id}}"><i class="fa fa-times"></i></a>
                                    </div>
                                </div>
                                <center>
                                    <table class="table table-responsive borderless">
                                        <tbody>
                                            <tr>
                                                <td >
                                                    <img src='/images/jocommy/{{$value->file_name}}' alt='' class='img-responsive center-block'>
                                                    <br>
                                                    <input type='file' name='image3[]' class='form-control center-block' style="width: 70%;" >
                                                    <input type='hidden' name='id3[]' class='form-control input-sm' value='{{$value->id}}' >
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </center>                              
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <div id="menu3" class="tab-pane fade">
                    <br>
                    <!-- START HQ-->
                    <?php 
                        foreach ($four as $key => $value) {
                            $active_status = $value->active_status;
                    ?>
                    <div class="panel panel-default test" id="B001_hq_temp">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-pencil"></i> Jocom Voucher
                                <span class="pull-right"><input class="form-control" type="number" min="1" value="{{$value->seq}}" name="seq4[]" style="width: 70px;  margin-top: -8px;"></span>
                            </h2>
                        </div>
                        <div class="panel-body " >
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <div class="col-lg-2 pull-right">
                                        <input type="hidden" name="bannerID4[]" class="form-control input-sm" value="{{$value->banner_id}}">
                                        <select class="form-control" name="status4[]">
                                            <option></option>
                                            <?php foreach ($status as $key => $val) { ?>
                                                <option value="<?php echo $key; ?>" <?php if ($key == $active_status) echo 'selected="selected"'?> ><?php echo $val;?></option>
                                            <?php } ?>
                                        </select> 
                                        <a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="{{$value->banner_id}}" href="/jocommy/templatedelete/{{$value->banner_id}}"><i class="fa fa-times"></i></a>
                                    </div>
                                </div>
                                <center>
                                    <table class="table table-responsive borderless">
                                        <tbody>
                                            <tr>
                                                <td >
                                                    <img src='/images/jocommy/{{$value->file_name}}' alt='' class='img-responsive center-block'>
                                                    <br>
                                                    <input type='file' name='image4[]' class='form-control center-block' style="width: 70%;" >
                                                    <input type='hidden' name='id4[]' class='form-control input-sm' value='{{$value->id}}' >
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </center>                              
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <br>
    <div class='form-group'>
        <div class="col-lg-10">
            {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
            {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
        </div>
    </div>
    <br>
    {{ Form::close() }}

</div>

<script>
    $('.test').on('click', '.remove', function() {
    $('.remove').closest('.test').find('.element').not(':first').last().remove();
    });
    $('.test').on('click', '.clone', function() {
        $('.clone').closest('.test').find('.element').first().clone().find("input:text").val("").end().appendTo('.results');
    });

    $(document).on("click", "#deleteBan", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete this banner template - " + $(this).attr("data-value") + " ?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete banner id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 
</script>
@stop
