@extends('layouts.master')
@section('title') Article Posts @stop
@section('content')
<div class="loading"><span id="load-message"></span></div>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
           <h1 class="page-header">Create Posts</h1>
             
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <div class="row" style="background-color: #fbfbfb;" >
        <div class="col-lg-3 col-md-12 col-xs-12" id="blog-form" style="padding: 10px">
            <h4 class="ui horizontal divider header"><i class="tag icon"></i>
                       Post Details
            </h4>
            <div class="form-group">
                <label for="email">Title:</label>
                <input type="" class="form-control" id="email" v-model="title">
            </div>
            <div class="form-group">
                <label for="pwd">Category:</label>
                <select name="categories" id="categories" class="form-control">
                <?php foreach ($categories as $key => $value) { ?>
                    <option value="<?php echo $value->id; ?>"><?php echo $value->category; ?></option>
                <?php } ?>
                </select>    
            </div>
            <div class="form-group">
                <label for="pwd">Publish Date:</label>
                <div class="input-group">
                    <span id="sizing-addon2" class="input-group-addon">
                        <i class="fa fa-calendar-o"></i></span> 
                        <input type="text" name="dueDate" placeholder="" id="dueDate" aria-describedby="sizing-addon2" class="form-control dueDate">
                </div>
            </div>
            <div class="form-group">
                <label for="pwd">Tag</label>
                <div class="input-group">
                    <input type="text" name="tag_input" v-model="tagInput" placeholder="" id="tag_input" aria-describedby="sizing-addon2" class="form-control">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button" v-on:click="addTag()"><i class="fa fa-plus"></i> Add Tag</button>
                    </span>
                </div>
                <div class="well" style="padding:5px;margin-top:5px;" v-if="tags.length > 0"> 
                    <div v-for="(tag, index) in tags" class="btn-group btn-sm" style="padding:0px;padding-top:2px;margin-left: 5px;" role="group" aria-label="..." >
                        <button type="button" class="btn btn-default btn-xs disabled">@{{tag}}</button>
                        <button type="button" class="btn btn-default btn-xs" v-on:click="removeTag(index)" ><i class="fa fa-trash-o"></i></button>
                    </div>
                </div>
            </div>
            <h4 class="ui horizontal divider header"><i class="tag icon"></i>Thumbnail Image(800px x 400px)</h4>
            <!-- Thumbnail Image -->
            <div class="form-group">
                <div>
                    <img v-on:click="addThumbTrigger" id="thumbnail_file_ico" style="cursor:pointer;width: 100%;height: 150px;" src="<?php echo $post->main_image_id != '' ? "/images/blog/".$post->main_image_id : '/images/asset/icon/add-image.png' ;  ?>" class="img-thumbnail" >
                    <input v-on:change="loadPreviewThumb" style="display: none;" type="file" id="thumbnail_file"  accept="image/x-png,image/jpeg,image/jpg" >
                    <!--<button class="btn btn-default" v-on:click="uploadThumb(123)">Test Upload</button>-->
                </div>
            </div>
            <h4 class="ui horizontal divider header"><i class="tag icon"></i>Status</h4>
            <div class="checkbox"><label><input type="checkbox" v-model="is_active"> Set as Active ?</label></div>
            <div class="checkbox"><label><input type="checkbox" v-model="is_pin_post"> Pin this post ?</label></div>
            <hr>
            <button class="btn btn-default btn-sm" v-on:click="savePost()"><i class="fa fa-save"></i> Save Post</button>
            <!--<button class="btn btn-default btn-sm"><i class="fa fa-upload"></i> Publish Now</button>-->
        </div>
        
        <div class="col-lg-9 col-md-12 col-xs-12" style="border-left: dashed 1px #ddd;background-color: #fff;">
            <div id="summernote"> <h1>Hi, <?php echo Session::get("username"); ?></h1>
                Create your post! .<br>
        </div>
    </div>
</div>

@stop
@section('inputjs')
<script>
    
    $( document ).ready(function() {
        
         $('#due_date').datetimepicker({
        format: 'YYYY-MM-DD'
        });
        
        $('#summernote').summernote({
            height: 600,                 // set editor height
            minHeight: null,             // set minimum height of editor
            maxHeight: null,             // set maximum height of editor
            focus: false                 // set focus to editable area after initializing summernote
        });
        
        
    });
   
</script>
<script src="/js/asset/blog.js?v=2" ></script>
@stop
@section('script')

@stop