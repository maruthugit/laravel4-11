@extends('layouts.master')

@section('title') Jocom Banner @stop

@section('content')

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="{{ url('/') }}/css/jquery.tagsinput.css" />
{{-- Without defer it will keep ecounter error when load it --}}
<script defer type="text/javascript" src="{{ url('/') }}/js/jquery.tagsinput.js"></script>

<style>
    #home .panel-body .clearfix p{margin-bottom: 0px;}
    #home .panel-body .clearfix textarea{resize: none; height: 120px;}
    .input-wrap h3, .input-wrap h4{margin-top: 0px; margin-bottom: 5px;}

    [data-type="scheduler"] .form-control{width: 15%; display: inline-block;}
    .form-control option:disabled{background-color: rgb(170, 170, 170); color: #fff;}
    #previewIMG, #previewIMG_M{max-width: 100%; width: auto; height: auto;}
    {{ ($multilang ? '[data-input-singlelang]' : '[data-input-multilang]') . '{display: none;}' }}
</style>

<div id="page-wrapper">
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

        <h1 class="page-header">{{ $type }} Banner</h1>
    </div>

    {{ Form::open(array('url' => Request::url(), 'class' => 'form-horizontal row', 'files' => true, 'method'=>'POST', 'enctype' => "multipart/form-data")) }}
    <div class="col-lg-12">
        <div class="tab-content" style="padding-left: 5px;padding-right: 5px;">
            <div id="home" class="tab-pane fade in active">
                <br>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Jocom Banner</h2>
                    </div>
                    <div class="panel-body ">
                        <div class="col-lg-12">
                            <div class="clearfix" style="margin-bottom: 10px;">
                                <label class="col-lg-3"></label>
                                <div class="col-lg-9">
                                    <p>Image File size maximum 2MB. IF image too big recommend compress it.</p>
                                    <p>Image Compressor: <a href="https://compressor.io" target="_blank">Compressor</a> <a href="https://tinypng.com" target="_blank">Tiny PNG</a></p>
                                </div>
                            </div>
                            <div class="clearfix" style="margin-bottom: 10px;">
                                <label class="col-lg-3" for="bannertype">Banner Type</label>
                                <div class="col-lg-9">
                                    <select class="form-control" name="banner_type" id="bannertype">
                                        <?php
                                            $match = (isset($banner_data->type) ? $banner_data->type : 'banner_hero');
                                        ?>
                                        @foreach ($banner_type as $key => $val)
                                            <option value="{{ $key }}" {{ $match === $key ? 'selected' : '' }}>{{ $val }}</option>
                                        @endforeach
                                    </select>
                                    <img class="banner_type_tips" src="{{ url('/') . '/' . Config::get('constants.JOCOMMY_BANNER_PATH') }}tips_{{ $match }}.jpg" style="max-width: 100%;">
                                </div>
                            </div>
                            <div class="clearfix" style="margin-bottom: 10px;">
                                <label class="col-lg-3" for="image">Banner Image</label>
                                <div class="col-lg-9">
                                    <?php
                                        if(isset($banner_data->image) && $multilang) $bd_imgs = json_decode($banner_data->image, true); // assume no JSON error
                                        $match = (isset($banner_data->image) && $banner_data->image && !$multilang ? $banner_data->image : '');
                                    ?>
                                    <div class="input-wrap" data-input-singlelang="img">
                                        <input type="file" class="form-control" name="image" id="image" onchange="FileOnChange(event)" accept="image/gif, image/jpeg, image/png">
                                        <input type="hidden" class="form-control" name="image_ref" value="{{ $match }}">
                                        <img id="previewIMG" {{ $match ? 'src="' . url('/') . '/' . Config::get('constants.JOCOMMY_BANNER_PATH') . $match . '"' : '' }}>
                                    </div>
                                    <div data-input-multilang="img">
                                        @if($multilang)
                                            @foreach($bd_imgs as $bdlang => $img)
                                            <div class="col-lg-6 input-wrap" data-multilang="{{ $bdlang }}">
                                                <h4>{{ $lang[$bdlang] . ' - ' . strtoupper($bdlang) }}</h4>
                                                <input type="file" class="form-control" name="limg[]" onchange="FileOnChange(event)" accept="image/gif, image/jpeg, image/png">
                                                <input type="hidden" class="form-control" name="limgref[]" value="{{ $img }}">
                                                <img id="previewIMG" src="{{ url('/') . '/' . Config::get('constants.JOCOMMY_BANNER_PATH') . $img }}">
                                            </div>
                                            @endforeach
                                        @else
                                        <div class="col-lg-6 input-wrap" data-multilang="en">
                                            <h4>English - EN</h4>
                                            <input type="file" class="form-control" name="limg[]" onchange="FileOnChange(event)" accept="image/gif, image/jpeg, image/png">
                                            <input type="hidden" class="form-control" name="limgref[]" value="">
                                            <img id="previewIMG">
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix" style="margin-bottom: 10px;">
                                <label class="col-lg-3" for="mimage">Banner Mobile Image</label>
                                <div class="col-lg-9">
                                    <?php
                                        if(isset($banner_data->image_m) && $multilang) $bdimgms = json_decode($banner_data->image_m, true); // assume no JSON error
                                        $match = (isset($banner_data->image_m) && $banner_data->image_m && !$multilang ? $banner_data->image_m : '');
                                    ?>
                                    <div class="input-wrap" data-input-singlelang="imgm">
                                        <input type="file" class="form-control" name="image_m" id="mimage" onchange="FileOnChange(event)" accept="image/gif, image/jpeg, image/png">
                                        <input type="hidden" class="form-control" name="image_ref_m" value="{{ $match }}">
                                        <img id="previewIMG_M" {{ $match ? 'src="' . url('/') . '/' . Config::get('constants.JOCOMMY_BANNER_PATH') . $match . '"' : '' }}>
                                    </div>
                                    <div data-input-multilang="imgm">
                                        @if($multilang)
                                            @foreach($bdimgms as $bdlang => $imgm)
                                            <div class="col-lg-6 input-wrap" data-multilang="{{ $bdlang }}">
                                                <h4>{{ $lang[$bdlang] . ' - ' . strtoupper($bdlang) }}</h4>
                                                <input type="file" class="form-control" name="limg_m[]" onchange="FileOnChange(event)" accept="image/gif, image/jpeg, image/png">
                                                <input type="hidden" class="form-control" name="limgref_m[]" value="{{ $imgm }}">
                                                <img id="previewIMG" src="{{ url('/') . '/' . Config::get('constants.JOCOMMY_BANNER_PATH') . $imgm }}">
                                            </div>
                                            @endforeach
                                        @else
                                        <div class="col-lg-6 input-wrap" data-multilang="en">
                                            <h4>English - EN</h4>
                                            <input type="file" class="form-control" name="limg_m[]" onchange="FileOnChange(event)" accept="image/gif, image/jpeg, image/png">
                                            <input type="hidden" class="form-control" name="limgref_m[]">
                                            <img id="previewIMG_M">
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix" style="margin-bottom: 10px;">
                                <label class="col-lg-3" for="title">Banner Title Name</label>
                                <div class="col-lg-9">
                                    <p>Title name display when at TnC Content or Product Banner</p>
                                    <div class="input-wrap" data-input-singlelang="title">
                                        <input type="text" class="form-control" name="title" {{ (isset($banner_data->title) && !$multilang ? 'value="' . $banner_data->title . '"' : '') }}>
                                    </div>
                                    <div data-input-multilang="title">
                                        @if($multilang)
                                            <?php $bt = json_decode($banner_data->title, true); ?>
                                            @foreach($bt as $btlang => $title)
                                            <div class="col-lg-6 input-wrap" data-multilang="{{ $btlang }}">
                                                <h4>{{ $lang[$btlang] . ' - ' . strtoupper($btlang) }}</h4>
                                                <input type="text" class="form-control" name="ltitle[]" value="{{ $title }}">
                                            </div>
                                            @endforeach
                                        @else
                                        <div class="col-lg-6 input-wrap" data-multilang="en">
                                            <h4>English - EN</h4>
                                            <input type="text" class="form-control" name="ltitle[]">
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix" style="margin-bottom: 10px;">
                                <label class="col-lg-3" for="position">Position at Banner</label>
                                <div class="col-lg-9">
                                    <input type="number" class="form-control" name="position" id="position" value="{{ (isset($banner_data->position) ? ((int)$banner_data->position >= 1 ? $banner_data->position : 1) : 1) }}" onchange="handleChange(this);">
                                </div>
                            </div>
                            <div class="clearfix" style="margin-bottom: 10px;">
                                <?php
                                    $match = ($contenttype ? $contenttype : 0);
                                ?>
                                <label class="col-lg-3" for="content_type">Content Type</label>
                                <div class="col-lg-9">
                                    <select class="form-control" name="content_type" id="content_type">
                                        @foreach ($content_type as $key => $val)
                                            <option value="{{ $key }}" {{ $match === $key ? 'selected' : '' }}>{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="clearfix" style="margin-bottom: 10px;">
                                <?php
                                    $val = (isset($banner_data->content_data) ? $banner_data->content_data : null);
                                    $checkNset = (isset($banner_data->content_data) ? true : false);
                                ?>
                                <label class="col-lg-3" for="content_data">Content</label>
                                <div class="col-lg-9 contentdata_selection">
                                    <div data-name="empty">
                                        <p>-</p>
                                    </div>

                                    <div data-name="url">
                                        <p>Example of Url Link: https://jocom.my</p>
                                        <input class="form-control" type="text" placeholder="Url Link" name="contentdata_url" value="{{ ($checkNset ? ($contenttype === 'url' ? $val : '') : '') }}">
                                    </div>
                                    
                                    <div data-name="qrcode">
                                        <p>Example of single QR code: TM100</p>
                                        <p>Example of multiple QR code: TM120, TM130</p>
                                        <textarea class="form-control" placeholder="QR code" name="contentdata_qrcode">{{ ($checkNset ? ($contenttype === 'qrcode' ? $val : '') : '') }}</textarea>
                                    </div>
                                    
                                    <div data-name="html">
                                        <textarea class="form-control" placeholder="Text" name="contentdata_html">{{ ($checkNset ? ($contenttype === 'html' ? $val : '') : '') }}</textarea>
                                    </div>

                                    <div data-name="search_name">
                                        <div class="input-wrap" data-input-singlelang="contentdata_searchname">
                                            <input class="form-control" type="text" placeholder="Search Name" name="contentdata_searchname" value="{{ ($checkNset ? ($contenttype === 'search_name' ? $val : '') : '') }}">
                                        </div>
                                        <div data-input-multilang="contentdata_searchname">
                                            @if($multilang)
                                                <?php $bdcd = json_decode($banner_data->content_data, true); ?>
                                                @foreach($bdcd as $cdlang => $cd)
                                                <div class="col-lg-6 input-wrap" data-multilang="{{ $cdlang }}">
                                                    <h4>{{ $lang[$cdlang] . ' - ' . strtoupper($cdlang) }}</h4>
                                                    <input class="form-control" type="text" placeholder="Search Name" name="lcd_search_name[]" value="{{ $cd }}">
                                                </div>
                                                @endforeach
                                            @else
                                            <div class="col-lg-6 input-wrap" data-multilang="en">
                                                <h4>English - EN</h4>
                                                <input class="form-control" type="text" placeholder="Search Name" name="lcd_search_name[]">
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div data-name="category_id">
                                        <p>Example of single Category ID: 1039</p>
                                        <p>Example of multiple Category ID: 1039, 1048</p>
                                        <textarea class="form-control" placeholder="Category ID" name="contentdata_catid">{{ ($checkNset ? ($contenttype === 'category_id' ? $val : '') : '') }}</textarea>
                                    </div>

                                    <div data-name="template">
                                        <div class="input-wrap" data-input-singlelang="contentdata_template">
                                            <?php
                                                $template_html = '<option' . (!$checkNset ? ' selected' : '') . ' disabled>Please Select Template</option>';
                                                $bdcd = json_decode($val, true);
                                                $val = (!$multilang && $contenttype === 'template' ? $bdcd['id'] : false);
                                                if(count($template) > 0){
                                                    foreach ($template as $key => $value) {
                                                        $template_html .= '<option value="' . $key . '"' . ($checkNset ? ((int)$val === (int)$key ? 'selected' : '') : '') . '>' . $value . '</option>';
                                                    }
                                                }
                                            ?>
                                            <select class="form-control" name="contentdata_template">{{ $template_html }}</select>
                                        </div>
                                        <div data-input-multilang="contentdata_template">
                                            @if($multilang && $contenttype === 'template')
                                                @foreach($bdcd as $cdlang => $cd)
                                                <div class="col-lg-6 input-wrap" data-multilang="{{ $cdlang }}">
                                                    <h4>{{ $lang[$cdlang] . ' - ' . strtoupper($cdlang) }}</h4>
                                                    <select class="form-control" name="lcd_template[]">
                                                        <option disabled>Please Select Template</option>
                                                        @foreach ($template as $key => $value)
                                                            <option value="{{ $key }}" {{ ($checkNset ? ((int)$cd['id'] === (int)$key ? 'selected' : '') : '') }}>{{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @endforeach
                                            @else
                                            <div class="col-lg-6 input-wrap" data-multilang="en">
                                                <h4>English - EN</h4>
                                                <select class="form-control" name="lcd_template[]">{{ $template_html }}</select>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div data-name="mix">
                                        <p>Example of Category ID: CAT_1039</p>
                                        <p>Example of multiple Category ID: CAT_1039, CAT_1048</p>
                                        <p>Example of QR code: TM100</p>
                                        <p>Example of multiple QR code: TM120, TM130</p>
                                        <p>Example of Flash Sales ID: FLASH_11</p>
                                        <p>Example of multiple Flash Sales ID: FLASH_11, FLASH_2</p>
                                        <p>Example all above mixture input: FLASH_11, CAT_1039, TM120</p>
                                        <textarea class="form-control" placeholder="Only allow Category ID, QR code, Flash Sales ID" name="contentdata_mix">{{ ($checkNset ? ($contenttype === 'category_id' ? $val : '') : '') }}</textarea>
                                    </div>

                                    <div data-name="flashsales_id">
                                        <p>Example of Flash Sales ID: 12</p>
                                        <input class="form-control" type="text" placeholder="Flash Sales ID" name="contentdata_flashid" value="{{ ($checkNset ? ($contenttype === 'flashsales_id' ? $val : '') : '') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix" style="margin-bottom: 10px;">
                                <?php
                                    if(isset($banner_data->logic_operation)){ $banner_data->logic_operation = (int)$banner_data->logic_operation; }
                                    $match = (isset($banner_data->logic_operation) ? $banner_data->logic_operation : null);
                                ?>
                                <label class="col-lg-3" for="logic_operation">Duration Method</label>
                                <div class="col-lg-9">
                                    <select class="form-control" name="logic_operation" id="logic_operation">
                                        @foreach ($logic_operation as $key => $val)
                                            <option value="{{ $key }}" {{ $key === $match ? 'selected' : '' }}>{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="clearfix contentdate" data-name="beign_at" style="margin-bottom: 10px;">
                                <?php
                                    $begin_at_datetime = (isset($banner_data->begin_at) ? $banner_data->begin_at : null);
                                    $checkNset = (isset($banner_data->logic_operation) ? true : false);

                                    if($checkNset){
                                        if($banner_data->logic_operation === 2){
                                            $scheduler_val_raw = $begin_at_datetime;
                                            $scheduler_val = explode(' ', $scheduler_val_raw);
                                            $time_keys = array_values(array_slice(array_keys($scheduler_val), -3, 3, true));
                                            $begin_at_datetime = '';
                                        }
                                    }
                                ?>
                                <label class="col-lg-3" for="beign_at">Begin At</label>
                                <div class="col-lg-9">
                                    <input class="form-control" type="text" name="beign_date" data-type="non_scheduler" placeholder="Click to select the datetime" value="{{ ($checkNset ? ($banner_data->logic_operation !== 2 ? $begin_at_datetime : '') : '') }}">
                                    <div data-type="scheduler">
                                        <select class="form-control" name="beign_scheduler_type" id="beign_at">
                                            @foreach ($scheduler_type as $key => $val)
                                                <option value="{{ $key }}" {{ ($checkNset && $banner_data->logic_operation === 2 ? ($key === $scheduler_val[0] ? 'selected' : '') : '') }}>{{ $val }}</option>
                                            @endforeach
                                        </select>
                                        <select class="form-control" name="beign_scheduler_week">
                                            @foreach ($scheduler_week as $key => $val)
                                                <option value="{{ $key }}" {{ ($checkNset && $banner_data->logic_operation === 2 && isset($scheduler_val[0]) && $scheduler_val[0] === 'WEEK' ? ($key === $scheduler_val[1] ? 'selected' : '') : '') }}>{{ $val }}</option>
                                            @endforeach
                                        </select>
                                        <select class="form-control" name="beign_scheduler_month">
                                            @foreach ($scheduler_month as $key => $val)
                                                <option value="{{ $key }}" {{ ($checkNset && $banner_data->logic_operation === 2 && isset($scheduler_val[0]) && $scheduler_val[0] === 'YEAR' ? ($key === $scheduler_val[1] ? 'selected' : '') : '') }}>{{ $val }}</option>
                                            @endforeach
                                        </select>
                                        <select class="form-control" name="beign_scheduler_day" alt="Days" title="Days">
                                            @for ($x = 1; $x <= 31; $x++)
                                                <option value="{{ $x }}" {{ ($checkNset && $banner_data->logic_operation === 2 && isset($scheduler_val[0]) && $scheduler_val[0] === 'YEAR' ? ($key === $scheduler_val[2] ? 'selected' : '') : '') }}>{{ $x }}</option>
                                            @endfor
                                        </select>
                                        <select class="form-control" name="beign_scheduler_hours" alt="Hours" title="Hours">
                                            @for ($x = 0; $x < 24; $x++)
                                                <option value="{{ $x }}" {{ ($checkNset && $banner_data->logic_operation === 2 ? ($x == (int)$scheduler_val[$time_keys[0]] ? 'selected' : '') : ($x == 23 ? 'selected' : '')) }}>{{ str_pad($x, 2, "0", STR_PAD_LEFT) }}</option>
                                            @endfor
                                        </select>
                                        <select class="form-control" name="beign_scheduler_min" alt="Minutes" title="Minutes">
                                            @for ($x = 0; $x < 60; $x++)
                                                <option value="{{ $x }}" {{ ($checkNset && $banner_data->logic_operation === 2 ? ($x == (int)$scheduler_val[$time_keys[1]] ? 'selected' : '') : ($x == 59 ? 'selected' : '')) }}>{{ str_pad($x, 2, "0", STR_PAD_LEFT) }}</option>
                                            @endfor
                                        </select>
                                        <select class="form-control" name="beign_scheduler_sec" alt="Seconds" title="Seconds">
                                            @for ($x = 0; $x < 60; $x++)
                                                <option value="{{ $x }}" {{ ($checkNset && $banner_data->logic_operation === 2 ? ($x == (int)$scheduler_val[$time_keys[2]] ? 'selected' : '') : ($x == 59 ? 'selected' : '')) }}>{{ str_pad($x, 2, "0", STR_PAD_LEFT) }}</option>
                                            @endfor
                                        </select>
                                        <input class="form-control" type="hidden" name="beign_schedule" value="{{ ($checkNset ? ($banner_data->logic_operation === 2 ? $scheduler_val_raw : '') : '') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix contentdate" data-name="end_at" style="margin-bottom: 10px;">
                                <?php
                                    $val = (isset($banner_data->duration) ? $banner_data->duration : null);
                                    $checkNset = (isset($banner_data->logic_operation) ? true : false);
                                ?>
                                <label class="col-lg-3" for="end_at"><div data-endtype="date">End At</div><div data-endtype="time">Duration</div></label>
                                <div class="col-lg-9">
                                    <div data-endtype="date">
                                        <input class="form-control" type="text" id="end_at" name="end_date" placeholder="Click to select the datetime" {{ ($checkNset ? ($banner_data->logic_operation == 1 && $val ? 'value="' . date('Y-m-d h:i:s', strtotime($banner_data->begin_at) + $val) . '"' : '') : '') }}>
                                    </div>
                                    
                                    <div data-endtype="time">
                                        <p>This field only accept UNIX timestamp format as integer format.</p>
                                        <p>
                                            Example:<br>
                                            1 Hour as 3600 Seconds - input as: 3600<br>
                                            1 Day as 86400 Seconds - input as: 86400<br>
                                            1 Week as 604800 Seconds - input as: 604800<br>
                                            1 Month (30.44 days) as 2629743 Seconds - input as: 2629743<br>
                                            1 Year (365.24 days) as 31556926 Seconds - input as: 31556926
                                        </p>
                                        <input class="form-control" type="text" id="end_at" name="end_time" placeholder="UNIX timestamp; Eg: 3600" {{ ($checkNset ? ($banner_data->logic_operation == 2 ? 'value="' . $val . '"' : '') : '') }}>
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

                            <div class="clearfix" data-name="multilang" style="margin-bottom: 10px;">
                                <label class="col-lg-3" for="multilang">Enable Multi Language input</label>
                                <div class="col-lg-9">
                                    <label><input type="checkbox" name="multilang" id="multilang" value="is_multilang" {{ ($multilang ? 'checked' : '') }}> Not checked is use default ENG</label>
                                    <p>When is enable certain content will have multi lang require input.</p>
                                </div>
                            </div>

                            <div class="clearfix" data-name="lang" style="margin-bottom: 10px; {{ ($multilang ? '' : 'display: none;') }}">
                                <label class="col-lg-3" for="lang">Language</label>
                                <div class="col-lg-9">
                                    <input id="lang" type="text" name="lang" class="form-control" value="{{ (isset($banner_data->lang) && $banner_data->lang ? $banner_data->lang : 'en') }}" />
                                    <select id="langselect" class="form-control">
                                        @foreach ($lang as $lcode => $lang)
                                        <option value="{{ $lcode }}">{{ $lang }}</option>
                                        @endforeach
                                    </select>
                                    <p>When is enable certain content will have multi lang require input.</p>
                                </div>
                            </div>

                            <div class="clearfix" data-name="region" style="margin-bottom: 10px;">
                                <label class="col-lg-3" for="region">Avaliable on Country</label>
                                <div class="col-lg-9">
                                    <select id="region" name="region" class="form-control">
                                        <option>Worldwide All Region</option>
                                        @foreach ($region as $r_id => $rname)
                                        <option value="{{ $r_id }}" {{ (isset($banner_data->region) && $r_id == $banner_data->region ? 'selected' : '') }}>{{ $rname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="clearfix" data-name="platform" style="margin-bottom: 10px;">
                                <label class="col-lg-3" for="platform">Avalible on Plateform/Device</label>
                                <div class="col-lg-9">
                                    <select id="platform" name="platform" class="form-control">
                                        <?php $match = (isset($banner_data->platform) ? $banner_data->platform : false); ?>
                                        @foreach ($platform as $pc => $val)
                                        <option value="{{ $pc }}" {{ $pc === $match ? 'selected' : '' }}>{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br>
    <div class="col-lg-12">
        {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
        {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
    </div>
    <br>
    {{ Form::close() }}
</div>

<script>
    var FileOnChange = function(event) {
        // Load File
        var parents = $(event.target).parents('div').first();
        var output = parents.find('#previewIMG, #previewIMG_M')[0];
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src) // free memory
        }

        // remove reference when have file
        parents.find('input[name="image_ref"], input[name="image_ref_m"]').val('');

        // Check the upload file size
        // 1MB 1048576
        // 2MB 2097152
        if(event.target.files[0].size > 2097152){
            alert("File is exceed 2MB. Please compress the image");
            this.value = "";
        };
    };

    function handleChange(input) {
        if (input.value < 1) input.value = 1;
        if (input.value > {{ $max_count + 1 }}) input.value = {{ $max_count + 1 }};
    }

    $(document).ready(function(){
        function is_flashsales(){
            if($('select[name="banner_type"]').val() === 'banner_flashsales'){
                if($('select[name="content_type"]').val() !== 'flashsales_id'){
                    $('select[name="content_type"]').val('url').trigger("change");
                }
                if($('select[name="logic_operation"]').val() !== '3'){
                    $('select[name="logic_operation"]').val(0).trigger("change");
                }

                // is flash sales ADJ the stuff to flash sales
                $('select[name="content_type"] option').prop('disabled', true);
                $('select[name="content_type"] option[value="flashsales_id"]').prop('disabled', false);
                $('select[name="logic_operation"] option').prop('disabled', true);
                $('select[name="logic_operation"] option[value="3"]').prop('disabled', false);
            }else{
                if($('select[name="content_type"]').val() === 'flashsales_id'){
                    $('select[name="content_type"]').val('url').trigger("change");
                }
                if($('select[name="logic_operation"]').val() === '3'){
                    $('select[name="logic_operation"]').val(0).trigger("change");
                }

                // not flash sales hide the stuff related flash sales
                $('select[name="content_type"] option').prop('disabled', false);
                $('select[name="content_type"] option[value="mix"]').prop('disabled', true);
                $('select[name="content_type"] option[value="flashsales_id"]').prop('disabled', true);
                $('select[name="logic_operation"] option').prop('disabled', false);
                $('select[name="logic_operation"] option[value="3"]').prop('disabled', true);
            }
        }

        function is_scheduler(){
            // logic operation check
            if($('select[name="logic_operation"]').val() === '0'){
                $('.tab-content .contentdate[data-name="beign_at"] [data-type="scheduler"]').hide();
                $('.tab-content .contentdate[data-name="end_at"]').hide();
            }

            if($('select[name="logic_operation"]').val() === '1'){
                $('.tab-content .contentdate[data-name="beign_at"] [data-type="scheduler"]').hide();
                $('.tab-content .contentdate[data-name="end_at"] [data-endtype="time"]').hide(); 
            }

            if($('select[name="logic_operation"]').val() === '2'){
                $('.tab-content .contentdate[data-name="beign_at"] [data-type="non_scheduler"]').hide();
                $('.tab-content .contentdate[data-name="end_at"] [data-endtype="date"]').hide();

                if($('select[name="beign_scheduler_type"]').val() === 'DAY'){
                    $('select[name="beign_scheduler_week"], select[name="beign_scheduler_month"], select[name="beign_scheduler_day"]').hide();
                }

                if($('select[name="beign_scheduler_type"]').val() === 'WEEK'){
                    $('select[name="beign_scheduler_month"], select[name="beign_scheduler_day"]').hide();
                }

                if($('select[name="beign_scheduler_type"]').val() === 'MONTH'){
                    $('elect[name="beign_scheduler_week"], select[name="beign_scheduler_month"]').hide();
                }

                if($('select[name="beign_scheduler_type"]').val() === 'YEAR'){
                    $('select[name="beign_scheduler_week"]').hide();
                }
            }
        }

        $('.contentdata_selection [data-name]').hide();
        $('.contentdata_selection [data-name="' + $('select[name="content_type"]').find(":selected").val() + '"]').show();
        
        is_scheduler();
        is_flashsales();

        $('input[name="beign_date"], input[name="end_date"]').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });

        $(document).on('change', 'select[name="content_type"]', function(event){
            $('.contentdata_selection [data-name]').hide();
            $('.contentdata_selection [data-name="' + event.target.value + '"]').show();
        });

        $(document).on('change', 'select[name="banner_type"]', function(event){
            var val = event.target.value;
            var path = '{{ url('/') . '/' . Config::get('constants.JOCOMMY_BANNER_PATH') }}';
            var event_banner = {{ json_encode($banner_img) }};
            $('.banner_type_tips').attr('src', event_banner[val] ? path + event_banner[val] : path + 'tips_' + val + '.jpg');

            is_flashsales();
        });

        $(document).on('change', 'select[name="beign_scheduler_type"]', function(event){
            $('select[name="beign_scheduler_week"], select[name="beign_scheduler_month"], select[name="beign_scheduler_day"]').hide();
            if(event.target.value === 'WEEK'){
                $('select[name="beign_scheduler_week"]').css('display', 'inline-block');
            }else if(event.target.value === 'MONTH'){
                $('select[name="beign_scheduler_day"]').css('display', 'inline-block');
            }else if(event.target.value === 'YEAR'){
                $('select[name="beign_scheduler_month"], select[name="beign_scheduler_day"]').css('display', 'inline-block');
            }
        });

        $(document).on('change', 'select[name="logic_operation"]', function(event){
            var val = parseInt(event.target.value);
            $('.tab-content .contentdate[data-name]').hide();
            if(val != 3){
                if(val == 2){
                    $('.tab-content .contentdate[data-name="beign_at"] [data-type="scheduler"]').show();
                    $('.tab-content .contentdate[data-name="beign_at"] [data-type="non_scheduler"]').hide();

                    $('select[name="beign_scheduler_type"]').val('DAY');
                    $('select[name="beign_scheduler_week"], select[name="beign_scheduler_month"], select[name="beign_scheduler_day"]').hide();

                    var format = $('select[name="beign_scheduler_type"]').val() + ' ' + $('select[name="beign_scheduler_hours"]').val() + ' ' + $('select[name="beign_scheduler_min"]').val() + ' ' + $('select[name="beign_scheduler_sec"]').val();
                    $('input[name="beign_schedule"]').val(format);
                }else{
                    $('.tab-content .contentdate[data-name="beign_at"] [data-type="scheduler"]').hide();
                    $('.tab-content .contentdate[data-name="beign_at"] [data-type="non_scheduler"]').show();
                }
                $('.tab-content .contentdate[data-name="beign_at"]').show();
            }

            if(val == 1 || val == 2){
                $('.tab-content [data-endtype="date"], .tab-content [data-endtype="time"]').hide();
                $('.tab-content .contentdate[data-name="end_at"], .tab-content [data-endtype="' + (val == 1 ? 'date' : 'time') + '"]').show();
            }
        });

        $(document).on('change', '[data-type="scheduler"] select', function(event){
            var refheader = 'select[name="beign_scheduler_';
            var schedule_type = $(refheader + 'type"]').val();
            var base_format = $(refheader + 'hours"]').val() + ' ' + $(refheader + 'min"]').val() + ' ' + $(refheader + 'sec"]').val();
            // Default treat as DAY type format
            $('input[name="beign_schedule"]').val(schedule_type + ' ' + (schedule_type === 'WEEK' ? $(refheader + 'week"]').val() + ' ' : (schedule_type === 'MONTH' ? $(refheader + 'day"]').val() + ' ' : (schedule_type === 'YEAR' ? $(refheader + 'month"]').val() + ' ' + $(refheader + 'day"]').val() + ' ' : ''))) + base_format);
        });

        // 
        $(document).on('change', 'input[name="multilang"]', function() {
            $('[data-name="lang"]').css('display', (this.checked ? 'block' : 'none'));
            $('[data-input-singlelang]').css('display', (this.checked ? 'none' : 'block'));
            $('[data-input-multilang]').css('display', (this.checked ? 'block' : 'none'));
        });

        var reflang = {
            before: ['{{ (isset($banner_data->lang) && $banner_data->lang ? implode("','", explode(',', $banner_data->lang)) : 'en') }}'],
            after: []
        };
        $('#lang').tagsInput({ 
            width: 'auto',
            readonly: true,
            addClass: 'form-control',
            onChange: function(elem, elem_tags){
                if(elem_tags !== undefined){
                    reflang.after.push(elem_tags);
                }else{
                    if(reflang.before.sort().toString() != reflang.after.sort().toString()){
                        for (var i = 0; i < reflang.after.length; i++) {
                            var matchidx = reflang.before.indexOf(reflang.after[i]);
                            if(matchidx != -1){
                                reflang.before.splice(matchidx, 1);
                            }else{
                                var headhtml = '<div class="col-lg-6 input-wrap" data-multilang="' + reflang.after[i] + '"><h4>' + $('#langselect option[value="' + reflang.after[i] + '"]').text() + ' - ' + reflang.after[i].toUpperCase() + '</h4>';
                                $('[data-input-multilang="img"]').append(headhtml + '<input type="file" class="form-control" name="limg[]" onchange="FileOnChange(event)" accept="image/gif, image/jpeg, image/png"><input type="hidden" class="form-control" name="limgref[]"><img id="previewIMG"></div>');
                                $('[data-input-multilang="imgm"]').append(headhtml + '<input type="file" class="form-control" name="limg_m[]" onchange="FileOnChange(event)" accept="image/gif, image/jpeg, image/png"><input type="hidden" class="form-control" name="limgref_m[]" value=""><img id="previewIMG_M"></div>');
                                $('[data-input-multilang="title"]').append(headhtml + '<input type="text" class="form-control" name="ltitle[]"></div>');
                                $('[data-input-multilang="contentdata_searchname"]').append(headhtml + '<input class="form-control" type="text" placeholder="Search Name" name="lcd_search_name[]"></div>');
                                $('[data-input-multilang="contentdata_template"]').append(headhtml + '<select class="form-control" name="lcd_template[]">{{ htmlentities($template_html, ENT_QUOTES) }}</select></div>');
                            }
                        }
                        if(reflang.before.length > 0){
                            for (var i = 0; i < reflang.before.length; i++) {
                                $('.col-lg-6.input-wrap[data-multilang="' + reflang.before[i] + '"]').remove();
                            }
                        }
                    }
                    reflang.before = reflang.after.sort();
                    reflang.after = []; // reset the after object
                }
            }
        });

        $(document).on('change', '#langselect', function(){
            var input_val = $('#lang').val();
            if(input_val){
                var reg = new RegExp('(,' + this.value + ',|,' + this.value + '$|^' + this.value + ',|^' + this.value + '$)', 'gm');
                var match = $('#lang').val().match(reg);
                if(!match) input_val += ',' + this.value;
            }else{
                input_val = this.value;
            }
            $('#lang').importTags(input_val);
        });
    });
</script>
@stop
