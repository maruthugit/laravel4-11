@extends('layouts.master')

@section('title') Package @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Product Package</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Edit Package Media</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url' => array('product/package/updatephoto', $package->id), 'method'=> 'PUT', 'class' => 'form-horizontal', 'files' => true)) }}
                            <div class="form-group @if ($errors->has('product_video')) has-error @endif">
                                {{ Form::label('qr_code', 'QR Code', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-5">
                                    @if ($package->qrcode_file)
                                    {{ HTML::image('images/qrcode/'.$package->qrcode_file)}}
                                    @else
                                    {{ HTML::image('images/qrcode/noqrcode.png')}}
                                    @endif
                                    <p class="clearfix"></p>
                                    <h4><span class="label label-danger">{{ $package->qrcode }}</span></h4>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group">
                            {{ Form::label('id_number', 'ID Number', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <p class="form-control-static">{{$package->id}}</p>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group">
                            {{ Form::label('sku_number', 'SKU Number', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <p class="form-control-static">{{$package->sku}}</p>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('prod_name')) has-error @endif">
                            {{ Form::label('prod_name', 'Package Name', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <p class="form-control-static">{{$package->name}}</p>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('newimage1')) has-error @endif">
                                {{ Form::label('image_path', 'Package Image', array('class'=> 'col-lg-2 control-label' )) }}
                                <div class="col-lg-5">
                                    @if ($package->img_1)
                                    {{ Form::hidden('image1', $package->img_1, array('id' => 'currentimage1'))}}
                                    @endif
                                    @if ($package->img_2)
                                    {{ Form::hidden('image2', $package->img_2, array('id' => 'currentimage2'))}}
                                    @endif
                                    @if ($package->img_3)
                                    {{ Form::hidden('image3', $package->img_3, array('id' => 'currentimage3'))}}
                                    @endif
                                    <input id="image1" type="file" name="newimage1" class="form-control"><br />
                                    <input id="image2" type="file" name="newimage2" class="form-control"><br />
                                    <input id="image3" type="file" name="newimage3" class="form-control">
                                    {{ $errors->first('newimage1', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <hr />
                            <div class="form-group @if ($errors->has('product_video')) has-error @endif">
                                {{ Form::label('product_video', 'Video URL', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-5">
                                    <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-rss"></i></span>
                                            {{ Form::text('product_video', $package->vid_1, array('class'=> 'form-control', 'placeholder' => 'Video URL')) }}
                                        </div>
                                    {{ $errors->first('product_video', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            
                            <hr />

                            <div class="form-group">
                                <div class="col-lg-10 col-lg-offset-2">
                                    <!-- <a class="btn btn-default" href="/product/package"><i class="fa fa-reply"></i> Cancel</a> -->
                                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 1, 'OR'))
                                    <button id="buttonSave" type="submit" value="Save" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                                    @endif
                                </div>
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
    <!-- /.row -->  
@stop

@section('inputjs')
<!-- File Input JavaScript -->
<script src="../../../js/fileinput.min.js"></script>    
@stop

@section('script')
    $("#image1").fileinput({
        initialPreview: [
            <?php $noimg = 'noimage.png'; ?>
            '<img src="/images/data/thumbs/<?php echo ($package->img_1 != '' ? "$package->img_1" : "$noimg") ?>" class="file-preview-image">'
        ],
        allowedFileExtensions: ["jpg", "png"],
        maxFileSize: 1024,
        showUpload: false
    });

    $("#image2").fileinput({
        <?php if($package->img_2 != '') { ?>
        initialPreview: [
            '<img src="/images/data/thumbs/<?php echo $package->img_2 ?>" class="file-preview-image">'
        ], <?php } ?>
        allowedFileExtensions: ["jpg", "png"],
        maxFileSize: 1024,
        showUpload: false
    });

    $("#image3").fileinput({
        <?php if($package->img_3 != '') { ?>
        initialPreview: [
            '<img src="/images/data/thumbs/<?php echo $package->img_3 ?>" class="file-preview-image">'
        ], <?php } ?>
        allowedFileExtensions: ["jpg", "png"],
        maxFileSize: 1024,
        showUpload: false
    });

    $('#image1, #image2, #image3').on('fileerror', function(event, file, previewId, index, reader) {        
        $('#buttonSave').attr('disabled', 'disabled')
    });

    $('#image1, #image2, #image3').on('filebrowse', function(event) {
        $('#buttonSave').removeAttr('disabled');
    });

    $('#image1').on('filecleared', function(event) {
        $("#currentimage1").val("");
    });

    $('#image2').on('filecleared', function(event) {
        $("#currentimage2").val("");
    });

    $('#image3').on('filecleared', function(event) {
        $("#currentimage3").val("");
    });
@stop