@extends('layouts.master')

@section('title') Product @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Products</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Edit Product Media</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url' => array('product/updatephoto', $product->product_id), 'method'=> 'PUT', 'class' => 'form-horizontal', 'files' => true)) }}
                            <div class="form-group @if ($errors->has('product_video')) has-error @endif">
                                {{ Form::label('qr_code', 'QR Code', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-5">
                                    @if ($product->qrcode_file)
                                    {{ HTML::image('images/qrcode/'.$product->qrcode_file)}}
                                    @else
                                    {{ HTML::image('images/qrcode/noqrcode.png')}}
                                    @endif
                                    <p class="clearfix"></p>
                                    <h4><span class="label label-danger">{{ $product->qrcode }}</span></h4>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group">
                            {{ Form::label('id_number', 'Product ID', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <p class="form-control-static">{{$product->product_id}}</p>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group">
                            {{ Form::label('sku_number', 'SKU Number', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <p class="form-control-static">{{$product->sku}}</p>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('product_name')) has-error @endif">
                            {{ Form::label('product_name', 'Product Name', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <p class="form-control-static">{{$product->name}}</p>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('image')) has-error @endif">
                                {{ Form::label('image_path', 'Product Image', array('class'=> 'col-lg-2 control-label' )) }}
                                <div class="col-lg-5">
                                    @if ($product->img_1)
                                    {{ Form::hidden('image1', $product->img_1, array('id' => 'currentimage1'))}}
                                    @endif
                                    @if ($product->img_2)
                                    {{ Form::hidden('image2', $product->img_2, array('id' => 'currentimage2'))}}
                                    @endif
                                    @if ($product->img_3)
                                    {{ Form::hidden('image3', $product->img_3, array('id' => 'currentimage3'))}}
                                    @endif
                                    <input id="image1" type="file" name="newimage1" class="form-control"><br />
                                    <input id="image2" type="file" name="newimage2" class="form-control"><br />
                                    <input id="image3" type="file" name="newimage3" class="form-control">
                                    {{ $errors->first('image', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <hr />
                            <div class="form-group @if ($errors->has('product_video')) has-error @endif">
                                {{ Form::label('product_video', 'Video URL', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-5">
                                    <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-rss"></i></span>
                                            {{ Form::text('product_video', $product->vid_1, array('class'=> 'form-control', 'placeholder' => 'Video URL')) }}
                                        </div>
                                    {{ $errors->first('product_video', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <hr />
                            <div class='form-group'>

                                {{ Form::label('updated_by', 'Last updated by', array('class' => 'col-lg-2 control-label')) }}

                                <div class="col-lg-3">

                                 <!-- <p class="form-control-static">{{$product->modify_by}}</p> -->

                               {{ Form::text('updated_by', $product->modify_by, ['placeholder' => '', 'class' => 'form-control', 'disabled']) }} 

                                </div>

                            </div>

                            <div class='form-group'>

                                {{ Form::label('updated_at', 'Last updated at', array('class' => 'col-lg-2 control-label')) }}

                                <div class="col-lg-3">

                                 <!-- <p class="form-control-static">{{$product->modify_date}}</p> -->

                                {{ Form::text('updated_at', $product->modify_date, ['placeholder' => '', 'class' => 'form-control', 'disabled']) }} 

                                </div>

                            </div>

                            <hr />

                            <div class="form-group">
                                <div class="col-lg-10 col-lg-offset-2">
                                    <!-- <a class="btn btn-default" href="/product/"><i class="fa fa-reply"></i> Cancel</a> -->
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
<script src="../../js/fileinput-v5.min.js"></script> 
@stop

@section('script')
    $("#image1").fileinput({
        <?php if ($product->img_1 != '') { ?>
        initialPreview: [
            '<img src="/images/data/thumbs/<?php echo $product->img_1; ?>" class="file-preview-image kv-preview-data">'
        ],
        <?php } ?>
        allowedFileExtensions: ['jpg', 'png'],
        maxFileCount: 1,
        showUpload: false,
        showRemove: false,
        showCancel: false,
        maxFileSize: 1024,
        layoutTemplates: {
            progress: '',
            actions: '<div class="file-actions">\n' +
        '    <div class="file-footer-buttons">\n' +
        '        {zoom}' +
        '    </div>\n' +
        '    <div class="clearfix"></div>\n' +
        '</div>',
        },
    });

    $("#image2").fileinput({
        <?php if ($product->img_2 != '') { ?>
        initialPreview: [
            '<img src="/images/data/thumbs/<?php echo $product->img_2; ?>" class="file-preview-image kv-preview-data">'
        ],
        <?php } ?>
        allowedFileExtensions: ['jpg', 'png'],
        maxFileCount: 1,
        showUpload: false,
        showRemove: false,
        showCancel: false,
        maxFileSize: 1024,
        layoutTemplates: {
            progress: '',
            actions: '<div class="file-actions">\n' +
        '    <div class="file-footer-buttons">\n' +
        '        {zoom}' +
        '    </div>\n' +
        '    <div class="clearfix"></div>\n' +
        '</div>',
        },
    });

    $("#image3").fileinput({
        <?php if ($product->img_3 != '') { ?>
        initialPreview: [
            '<img src="/images/data/thumbs/<?php echo $product->img_3; ?>" class="file-preview-image kv-preview-data">'
        ],
        <?php } ?>
        allowedFileExtensions: ['jpg', 'png'],
        maxFileCount: 1,
        showUpload: false,
        showRemove: false,
        showCancel: false,
        maxFileSize: 1024,
        layoutTemplates: {
            progress: '',
            actions: '<div class="file-actions">\n' +
        '    <div class="file-footer-buttons">\n' +
        '        {zoom}' +
        '    </div>\n' +
        '    <div class="clearfix"></div>\n' +
        '</div>',
        },
    });

    $('#image1, #image2, #image3').on('fileerror', function(event, file, previewId, index, reader) {        
        $('#buttonSave').attr('disabled', 'disabled')
    });

    $('#image1, #image2, #image3').on('filebrowse', function(event) {
        $('#buttonSave').removeAttr('disabled');
    });
    
    $('#image1, #image2, #image3').on('fileloaded', function(event) {
        $('#buttonSave').removeAttr('disabled');
    });

    $('#image1').on('filecleared', function(event) {
        $("#currentimage1").val("");
        $('#buttonSave').removeAttr('disabled');
    });

    $('#image2').on('filecleared', function(event) {
        $("#currentimage2").val("");
        $('#buttonSave').removeAttr('disabled');
    });

    $('#image3').on('filecleared', function(event) {
        $("#currentimage3").val("");
        $('#buttonSave').removeAttr('disabled');
    });
@stop