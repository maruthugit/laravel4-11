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
    #home .panel-body .clearfix p{margin-bottom: 0px;}
    #home .panel-body .clearfix textarea{resize: none; height: 120px;}

    [data-type="scheduler"] .form-control{width: 15%; display: inline-block;}
    .form-control option:disabled{background-color: rgb(170, 170, 170); color: #fff;}
    #preview_contentimg img, #preview_bannerimg img{max-width: 300px; vertical-align: top;}
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

            <h1 class="page-header">{{ $title }} Banner Template</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(array('url' => Request::url(), 'class' => 'form-horizontal', 'method' => 'POST', 'enctype' => "multipart/form-data")) }}

    <div class="row" style="margin-top: 15px;">
        <div class="col-lg-12">
            <div class="tab-content" style="padding-left: 5px;padding-right: 5px;">
                <div id="home" class="tab-pane fade in active">
                    <br>
                    <!-- START HQ-->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-pencil"></i> Template Info</h2>
                        </div>
                        <div class="panel-body ">
                            <div class="col-lg-12">
                                <div class="clearfix contentdate" style="margin-bottom: 10px;">
                                    <label class="col-lg-3" for="beign_at">Template Name</label>
                                    <div class="col-lg-9">
                                        <p>Maximum Charcter input is 40 character. Text count will include spacing.</p>
                                        <input class="form-control" type="text" name="name" placeholder="Template Name"{{ (isset($data->name) && $data->name ? ' value="' . $data->name . '"' : false) }} maxlength="40">
                                    </div>
                                </div>

                                <div class="clearfix" style="margin-bottom: 10px;">
                                    <label class="col-lg-3" for="banner_image">Banner Image</label>
                                    <div class="col-lg-9">
                                        <input type="file" class="form-control" name="banner_image" id="banner_image" onchange="OnChangeBannerIMG(event)" accept="image/gif, image/jpeg, image/png">
                                        <input type="hidden" name="banner_image_ref"{{ (isset($data->banner_image) && $data->banner_image ? ' value="' . $data->banner_image . '"' : '') }}>
                                        <div id="preview_bannerimg">
                                            {{ isset($data->banner_image) && $data->banner_image ? '<img src="' . url('/') . '/' . Config::get('constants.JOCOMMY_BANNER_PATH') . $data->banner_image . '">' : '' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="clearfix" style="margin-bottom: 10px;">
                                    <label class="col-lg-3" for="content_image">Content Image</label>
                                    <div class="col-lg-9">
                                        <input type="file" class="form-control" name="content_image[]" id="content_image" onchange="OnChangeContentIMG(event)" multiple accept="image/gif, image/jpeg, image/png">
                                        <input type="hidden" name="content_image_ref"{{ (isset($data->content_image) && $data->content_image ? ' value="' . $data->content_image . '"' : '') }}>
                                        <div id="preview_contentimg">
                                            <?php
                                                if(isset($data->content_image) && $data->content_image) foreach (explode('|', $data->content_image) as $key => $value) echo '<img src="' . url('/') . '/' . Config::get('constants.JOCOMMY_BANNER_PATH') . $value . '">';
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="clearfix" style="margin-bottom: 10px;">
                                    <label class="col-lg-3" for="beign_at">Url encrypt hash</label>
                                    <div class="col-lg-8">
                                        <input class="form-control" type="text" name="crypt" placeholder="eg: https://jocom.my">
                                    </div>
                                    <div class="col-lg-1" style="padding-left: 0px;">
                                        <div class="btn btn-default btn-crypt" style="width: 100%;">Crypt</div>
                                    </div>
                                    <div class="col-lg-3"></div>
                                    <div class="col-lg-9 hashresult" style="word-break: break-all;"></div>
                                </div>
                                <div class="clearfix" style="margin-bottom: 10px;">
                                    <label class="col-lg-3">Format Example</label>
                                    <div class="col-lg-9">
                                        <div>
                                            <p>Bullet Format: <b>&|Format|Bullet|START|&</b> <b>&|Format|Bullet|END|&</b></p>
                                            <p>Number Format: <b>&|Format|Number|START|&</b> <b>&|Format|Number|END|&</b></p>
                                            <p>Dash Format: <b>&|Format|Dash|START|&</b> <b>&|Format|Dash|END|&</b></p>
                                        </div>
                                        <p>&|Image|Number|&</p>
                                        <p>&|Image|1|&</p>
                                        <div>
                                            <p>If want to use link redirect stuff should use the format <b>&|URL|HREFbase64HASH|IS_NEWTAB|TXT|&</b></p>
                                            <p>Example: <b>&|URL|aHR0cHM6Ly9qb2NvbS5teS8=|NEW|JOCOM|&</b> is <a href="https://jocom.my/" target="_blank">JOCOM</a></p>
                                        </div>
                                        <p>&|TEXT|BOLD|START|&</p>
                                        <p>&|TEXT|BOLD|END|&</p>
                                    </div>
                                </div>
                                <div class="clearfix" style="margin-bottom: 10px;">
                                    <label class="col-lg-3" for="beign_at">Content</label>
                                    <div class="col-lg-9">
                                        <textarea class="form-control" name="content">{{ (isset($data->content_input) && $data->content_input ? $data->content_input : '') }}</textarea>
                                    </div>
                                </div>

                                <div class="clearfix" style="margin-bottom: 10px;">
                                    <label class="col-lg-3" for="status">Status</label>
                                    <div class="col-lg-9">
                                        <?php
                                            $match = (isset($data->status) && $data->status ? (int)$data->status : 1);
                                        ?>
                                        <select class="form-control" name="status" id="status">
                                            @foreach ($status as $key => $val)
                                                <option value="{{ $key }}" {{ $key === $match ? 'selected' : '' }}>{{ $val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <p class="col-lg-12">Image File size maximum 2MB. IF image too big recommend compress it.</p>
                                <p class="col-lg-12">Image Compressor Service: <a href="https://compressor.io" target="_blank">Compressor</a> <a href="https://tinypng.com" target="_blank">Tiny PNG</a></p>
                            </div>
                        </div>
                    </div>
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
    var OnChangeContentIMG = function(e) {
        $('input[name="content_image_ref"]').val('');
        $('#preview_contentimg').html('');

        // Check the upload file size
        // 1MB 1048576
        // 2MB 2097152
        for (var i = e.target.files.length - 1; i >= 0; i--) {
            if(e.target.files[i].size > 2097152){
                alert("File is exceed 2MB. Please compress the image");
                e.target.value = "";
                break;
            }else{
                $('#preview_contentimg').append('<img src="' + URL.createObjectURL(e.target.files[i]) + '">');
            }
        }
    };

    var OnChangeBannerIMG = function(e) {
        $('input[name="banner_image_ref"]').val('');
        $('#preview_bannerimg').html('');

        for (var i = e.target.files.length - 1; i >= 0; i--) {
            if(e.target.files[i].size > 2097152){
                alert("File is exceed 2MB. Please compress the image");
                e.target.value = "";
                break;
            }else{
                $('#preview_bannerimg').append('<img src="' + URL.createObjectURL(e.target.files[i]) + '">');
            }
        }
    };

    $(document).on('click', '.btn-crypt', function(){
        $('.hashresult').html('Crypt Result:<br>' + btoa($('input[name="crypt"]').val()).replace( /(<([^>]+)>)/ig, ''));
    });
</script>
@stop
