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

            <h1 class="page-header">{{ $type }} Event/Camping Banner Type</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(array('url' => Request::url(), 'class' => 'form-horizontal', 'files' => true, 'method'=>'POST', 'enctype' => "multipart/form-data")) }}

    <div class="row" style="margin-top: 15px;">
        <div class="col-lg-12">
            <div class="tab-content" style="padding-left: 5px;padding-right: 5px;">
                <div id="home" class="tab-pane fade in active">
                    <br>
                    <!-- START HQ-->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-pencil"></i> Event/Camping Info</h2>
                        </div>
                        <div class="panel-body ">
                            <div class="col-lg-12">
                                <div class="clearfix contentdate" data-name="beign_at" style="margin-bottom: 10px;">
                                    <label class="col-lg-3" for="beign_at">Event/Camping Name</label>
                                    <div class="col-lg-9">
                                        <p>Maximum Charcter input is 40 charcter. Text count will include spacing.</p>
                                        <input class="form-control" type="text" name="eventname" placeholder="Event/Camping Name" value="{{ ($event ? $event->name : false) }}" maxlength="40">
                                    </div>
                                </div>

                                <?php
                                    function TemplateBannerType($banner_type, $banner = false, $count = 1){
                                        $str = '';
                                        $match = (isset($banner->type) ? explode('_', $banner->type)[1] : 'hero');
                                        foreach ($banner_type as $key => $val) { 
                                            $str .= '<option value="' . $key . '" ' . ($match === $key ? 'selected' : '') . '>' . $val . '</option>';
                                        }
                                        echo '
                                        <div class="banner_t" data-count="' . $count . '">
                                            <div class="clearfix" style="margin-bottom: 10px;">
                                                <label class="col-lg-3" for="bannertype">Banner Type ' . $count . '</label>
                                                <div class="col-lg-9">
                                                    <select class="form-control" name="banner_type[]" id="bannertype">' . $str . '</select>
                                                </div>
                                            </div>
                                            <div class="clearfix" style="margin-bottom: 10px;">
                                                <label class="col-lg-3">Upload Example Image For Banner ' . $count . '</label>
                                                <div class="col-lg-9">
                                                    <input type="file" class="form-control" name="image[]" id="image" onchange="FileOnChange(event)" accept="image/gif, image/jpeg, image/png">
                                                    <input type="hidden" class="form-control" name="image_ref[]"' . ($banner ? 'value="' . $banner->banner_exp_img . '"' : '') . '>
                                                    ' . ($banner ? '<image class="preview" src="' . URL::to('/') . '/' . Config::get('constants.JOCOMMY_BANNER_PATH') . $banner->banner_exp_img . '" />' : '') . '
                                                </div>
                                            </div>
                                        </div>
                                        ';
                                    }

                                    if(isset($event_banner) && count($event_banner) > 0){
                                        foreach ($event_banner as $key => $banner) {
                                            TemplateBannerType($banner_type, $banner, $key + 1);
                                        }
                                    }else{
                                        TemplateBannerType($banner_type);
                                    }
                                ?>

                                <div class="clearfix" style="margin-bottom: 10px;">
                                    <label class="col-lg-3"></label>
                                    <div class="col-lg-9">
                                        <div class="btn btn-large btn-default" id="addbanner">Add Banner</div>
                                        <div class="btn btn-large btn-default" id="removebanner">Remove Banner</div>
                                    </div>
                                </div>

                                <div class="clearfix contentdate" data-name="beign_at" style="margin-bottom: 10px;">
                                    <label class="col-lg-3" for="beign_at">Begin At</label>
                                    <div class="col-lg-9">
                                        <input class="form-control" type="text" name="beign_date" placeholder="Click to select the datetime" value="{{ (isset($event->start_at) ? $event->start_at : false) }}">
                                    </div>
                                </div>

                                <div class="clearfix contentdate" data-name="end_at" style="margin-bottom: 10px;">
                                    <label class="col-lg-3" for="end_at"><div data-endtype="date">End At</div></label>
                                    <div class="col-lg-9">
                                        <div data-endtype="date">
                                            <input class="form-control" type="text" id="end_at" name="end_date" placeholder="Click to select the datetime" value="{{ (isset($event->end_at) ? $event->end_at : false) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="clearfix" data-name="beign_at" style="margin-bottom: 10px;">
                                    <label class="col-lg-3" for="status">Status</label>
                                    <div class="col-lg-9">
                                        <?php
                                            $match = (isset($banner_data->status) ? (int)$banner_data->status : 1);
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
    var FileOnChange = function(event) {
        // Load File
        var output = document.getElementById('previewIMG');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src) // free memory
        }

        // remove reference when have file
        $('input[name="image_ref"]').val('');

        // Check the upload file size
        // 1MB 1048576
        // 2MB 2097152
        if(this.files[0].size > 2097152){
            alert("File is exceed 2MB. Please compress the image");
            this.value = "";
        };
    };

    function handleChange(input) {
        if (input.value < 1) input.value = 1;
        if (input.value > {{ $max_count + 1 }}) input.value = {{ $max_count + 1 }};
    }

    <?php
        $current_count = (isset($event_banner) ? count($event_banner) : 1); // if edit event need to chancge it
        $total_banner = count($banner_data->type);
    ?>

    $(document).ready(function(){
        $('input[name="beign_date"], input[name="end_date"]').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });

        var current_count = {{ $current_count }};
        $(document).on('click', '#addbanner', function(event){
            var total_count = {{ $total_banner = count($banner_type) }};
            var banner_data = {{ json_encode($banner_type) }};
            if(current_count < total_count){
                current_count++;
                var str = '<div class="banner_t" data-count="' + current_count + '"><div class="clearfix" style="margin-bottom: 10px;"><label class="col-lg-3" for="bannertype">Banner Type ' + current_count + '</label><div class="col-lg-9"><select class="form-control" name="banner_type[]" id="bannertype">';
                for (var key in banner_data) {
                    // skip loop if the property is from prototype
                    if (!banner_data.hasOwnProperty(key)) continue;

                    var obj = banner_data[key];
                    str += '<option value="'+ key + '">' + obj + '</option>';
                }
                str += '</select></div></div><div class="clearfix" style="margin-bottom: 10px;"><label class="col-lg-3">Upload Example Image For Banner ' + current_count + '</label><div class="col-lg-9"><input type="file" class="form-control" name="image[]" id="image" onchange="FileOnChange(event)" accept="image/gif, image/jpeg, image/png"><input type="hidden" class="form-control" name="image_ref[]"></div></div></div>';
                target = $("#addbanner").parents('.clearfix').first();
                target.before(str);
            }
        });

        $(document).on('click', '#removebanner', function(event){
            if(current_count > 1){
                $('.banner_t[data-count=' + current_count + ']').remove();
                current_count--;
            }
        });        

        $(document).on('change', 'select[name="banner_type"]', function(event){
            var val = event.target.value;
            var path = '{{ url('/') . '/' . Config::get('constants.JOCOMMY_BANNER_PATH') }}tips_';
            $('.banner_type_tips').attr('src', path + val + '.jpg');

            is_flashsales();
        });
    });
</script>
@stop
