@extends('layouts.master')

@section('title') Comment @stop

@section('content')
<style>
    .wrapper-upimage{font-size: 0px;}
    .wrapper-upimage > *:first-child{margin-left: 0px;}
    .wrapper-upimage > *:last-child{margin-right: 0px;}
    .up-img{height: 100px; width: 100px; border: 1px solid #ccc; border-radius: 10px; text-align: center; color: #ccc; cursor: pointer; margin: 0px 5px;}
    .up-img .fa-plus{font-size: 30px; margin-top: 35px;}
    .up-img input[type="file"]{width: 0px; height: 0px; opacity: 0;}
    .upload-img, .prev-img{height: 100px; width: 100px; border: 1px solid #ccc; border-radius: 10px; display: inline-block; vertical-align: top; overflow: hidden; margin: 0px 5px;}
    .upload-img table, .upload-img table td, .prev-img table, .prev-img table td{width: 100px; height: 100px; max-width: 100px; max-height: 100px;}
    .upload-img img, .prev-img img{display: block; width: 100%;}
    .prev-img{cursor: pointer; position: relative;}
    .prev-img .pop-close{display: none; position: absolute; top: 0px; right: 0px; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); text-align: center;}
    .prev-img .pop-close .fa-times{line-height: 100px; font-size: 30px; margin: auto; color: #fff;}
    .prev-img:hover .pop-close{display: block;}
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Comments Management</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Edit Comment</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(['url' => 'comment/update/' . $comment->id, 'method'=> 'PUT', 'class' => 'form-horizontal', 'enctype' => "multipart/form-data"]) }}
                            <div class="form-group @if ($errors->has('comment_date')) has-error @endif">
                                {{ Form::label('comment_date', 'Date & Time *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('comment_date', $comment->comment_date, array('id' => 'datepicker', 'class'=> 'form-control', 'placeholder' => 'YYYY-MM-DD HH:ii:ss')) }}
                                    {{ $errors->first('comment_date', '<p class="help-block">:message</p>') }}
                                    <p class="help-block">Format: YYYY-MM-DD HH:ii:ss</p>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('user')) has-error @endif">
                                {{ Form::label('user', 'User *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    {{ Form::text('user', $comment->full_name, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                                    {{ $errors->first('user', '<p class="help-block">:message</p>') }}
                                    {{ Form::hidden('user_id', $comment->userid, array('id' => 'user_id')) }}                                   
                                </div>
                                <div class="col-xs-1">
                                    <div class="input-group">
                                        <div class="input-group-btn">
                                            <span class="pull-left"><button id="selectUserBtn" class="btn btn-primary selectUserBtn" href="/comment/ajaxcustomer"><i class="fa fa-plus"></i> Select User</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('product')) has-error @endif">
                                {{ Form::label('product', 'Product *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                    {{ Form::text('product', $comment->name, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                                    {{ $errors->first('product', '<p class="help-block">:message</p>') }}
                                    {{ Form::hidden('product_id', $comment->prodid, array('id' => 'product_id')) }} 
                                </div>
                                <div class="col-xs-1">
                                    <div class="input-group">
                                        <div class="input-group-btn">
                                            <span class="pull-left"><button id="addProdBtn" class="btn btn-primary addProdBtn" href="/comment/ajaxproduct"><i class="fa fa-plus"></i> Select Product</span>
                                        </div>
                                    </div>
                                </div>
                            </div>  

                            <hr />

                            <div class="form-group @if ($errors->has('comment')) has-error @endif">
                                {{ Form::label('comment', 'Comment *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-10">
                                    {{ Form::textarea('comment', $comment->comment, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                    {{ $errors->first('comment', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('image')) has-error @endif">
                                {{ Form::label('image[]', 'Image', array('class'=> 'col-lg-2 control-label')) }}
                                <?php $de_img = ($comment->image ? json_decode($comment->image, true) : false); ?>
                                <div class="col-lg-10 wrapper-upimage">
                                    @if($de_img)
                                    @foreach($de_img AS $img)
                                    <div class="prev-img" data-imgsrc="{{ htmlentities($img, ENT_QUOTES) }}"><table><td><img src="{{ url('/') . '/' . Config::get('constants.COMMENT_IMG_PATH') . $img }}"></td></table><div class="pop-close"><span class="fa fa-times"></span></div></div>
                                    @endforeach
                                    @endif
                                    <label class="up-img"><span class="fa fa-plus"></span><input type="file" name="image[]" multiple onchange="FileOnChange(event, this)" accept="image/gif, image/jpeg, image/png"></label>
                                    {{ $errors->first('image', '<p class="help-block">:message</p>') }}
                                </div>
                                <div>
                                    <div class="col-lg-2"></div>
                                    <div class="col-lg-10"><p style="color: red;">Upload image file size should not exceed 1MB</p></div>
                                </div>
                                <input type="hidden" name="image_ref" value="{{ $de_img ? implode(',', $de_img) : '' }}">
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('rating')) has-error @endif">
                                {{ Form::label('rating', 'Rating *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-md-4">
                                    <label for="rating-radio-inline1" class="radio-inline">
                                        {{ Form::radio('rating', '1 star', ($comment->rating == '1') ? true : false) }} 1 star
                                    </label>
                                    <label for="rating-radio-inline2" class="radio-inline">
                                        {{ Form::radio('rating', '2 stars', ($comment->rating == '2') ? true : false) }} 2 stars
                                    </label>
                                    <label for="rating-radio-inline3" class="radio-inline">
                                        {{ Form::radio('rating', '3 stars', ($comment->rating == '3') ? true : false) }} 3 stars
                                    </label> 
                                    <label for="rating-radio-inline4" class="radio-inline">
                                        {{ Form::radio('rating', '4 stars', ($comment->rating == '4') ? true : false) }} 4 stars 
                                    </label>
                                    <label for="rating-radio-inline5" class="radio-inline">
                                        {{ Form::radio('rating', '5 stars', ($comment->rating == '5') ? true : false) }} 5 stars
                                    </label>
                                    {{ $errors->first('rating', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('status')) has-error @endif">
                                {{ Form::label('status', 'Status', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-md-4">
                                    <select name="status" class="form-control">
                                        <?php
                                            foreach($status as $s_val => $s_txt){
                                                if($s_val == 2) continue;
                                                echo '<option value="' . $s_val . '">' . $s_txt . '</option>';
                                            }
                                        ?>
                                    </select>
                                    {{ $errors->first('status', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('lang')) has-error @endif">
                                {{ Form::label('lang', 'Language', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-md-4">
                                    <select name="lang" class="form-control">
                                        <?php
                                            foreach($lang as $l_k => $l_v) echo '<option value="' . $l_k . '"' . ($comment->lang === $l_k ? ' selected' : '') . '>' . $l_v . '</option>';
                                        ?>
                                    </select>
                                    {{ $errors->first('lang', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <hr />

                            <div class="form-group">
                                <div class="col-lg-10 col-lg-offset-2">
                                    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 3, 3, 'AND'))
                                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                    <button type="submit" value="Save" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
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

    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <script>
        var FileOnChange = function(event, ev_target) {
            console.log(event.target.files);
            var parents = $(event.target).parents('.col-lg-10').first();
            $(parents).children('.upload-img').remove();
            for (var i = 0; i < event.target.files.length; i++) {
                if(event.target.files[i].size > 1048576){
                    $(parents).children('.upload-img').remove();
                    ev_target.value = '';
                    alert('Image is too big, Please upload image less than 1MB');
                }else{
                    src = URL.createObjectURL(event.target.files[i]);
                    $(parents).prepend('<div class="upload-img"><table><td><img src="' + src + '"></td></table></div>');
                }
            }
        };
    </script>
@stop
    

@section('script')
    
    $('#datepicker').datepicker({ dateFormat: "yy-mm-dd" }).val();

    $('#addProdBtn').colorbox({
        iframe:true, width:"80%", height:"80%",
        onCleanup:function(){ 
            $('input#product').val(localStorage.data3);
            $('input#product_id').val(localStorage.data0);
        },
        onClosed: function() {
            localStorage.clear();
        }
    });

    $('#selectUserBtn').colorbox({
        iframe:true, width:"80%", height:"80%",
        onCleanup:function(){ 
            $('input#user').val(localStorage.data2);
            $('input#user_id').val(localStorage.data0);
        },
        onClosed: function() {
            localStorage.clear();
        }
    });

    $(document).on("click", ".prev-img", function(e) {
        var t_ele = this;
        var t_src = $(this).data('imgsrc');
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete these image?",
            callback: function(result) {
                if (result === true) {
                    $(t_ele).remove();
                    var text = $('input[name="image_ref"]').val();
                    $('input[name="image_ref"]').val(text.replace(t_src, ""));
                }
            }
        });
    });
@stop