@extends('layouts.master')

@section('title') Product @stop

@section('content')
<div id="page-wrapper">
    <style>
        .p_img{display: block;}
        .p_img .wrap-img {position: relative;}
        .p_img .wrap-img .btn-clear{ position: absolute; top: 0px; right: 0px; display: none; }
        .p_img .wrap-img .btn-clear.active{display: block;}
    </style>
    <h1 class="page-header">Products</h1>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-pencil"></i> Edit Product Media</h3></div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(['url' => ['product/updatephoto', $product->product_id], 'method'=> 'PUT', 'class' => 'form-horizontal', 'files' => true]) }}
                            <div class="form-group @if ($errors->has('product_video')) has-error @endif">
                                {{ Form::label('qr_code', 'QR Code', ['class'=> 'col-lg-2 control-label']) }}
                                <div class="col-lg-5">
                                    {{ HTML::image('images/qrcode/'. ($product->qrcode_file ? $product->qrcode_file : 'noqrcode.png')) }}
                                    <p class="clearfix"></p>
                                    <h4><span class="label label-danger">{{ $product->qrcode }}</span></h4>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group">
                                {{ Form::label('id_number', 'Product ID', ['class' => 'col-lg-2 control-label']) }}
                                <div class="col-lg-3"><p class="form-control-static">{{ $product->product_id }}</p></div>
                            </div>

                            <hr />

                            <div class="form-group">
                                {{ Form::label('sku_number', 'SKU Number', ['class' => 'col-lg-2 control-label']) }}
                                <div class="col-lg-3"><p class="form-control-static">{{$product->sku}}</p></div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('product_name')) has-error @endif">
                                {{ Form::label('product_name', 'Product Name', ['class'=> 'col-lg-2 control-label']) }}
                                <div class="col-lg-3"><p class="form-control-static">{{ $product->name }}</p></div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('image')) has-error @endif">
                                {{ Form::label('image_path', 'Product Image', ['class'=> 'col-lg-2 control-label']) }}
                                <div class="col-lg-5">
                                    @for ($i = 1; $i <= 3; $i++)
                                        <label class="p_img">
                                            <img class="preview_img" data-bkpsrc="/images/data/thumbs/{{ $product->{'img_' . $i} != '' ? $product->{'img_' . $i} : 'noimage.png' }}">
                                            <div class="wrap-img"><input id="image{{ $i }}" type="file" name="newimage{{ $i }}" class="form-control" onchange="checkFileDetails(this)" accept=".png, .jpg, .jpeg"><div class="btn btn-default btn-clear{{ $product->{'img_' . $i} != '' ? ' active' : '' }}"><i class="glyphicon glyphicon-trash"></i> Remove</div></div>
                                            {{ ($product->{'img_' . $i} ? Form::hidden('image' . $i, $product->{'img_' . $i}, ['id' => 'currentimage' . $i]) : '') }}
                                        </label>
                                    @endfor
                                    {{ $errors->first('image', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <hr />
                            <div class="form-group @if ($errors->has('product_video')) has-error @endif">
                                {{ Form::label('product_video', 'Video URL', ['class'=> 'col-lg-2 control-label']) }}
                                <div class="col-lg-5">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-rss"></i></span>
                                        {{ Form::text('product_video', $product->vid_1, ['class' => 'form-control', 'placeholder' => 'Video URL']) }}
                                    </div>
                                    {{ $errors->first('product_video', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <hr />
                            <div class='form-group'>
                                {{ Form::label('updated_by', 'Last updated by', ['class' => 'col-lg-2 control-label']) }}
                                <div class="col-lg-3">
                                    {{ Form::text('updated_by', $product->modify_by, ['placeholder' => '', 'class' => 'form-control', 'disabled']) }} 
                                </div>
                            </div>

                            <div class='form-group'>
                                {{ Form::label('updated_at', 'Last updated at', ['class' => 'col-lg-2 control-label']) }}
                                <div class="col-lg-3">
                                    {{ Form::text('updated_at', $product->modify_date, ['placeholder' => '', 'class' => 'form-control', 'disabled']) }}
                                </div>
                            </div>

                            <hr />

                            <div class="form-group">
                                <div class="col-lg-10 col-lg-offset-2">
                                    {{ Form::reset('Reset', ['class' => 'btn btn-default', 'data-toggle' => 'tooltip']) }}
                                    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 1, 'OR'))
                                    <button id="buttonSave" type="submit" value="Save" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                                    @endif
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('inputjs')
    <script>
        // YH: check image size before submit
        // GET THE IMAGE WIDTH AND HEIGHT USING fileReader() API.
        // https://www.encodedna.com/javascript/check-image-width-height-and-type-before-uploading-using-javascript.htm
        // https://stackoverflow.com/questions/8903854/check-image-width-and-height-before-upload-with-javascript
        // https://stackoverflow.com/questions/12570834/get-file-size-image-width-and-height-before-upload
        function resetInput(target, preview_img, btn_clear){
            preview_img.src = preview_img.dataset.bkpsrc;
            btn_clear.className = 'btn btn-default btn-clear';

            var newInput = document.createElement("input");
            newInput.type = "file"; 
            newInput.id = target.id;
            newInput.name = target.name; 
            newInput.className = target.className; 
            newInput.style.cssText = target.style.cssText;
            newInput.setAttribute("onchange", 'checkFileDetails(this)');
            newInput.setAttribute("accept", '.png, .jpg, .jpeg');
            target.parentNode.replaceChild(newInput, target);
        }

        function readImageFile(target, file, fileExtension) {
            var reader = new FileReader(); // CREATE AN NEW INSTANCE.

            reader.onload = function (e) {
                var img = new Image();      
                img.src = e.target.result;

                img.onload = function () {
                    var w = this.width;
                    var h = this.height;
                    var size_KBs = Math.round((file.size / 1024));

                    var btn_clear = target.parentElement.parentElement.querySelector('.btn-clear');
                    var preview_img = target.parentElement.parentElement.querySelector('img.preview_img');
                    btn_clear.className = 'btn btn-default btn-clear active';
                    preview_img.src = URL.createObjectURL(file);
                    
                    if(w != 640 || h != 640 || size_KBs > 120){
                        bootbox.alert('Image file too big, Pls upload image file size under 120KB and image dimensions exactly 640px width and 640px height.');
                        resetInput(target, preview_img, btn_clear);
                        return false;
                    }

                    // https://stackoverflow.com/questions/829571/clearing-an-html-file-upload-field-via-javascript#:~:text=bind('focus'%2C%20function,%23select%2Dfile').
                    // oldInput.parentNode.replaceChild(oldInput.cloneNode(), oldInput);
                }
            };
            reader.readAsDataURL(file);
        }

        function checkFileDetails(target) {
            var fi = target;
            if (fi.files.length > 0) { // JS prefer do length check, 
                for (var i = 0; i <= fi.files.length - 1; i++) {
                    var fileName, fileExtension, fileSize, fileType, dateModified;

                    // FILE NAME AND EXTENSION.
                    fileName = fi.files.item(i).name;
                    fileExtension = fileName.replace(/^.*\./, '');

                    if (['png', 'jpg', 'jpeg'].includes(fileExtension)) { // IF IS IMAGE FILE DO CHECK
                       readImageFile(target, fi.files.item(i), fileExtension); // GET IMAGE INFO USING fileReader().
                    } else { // PRMOT ERROR
                        bootbox.alert('Only image file (.jpg, .jpeg, .png) allow to upload.');
                        resetInput(target, target.parentElement.parentElement.querySelector('img.preview_img'), target.parentElement.parentElement.querySelector('.btn-clear'));
                    }
                }
            }
        }

        $(document).on("click", ".btn-clear", function(e) {
            e.preventDefault();
            bootbox.confirm({
                title: "Remove Product Image",
                message: "Are you sure to remove these upload image?",
                callback: function(result) {
                    if (result === true) {
                        var target = e.target.parentElement.querySelectorAll('input[type="file"]')[0];
                        var parent_t = target.parentElement.parentElement;
                        var prev_img = parent_t.querySelector('img.preview_img');
                        var img_ref = parent_t.querySelector('input[type="hidden"]');
                        resetInput(target, prev_img, e.target);
                        prev_img.src = '/images/data/thumbs/noimage.png';
                        delete prev_img.dataset.bkpsrc;

                        var newInput = document.createElement("input");
                        newInput.type = "hidden";
                        newInput.id = img_ref.id;
                        newInput.name = img_ref.name;
                        parent_t.replaceChild(newInput, img_ref);
                    }
                }
            });
        });

        $('img.preview_img').each(function(index) {
            $(this).attr('src', $(this).data('bkpsrc'));
        });
    </script>
@stop

@section('script')
@stop