@extends('layouts.master')

@section('title', 'Festival Campaigns Create New')
@stop

@section('extra-css')
    {{ HTML::style('vendors/colorpicker/bootstrap-colorpicker.min.css') }}
    <style>
        .navbar-custom{ background-color: rgba(0,0,0,.075);color: #464545; margin-top: 15px; }
        .navbar-custom h3{ font-size: 20px; font-weight: 700 !important;  }
        .navbar-brand-centered {
            position: absolute;
            left: 50%;
            display: block;
            text-align: center;
            background-color: transparent;
        }
        .navbar-brand-centered h3{margin:0;margin-left: -30px;margin-top:-2px}
            .navbar>.container .navbar-brand-centered,
            .navbar>.container-fluid .navbar-brand-centered {
                margin-left: -80px;
        }
        .form_main {
            width: 100%;
        }
        .form_main h4 {
            font-family: roboto;
            font-size: 20px;
            font-weight: 300;
            margin-bottom: 15px;
            margin-top: 20px;
            text-transform: uppercase;
        }
        .heading {
            border-bottom: 1px solid #fcab0e;
            padding-bottom: 9px;
            position: relative;
        }
        .heading span {
            background: #9e6600 none repeat scroll 0 0;
            bottom: -2px;
            height: 3px;
            left: 0;
            position: absolute;
            width: 75px;
        }
        .txt2[type="submit"] {
            background: #242424 none repeat scroll 0 0;
            border: 1px solid #4f5c04;
            border-radius: 25px;
            color: #fff;
            font-size: 16px;
            font-style: normal;
            line-height: 35px;
            margin: 10px 0;
            padding: 0;
            text-transform: uppercase;
            width: 30%;
        }
        .txt2:hover {
            background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
            color: #5793ef;
            transition: all 0.5s ease 0s;
        }
        .label-txt{
            font-size: 12px;
        }

       /* Switch button */
        .btn-default.btn-on.active{background-color: #5BB75B;color: white;}
        .btn-default.btn-off.active{background-color: #DA4F49;color: white;}

        .btn-default.btn-on-1.active{background-color: #006FFC;color: white;}
        .btn-default.btn-off-1.active{background-color: #DA4F49;color: white;}

        .btn-default.btn-on-2.active{background-color: #00D590;color: white;}
        .btn-default.btn-off-2.active{background-color: #A7A7A7;color: white;}

        .btn-default.btn-on-3.active{color: #5BB75B;font-weight:bolder;}
        .btn-default.btn-off-3.active{color: #DA4F49;font-weight:bolder;}

        .btn-default.btn-on-4.active{background-color: #006FFC;color: #5BB75B;}
        .btn-default.btn-off-4.active{background-color: #DA4F49;color: #DA4F49;}

        #floating_img, #cover_img, #featured_img{
          max-width:100px;
          height:100px;
          margin-top:20px;
        }
        .bootstrap-datetimepicker-widget {
            z-index: 1100 !important;
        }
    </style>
@stop

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <nav class="navbar navbar-custom">
              <div class="container-fluid">
                <div class="navbar-header" style="padding-left: 10px">
                  <a href="/campaigns/festival-campaigns" class="btn btn-default navbar-btn pull-left"><span class="glyphicon glyphicon-chevron-left"></span> Back</a>
                  <div class="navbar-text navbar-brand-centered"><h3>Create New Festival Campaign</h3></div>
                </div>
              </div>
            </nav>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form_main">
                    <h4 class="heading"><strong>Create </strong> New <span></span></h4>
                    <div class="form">
                        @if(count($errors) > 0 || Session::has('message'))
                            <div class="alert alert-{{ !empty(Session::has('message')) ? 'info' : 'warning' }}">
                                <ul>
                                    @if (Session::has('message'))
                                        <li>{{ Session::get('message') }}</li>
                                    @endif
                                    @foreach($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="/campaigns/festival-campaigns" method="post" id="contactFrm" name="contactFrm" enctype="multipart/form-data">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Festival Title</label>
                                    <input type="text" required placeholder="Festival Title" id="title" name="title" value="" class="form-control">
                                </div>
                                 <div class="form-group">
                                    <label for="items">ITEMS / QRCODE(s)  <span class="label label-txt label-primary">(QRCODE(s) must be separated with comma without space. e.g. : QR4,QR6,QR8)</span></label>
                                    <textarea required placeholder="QRCODE(s) must be separated with comma without space." name="items" id="items" type="text" class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="shortdesc">Short Description</label>
                                    <textarea required placeholder="Short Description" name="description_1" id="description_1" type="text" class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="fromdate">From Date</label>
                                    <input type="text" required placeholder="From Date" value="" name="valid_from" id="valid_from" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="todate">To Date</label>
                                    <input type="text" required placeholder="To Date" value="" name="valid_to" id="valid_to" class="form-control">
                                </div>
                                <div class="form-group">
                                    <br>
                                    <div class="col-md-4">
                                        <label for="todate">Floating Image</label>
                                        <input type="file" name="floating_img" class="form-control" accept="image/*" required onchange="readURL(this);" />
                                        <img id="floating_img" class="img-circle" data-toggle="popover" title="Floating Image" data-placement="bottom" data-content="Size must be 350x350 px" src="https://via.placeholder.com/100" alt="Floating image" />
                                    </div>
                                    <div class="col-md-4">
                                        <label for="todate">Featured Image</label>
                                        <input type="file" name="featured_img" class="form-control" accept="image/*" required onchange="readURL(this);" />
                                        <img id="featured_img" class="img-circle" data-toggle="popover" title="Featured Image" data-placement="bottom" data-content="Size must be 512x512 px" src="https://via.placeholder.com/100" alt="Floating image" />
                                    </div>
                                    <div class="col-md-4">
                                        <label for="todate">Cover Image</label>
                                        <input type="file" name="cover_img" class="form-control" accept="image/*" required onchange="readURL(this);" />
                                        <img id="cover_img" data-toggle="popover" title="Cover Image" data-placement="bottom" data-content="Size must be 512x512 px" class="img-circle" src="https://via.placeholder.com/100" alt="Floating image" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="desc">Description</label>
                                    <textarea placeholder="Description" name="description_2" id="description_2" type="text" class="form-control"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="greet">Greeting Message</label>
                                    <input type="text" placeholder="Greeting Message" value="" name="greet_txt" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label for="Primary Color">Primary Color</label>
                                    <div id="cp1" class="input-group colorpicker-component">
                                        <input type="text" name="color_primary" value="#fafafa" class="form-control" />
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Primary Color">Primary Dark Color</label>
                                    <div id="cp2" class="input-group colorpicker-component">
                                        <input type="text" name="color_primary_dark" value="#7e898a" class="form-control" />
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Primary Color">Color Accent</label>
                                    <div id="cp3" class="input-group colorpicker-component">
                                        <input type="text" name="color_accent" value="#aa381e" class="form-control" />
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="Primary Color">Color Text</label>
                                    <div id="cp4" class="input-group colorpicker-component">
                                        <input type="text" name="color_text" value="#fafafa" class="form-control" />
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                  <label for="Frequent">Frequent Status</label>
                                  <div>
                                    <select id="frequent_status" name="frequent_status" class="form-control">
                                      <option value="1">One Time</option>
                                      <option value="2">Always</option>
                                      <option value="3">User Decide</option>
                                    </select>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="Frequent">Effect</label>
                                  <div>
                                    <select id="effect_type" name="effect_type" class="form-control">
                                      <option value="1">Screen</option>
                                      <option value="2">Confetti</option>
                                      <option value="3">Christmas</option>
                                      <option value="4">Other</option>
                                    </select>
                                  </div>
                                </div>
                                <div class="form-group">
                                  <label for="Frequent">Related Effect</label>
                                  <div>
                                    <select id="related_effect" name="related_effect" class="form-control">
                                      <option value="1">True</option>
                                      <option value="0">False</option>
                                    </select>
                                  </div>
                                </div>
                                <div class="form-group">
                                    <label for="Status">Status</label>
                                    <div>
                                        <div class="btn-group" id="status" data-toggle="buttons">
                                          <label class="btn btn-default btn-on active">
                                          <input type="radio" value="1" name="status" checked="checked">ON</label>
                                          <label class="btn btn-default btn-off">
                                          <input type="radio" value="0" name="status">OFF</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <hr>
                                 <div class="form-group text-center">
                                    <input type="submit" value="Create" class="btn btn-lg btn-success txt2">
                                 </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('extra-js')
    {{ HTML::script('vendors/colorpicker/bootstrap-colorpicker.min.js') }}
    <script>
        $(function() {
            $('[data-toggle="popover"]').popover('show').off('click');

            $('#valid_from').datetimepicker({
                format: 'YYYY-MM-DD H:mm',
            });

            $('#valid_to').datetimepicker({
                format: 'YYYY-MM-DD H:mm',
            });

            $('#cp1').colorpicker();
            $('#cp2').colorpicker();
            $('#cp3').colorpicker();
            $('#cp4').colorpicker();
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    if (input.name == 'floating_img') {
                        $('#floating_img').attr('src', e.target.result);
                    }
                    if (input.name == 'cover_img') {
                        $('#cover_img').attr('src', e.target.result);
                    }
                    if(input.name === "featured_img") {
                        $('#featured_img').attr('src', e.target.result);
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@stop