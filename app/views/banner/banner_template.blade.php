@extends('layouts.master')

@section('title') Banner Template @stop

@section('content')

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<style>
    .table-responsive{
        max-width: 800px;
    }

    .nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus{
        border-bottom-color: #ddd !important; 
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

            <h1 class="page-header">Banner Template Management</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(array('url' => 'banner/bannertemplateupdate/' , 'class' => 'form-horizontal', 'files' => true, 'method'=>'POST', 'enctype' => "multipart/form-data")) }}

    <div class="row" style="margin-top: 15px;">
        <div class="col-lg-12">
            <ul class="nav nav-tabs" style="background: rgba(221, 221, 221, 0.38);border: 1px solid #ddd;border-top-right-radius: 9px;border-top-left-radius: 9px;">
                <li class="active"><a data-toggle="tab" href="#home"><center><i class="fa fa-picture-o fa-lg fa-lg" aria-hidden="true"></i></center>HQ</a></li>
                <li><a data-toggle="tab" href="#menu1"><center><i class="fa fa-picture-o fa-lg fa-lg"></i></center>JOHOR</a></li>
                <li><a data-toggle="tab" href="#menu2"><center><i class="fa fa-picture-o fa-lg fa-lg"></i></center>PNG</a></li>
            </ul> 
            <div class="tab-content" style="padding-left: 5px;padding-right: 5px;">
                <div id="home" class="tab-pane fade in active">
                    <br>
                    <!-- START HQ-->
                    <div class="panel panel-default B001_hq_temp" id="B001_hq_temp">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B001(HQ)</h2>
                        </div>
                        <div class="panel-body">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <div class="col-lg-2 pull-right">
                                        <select class="form-control" name="B001_hq">
                                             @foreach($status as $statuses=>$value)
                                                <option value="{{$statuses}}" <?php if ($statuses == $actives[0]['active_status']) echo 'selected="selected"'?>>{{ ucwords($value)}}</option>
                                                @endforeach
                                        </select>
                                        <input type="hidden" name="B001_hq_ori" value="{{$actives[0]['active_status']}}">
                                    </div>
                                </div>
                                <center><table class="table  table-responsive">
                                    <tr>
                                        <td rowspan="2" style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b001_hq[0]['file_name']}}" alt="" class="img-responsive center-block">
                                            <br>
                                            <input type="file" name="image1" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode1" class="form-control input-sm" value="{{$b001_hq[0]['qrcode']}}">
                                            <input type="hidden" value="{{$b001_hq[0]['qrcode']}}" name="qrcode1_ori">    
                                           <!--  <input type="hidden" value="{{$b001_hq[0]['id']}}" name="b1">  -->

                                            <span class="help-block">Size : {{$b001_hq[0]['max_width']}}px (W) x {{$b001_hq[0]['max_height']}}px (H)</span>      
                                        </td>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b001_hq[1]['file_name']}}" alt="" class="img-responsive">
                                            <br>
                                            <input type="file" name="image2" class="form-control input-sm" >
                                            <br>
                                            <input type="text" name="qrcode2" class="form-control input-sm" value="{{$b001_hq[1]['qrcode']}}">
                                            <input type="hidden" value="{{$b001_hq[1]['qrcode']}}" name="qrcode2_ori">    
                                            <!-- <input type="hidden" value="{{$b001_hq[1]['id']}}" name="b2"> -->
                                            <span class="help-block">Size : {{$b001_hq[1]['max_width']}}px (W) x {{$b001_hq[1]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b001_hq[2]['file_name']}}" alt="" class="img-responsive">
                                            <br>
                                            <input type="file" name="image3" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode3" class="form-control input-sm" value="{{$b001_hq[2]['qrcode']}}">
                                            <input type="hidden" value="{{$b001_hq[2]['qrcode']}}" name="qrcode3_ori">    
                                            <!-- <input type="hidden" value="{{$b001_hq[2]['id']}}" name="b3"> -->
                                            <span class="help-block">Size : {{$b001_hq[2]['max_width']}}px (W) x {{$b001_hq[2]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                </table></center>
                            </div>          
                        </div>
                    </div>
                    <div class="panel panel-default" id="B002_hq_temp">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B002(HQ)</h2>
                        </div>
                        <div class="panel-body">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <div class="col-lg-2 pull-right">
                                        <select class="form-control" name="B002_hq">
                                             @foreach($status as $statuses=>$value)
                                                <option value="{{$statuses}}" <?php if ($statuses == $actives[1]['active_status']) echo 'selected="selected"'?>>{{ ucwords($value)}}</option>
                                                @endforeach
                                        </select>
                                        <input type="hidden" name="B002_hq_ori" value="{{$actives[1]['active_status']}}">
                                    </div>
                                </div>
                               <center><table class="table  table-responsive">
                                    <tr>
                                        <td colspan="2" style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b002_hq[0]['file_name']}}" alt="" class="img-responsive" >
                                            <br>
                                            <input type="file" name="image4" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode4" class="form-control input-sm" value="{{$b002_hq[0]['qrcode']}}">
                                            <input type="hidden" value="{{$b002_hq[0]['qrcode']}}" name="qrcode4_ori">    
                                           <!--  <input type="hidden" value="/" name="b16"> -->
                                            <span class="help-block">Size : {{$b002_hq[0]['max_width']}}px (W) x {{$b002_hq[0]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b002_hq[1]['file_name']}}" alt="" class="img-responsive" >
                                             <br>
                                            <input type="file" name="image5" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode5" class="form-control input-sm" value="{{$b002_hq[1]['qrcode']}}">
                                            <input type="hidden" value="{{$b002_hq[1]['qrcode']}}" name="qrcode5_ori">    
                                           <!--  <input type="hidden" value="{{$b002_hq[1]['id']}}" name="b4"> -->
                                            <span class="help-block">Size : {{$b002_hq[1]['max_width']}}px (W) x {{$b002_hq[1]['max_height']}}px (H)</span> 
                                        </td>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b002_hq[2]['file_name']}}" alt="" class="img-responsive">
                                             <br>
                                            <input type="file" name="image6" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode6" class="form-control input-sm" value="{{$b002_hq[2]['qrcode']}}">
                                            <input type="hidden" value="{{$b002_hq[2]['qrcode']}}" name="qrcode6_ori">    
                                            <!-- <input type="hidden" value="{{$b002_hq[2]['id']}}" name="b5"> -->
                                            <span class="help-block">Size : {{$b002_hq[2]['max_width']}}px (W) x {{$b002_hq[2]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                </table></center>
                            </div>
                                
                        </div>
                    </div>
                    <div class="panel panel-default B001_hq_temp" id="B001_hq_temp" style="display:none;">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B003(HQ)</h2>
                        </div>
                        <div class="panel-body">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <div class="col-lg-2 pull-right">
                                        <select class="form-control" name="B003_hq">
                                             @foreach($status as $statuses=>$value)
                                                <option value="{{$statuses}}" <?php if ($statuses == $actives[2]['active_status']) echo 'selected="selected"'?>>{{ ucwords($value)}}</option>
                                                @endforeach
                                        </select>
                                        <input type="hidden" name="B003_hq_ori" value="{{$actives[2]['active_status']}}">
                                    </div>
                                </div>
                                <center><table class="table  table-responsive">
                                    <tr>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b003_hq[0]['file_name']}}" alt="" class="img-responsive">
                                            <br>
                                            <input type="file" name="image7" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode7" class="form-control input-sm" value="{{$b003_hq[0]['qrcode']}}">
                                            <input type="hidden" value="{{$b003_hq[0]['qrcode']}}" name="qrcode7_ori">    
                                            <!-- <input type="hidden" value="{{$b003_hq[0]['id']}}" name="b3"> -->
                                            <span class="help-block">Size : {{$b003_hq[0]['max_width']}}px (W) x {{$b003_hq[0]['max_height']}}px (H)</span> 
                                        </td>
                                        <td rowspan="2" style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b003_hq[2]['file_name']}}" alt="" class="img-responsive">
                                            <br>
                                            <input type="file" name="image8" class="form-control input-sm" >
                                            <br>
                                            <input type="text" name="qrcode8" class="form-control input-sm" value="{{$b003_hq[2]['qrcode']}}">
                                            <input type="hidden" value="{{$b003_hq[2]['qrcode']}}" name="qrcode8_ori">    
                                            <!-- <input type="hidden" value="{{$b003_hq[2]['id']}}" name="b2"> -->
                                            <span class="help-block">Size : {{$b003_hq[2]['max_width']}}px (W) x {{$b003_hq[2]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b003_hq[1]['file_name']}}" alt="" class="img-responsive center-block">
                                            <br>
                                            <input type="file" name="image9" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode9" class="form-control input-sm" value="{{$b003_hq[1]['qrcode']}}">
                                            <input type="hidden" value="{{$b003_hq[1]['qrcode']}}" name="qrcode9_ori">    
                                            <!-- <input type="hidden" value="{{$b003_hq[1]['id']}}" name="b1">  -->

                                            <span class="help-block">Size : {{$b003_hq[1]['max_width']}}px (W) x {{$b003_hq[1]['max_height']}}px (H)</span>      
                                        </td>     
                                    </tr>
                    
                                </table></center>
                            </div>          
                        </div>
                    </div>
                    <div class="panel panel-default" id="B002_hq_temp" style="display:none;">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B004(HQ)</h2>
                        </div>
                        <div class="panel-body">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <div class="col-lg-2 pull-right">
                                        <select class="form-control" name="B004_hq">
                                             @foreach($status as $statuses=>$value)
                                                <option value="{{$statuses}}" <?php if ($statuses == $actives[3]['active_status']) echo 'selected="selected"'?>>{{ ucwords($value)}}</option>
                                                @endforeach
                                        </select>
                                        <input type="hidden" name="B004_hq_ori" value="{{$actives[3]['active_status']}}">
                                    </div>
                                </div>
                               <center><table class="table  table-responsive">
                                    <tr>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b004_hq[0]['file_name']}}" alt="" class="img-responsive" >
                                             <br>
                                            <input type="file" name="image10" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode10" class="form-control input-sm" value="{{$b004_hq[0]['qrcode']}}">
                                            <input type="hidden" value="{{$b004_hq[0]['qrcode']}}" name="qrcode10_ori">    
                                            <!-- <input type="hidden" value="{{$b004_hq[0]['id']}}" name="b4"> -->
                                            <span class="help-block">Size : {{$b004_hq[0]['max_width']}}px (W) x {{$b004_hq[0]['max_height']}}px (H)</span> 
                                        </td>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b004_hq[1]['file_name']}}" alt="" class="img-responsive">
                                             <br>
                                            <input type="file" name="image11" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode11" class="form-control input-sm" value="{{$b004_hq[1]['qrcode']}}">
                                            <input type="hidden" value="{{$b004_hq[1]['qrcode']}}" name="qrcode11_ori">    
                                            <!-- <input type="hidden" value="{{$b004_hq[1]['id']}}" name="b5"> -->
                                            <span class="help-block">Size : {{$b004_hq[1]['max_width']}}px (W) x {{$b004_hq[1]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b004_hq[2]['file_name']}}" alt="" class="img-responsive" >
                                            <br>
                                            <input type="file" name="image12" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode12" class="form-control input-sm" value="{{$b004_hq[2]['qrcode']}}">
                                            <input type="hidden" value="{{$b004_hq[2]['qrcode']}}" name="qrcode12_ori">    
                                            <!-- <input type="hidden" value="{{$b004_hq[2]['id']}}" name="b16"> -->
                                            <span class="help-block">Size : {{$b004_hq[2]['max_width']}}px (W) x {{$b004_hq[2]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                </table></center>
                            </div>
                                
                        </div>
                    </div>
                </div>
                <!-- END HQ-->
                <!-- START JOHOR-->
                <div id="menu1" class="tab-pane fade">
                    <br>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B001(JOHOR)</h2>
                        </div>
                        <div class="panel-body">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <div class="col-lg-2 pull-right">
                                        <select class="form-control" name="B001_jb">
                                            @foreach($status as $statuses=>$value)
                                            <option value="{{$statuses}}" <?php if ($statuses == $actives[4]['active_status']) echo 'selected="selected"'?>>{{ ucwords($value)}}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="B001_jb_ori" value="{{$actives[4]['active_status']}}">
                                    </div>
                                </div>
                                <center><table class="table  table-responsive">
                                    <tr>
                                        <td rowspan="2" style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b001_jb[0]['file_name']}}" alt="" class="img-responsive center-block">
                                            <br>
                                            <input type="file" name="image13" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode13" class="form-control input-sm" value="{{$b001_jb[0]['qrcode']}}">
                                            <input type="hidden" value="{{$b001_jb[0]['qrcode']}}" name="qrcode13_ori">    
                                            <input type="hidden" value="{{$b001_jb[0]['id']}}" name="b6">                               
                                            <span class="help-block">Size : {{$b001_jb[0]['max_width']}}px (W) x {{$b001_jb[0]['max_height']}}px (H)</span>      
                                        </td>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b001_jb[1]['file_name']}}" alt="" class="img-responsive">
                                            <br>
                                            <input type="file" name="image14" class="form-control input-sm" >
                                            <br>
                                            <input type="text" name="qrcode14" class="form-control input-sm" value="{{$b001_jb[1]['qrcode']}}">
                                            <input type="hidden" value="{{$b001_jb[1]['qrcode']}}" name="qrcode14_ori">    
                                            <!-- <input type="hidden" value="{{$b001_jb[1]['id']}}" name="b7"> -->
                                            <span class="help-block">Size : {{$b001_jb[1]['max_width']}}px (W) x {{$b001_jb[1]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b001_jb[2]['file_name']}}" alt="" class="img-responsive">
                                            <br>
                                            <input type="file" name="image15" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode15" class="form-control input-sm" value="{{$b001_jb[2]['qrcode']}}">
                                            <input type="hidden" value="{{$b001_jb[2]['qrcode']}}" name="qrcode15_ori">    
                                            <!-- <input type="hidden" value="{{$b001_jb[2]['id']}}" name="b8"> -->
                                            <span class="help-block">Size : {{$b001_jb[2]['max_width']}}px (W) x {{$b001_jb[2]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                </table></center>
                            </div>          
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B002(JOHOR)</h2>
                        </div>
                        <div class="panel-body">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <div class="col-lg-2 pull-right">
                                        <select class="form-control" name="B002_jb">
                                            @foreach($status as $statuses=>$value)
                                            <option value="{{$statuses}}" <?php if ($statuses == $actives[5]['active_status']) echo 'selected="selected"'?>>{{ ucwords($value)}}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="B002_jb_ori" value="{{$actives[5]['active_status']}}">
                                    </div>
                                </div>
                               <center><table class="table  table-responsive">
                                    <tr>
                                        <td colspan="2" style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b002_jb[0]['file_name']}}" alt="" class="img-responsive" >
                                            <br>
                                            <input type="file" name="image16" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode16" class="form-control input-sm" value="{{$b002_jb[0]['qrcode']}}">
                                            <input type="hidden" value="{{$b002_jb[0]['qrcode']}}" name="qrcode16_ori">    
                                            <!-- <input type="hidden" value="{{$b002_jb[0]['id']}}" name="b16"> -->
                                            <span class="help-block">Size : {{$b002_jb[0]['max_width']}}px (W) x {{$b002_jb[0]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b002_jb[1]['file_name']}}" alt="" class="img-responsive" >
                                             <br>
                                            <input type="file" name="image17" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode17" class="form-control input-sm" value="{{$b002_jb[1]['qrcode']}}">
                                            <input type="hidden" value="{{$b002_jb[1]['qrcode']}}" name="qrcode17_ori">    
                                            <!-- <input type="hidden" value="{{$b002_jb[1]['id']}}" name="b9"> -->
                                            <span class="help-block">Size : {{$b002_jb[1]['max_width']}}px (W) x {{$b002_jb[1]['max_height']}}px (H)</span> 
                                        </td>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b002_jb[2]['file_name']}}" alt="" class="img-responsive">
                                             <br>
                                            <input type="file" name="image18" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode18" class="form-control input-sm" value="{{$b002_jb[2]['qrcode']}}">
                                            <input type="hidden" value="{{$b002_jb[2]['qrcode']}}" name="qrcode18_ori">    
                                            <!-- <input type="hidden" value="{{$b002_jb[2]['id']}}" name="b10"> -->
                                            <span class="help-block">Size : {{$b002_jb[2]['max_width']}}px (W) x {{$b002_jb[2]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                </table></center>
                            </div>
                                
                        </div>
                    </div>
                    <div class="panel panel-default B001_hq_temp" id="B001_hq_temp" style="display:none;">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B003(JOHOR)</h2>
                        </div>
                        <div class="panel-body">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <div class="col-lg-2 pull-right">
                                        <select class="form-control" name="B003_jb">
                                             @foreach($status as $statuses=>$value)
                                                <option value="{{$statuses}}" <?php if ($statuses == $actives[6]['active_status']) echo 'selected="selected"'?>>{{ ucwords($value)}}</option>
                                                @endforeach
                                        </select>
                                        <input type="hidden" name="B003_jb_ori" value="{{$actives[6]['active_status']}}">
                                    </div>
                                </div>
                                <center><table class="table  table-responsive">
                                    <tr>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b003_jb[0]['file_name']}}" alt="" class="img-responsive">
                                            <br>
                                            <input type="file" name="image19" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode19" class="form-control input-sm" value="{{$b003_jb[0]['qrcode']}}">
                                            <input type="hidden" value="{{$b003_jb[0]['qrcode']}}" name="qrcode19_ori">    
                                            <!-- <input type="hidden" value="{{$b003_jb[0]['id']}}" name="b3"> -->
                                            <span class="help-block">Size : {{$b003_jb[0]['max_width']}}px (W) x {{$b003_jb[0]['max_height']}}px (H)</span> 
                                        </td>
                                        <td rowspan="2" style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b003_jb[2]['file_name']}}" alt="" class="img-responsive">
                                            <br>
                                            <input type="file" name="image20" class="form-control input-sm" >
                                            <br>
                                            <input type="text" name="qrcode20" class="form-control input-sm" value="{{$b003_jb[2]['qrcode']}}">
                                            <input type="hidden" value="{{$b003_jb[2]['qrcode']}}" name="qrcode20_ori">    
                                            <!-- <input type="hidden" value="{{$b003_jb[2]['id']}}" name="b2"> -->
                                            <span class="help-block">Size : {{$b003_jb[2]['max_width']}}px (W) x {{$b003_jb[2]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b003_jb[1]['file_name']}}" alt="" class="img-responsive center-block">
                                            <br>
                                            <input type="file" name="image21" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode21" class="form-control input-sm" value="{{$b003_jb[1]['qrcode']}}">
                                            <input type="hidden" value="{{$b003_jb[1]['qrcode']}}" name="qrcode21_ori">    
                                            <!-- <input type="hidden" value="{{$b003_jb[1]['id']}}" name="b1">  -->

                                            <span class="help-block">Size : {{$b003_jb[1]['max_width']}}px (W) x {{$b003_jb[1]['max_height']}}px (H)</span>      
                                        </td>     
                                    </tr>
                    
                                </table></center>
                            </div>          
                        </div>
                    </div>
                    <div class="panel panel-default" id="B002_hq_temp" style="display:none;">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B004(JOHOR)</h2>
                        </div>
                        <div class="panel-body">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <div class="col-lg-2 pull-right">
                                        <select class="form-control" name="B004_jb">
                                             @foreach($status as $statuses=>$value)
                                                <option value="{{$statuses}}" <?php if ($statuses == $actives[7]['active_status']) echo 'selected="selected"'?>>{{ ucwords($value)}}</option>
                                                @endforeach
                                        </select>
                                        <input type="hidden" name="B004_jb_ori" value="{{$actives[7]['active_status']}}">
                                    </div>
                                </div>
                               <center><table class="table  table-responsive">
                                    <tr>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b004_jb[0]['file_name']}}" alt="" class="img-responsive" >
                                             <br>
                                            <input type="file" name="image22" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode22" class="form-control input-sm" value="{{$b004_jb[0]['qrcode']}}">
                                            <input type="hidden" value="{{$b004_jb[0]['qrcode']}}" name="qrcode22_ori">    
                                            <!-- <input type="hidden" value="{{$b004_jb[0]['id']}}" name="b4"> -->
                                            <span class="help-block">Size : {{$b004_jb[0]['max_width']}}px (W) x {{$b004_jb[0]['max_height']}}px (H)</span> 
                                        </td>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b004_jb[1]['file_name']}}" alt="" class="img-responsive">
                                             <br>
                                            <input type="file" name="image23" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode23" class="form-control input-sm" value="{{$b004_jb[1]['qrcode']}}">
                                            <input type="hidden" value="{{$b004_jb[1]['qrcode']}}" name="qrcode23_ori">    
                                            <!-- <input type="hidden" value="{{$b004_jb[1]['id']}}" name="b5"> -->
                                            <span class="help-block">Size : {{$b004_jb[1]['max_width']}}px (W) x {{$b004_jb[1]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b004_jb[2]['file_name']}}" alt="" class="img-responsive" >
                                            <br>
                                            <input type="file" name="image24" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode24" class="form-control input-sm" value="{{$b004_jb[2]['qrcode']}}">
                                            <input type="hidden" value="{{$b004_jb[2]['qrcode']}}" name="qrcode24_ori">    
                                            <!-- <input type="hidden" value="{{$b004_jb[2]['id']}}" name="b16"> -->
                                            <span class="help-block">Size : {{$b004_jb[2]['max_width']}}px (W) x {{$b004_jb[2]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                </table></center>
                            </div>
                                
                        </div>
                    </div>
                </div>
                <!-- END JOHOR-->
                <!-- START PNG-->
                <div id="menu2" class="tab-pane fade">
                    <br>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B001(PNG)</h2>
                        </div>
                        <div class="panel-body">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <div class="col-lg-2 pull-right">
                                        <select class="form-control" name="B001_png">
                                            @foreach($status as $statuses=>$value)
                                            <option value="{{$statuses}}" <?php if ($statuses == $actives[8]['active_status']) echo 'selected="selected"'?>>{{ ucwords($value)}}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="B001_png_ori" value="{{$actives[8]['active_status']}}">
                                    </div>
                                </div>
                                <center><table class="table  table-responsive">
                                    <tr>
                                        <td rowspan="2" style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b001_png[0]['file_name']}}" alt="" class="img-responsive center-block">
                                            <br>
                                            <input type="file" name="image25" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode25" class="form-control input-sm" value="{{$b001_png[0]['qrcode']}}">
                                            <input type="hidden" value="{{$b001_png[0]['qrcode']}}" name="qrcode25_ori">    
                                            <!-- <input type="hidden" value="{{$b001_png[0]['id']}}" name="b11">                                -->
                                            <span class="help-block">Size : {{$b001_png[0]['max_width']}}px (W) x {{$b001_png[0]['max_height']}}px (H)</span>      
                                        </td>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b001_png[1]['file_name']}}" alt="" class="img-responsive">
                                            <br>
                                            <input type="file" name="image26" class="form-control input-sm" >
                                            <br>
                                            <input type="text" name="qrcode26" class="form-control input-sm" value="{{$b001_png[1]['qrcode']}}">
                                            <input type="hidden" value="{{$b001_png[1]['qrcode']}}" name="qrcode26_ori">    
                                            <!-- <input type="hidden" value="{{$b001_png[1]['id']}}" name="b12"> -->
                                            <span class="help-block">Size : {{$b001_png[1]['max_width']}}px (W) x {{$b001_png[1]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b001_png[2]['file_name']}}" alt="" class="img-responsive">
                                            <br>
                                            <input type="file" name="image27" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode27" class="form-control input-sm" value="{{$b001_png[2]['qrcode']}}">
                                            <input type="hidden" value="{{$b001_png[2]['qrcode']}}" name="qrcode27_ori">    
                                            <!-- <input type="hidden" value="{{$b001_png[2]['id']}}" name="b13"> -->
                                            <span class="help-block">Size : {{$b001_png[2]['max_width']}}px (W) x {{$b001_png[2]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                </table></center>
                            </div>          
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B002(PNG)</h2>
                        </div>
                        <div class="panel-body">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <div class="col-lg-2 pull-right">
                                        <select class="form-control" name="B002_png">
                                            @foreach($status as $statuses=>$value)
                                            <option value="{{$statuses}}" <?php if ($statuses == $actives[9]['active_status']) echo 'selected="selected"'?>>{{ ucwords($value)}}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="B002_png_ori" value="{{$actives[9]['active_status']}}">
                                    </div>
                                </div>
                               <center><table class="table  table-responsive">
                                    <tr>
                                        <td colspan="2" style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b002_png[0]['file_name']}}" alt="" class="img-responsive" >
                                            <br>
                                            <input type="file" name="image28" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode28" class="form-control input-sm" value="{{$b002_png[0]['qrcode']}}">
                                            <input type="hidden" value="{{$b002_png[0]['qrcode']}}" name="qrcode28_ori">    
                                            <!-- <input type="hidden" value="{{$b002_png[0]['id']}}" name="b16"> -->
                                            <span class="help-block">Size : {{$b002_png[0]['max_width']}}px (W) x {{$b002_png[0]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b002_png[1]['file_name']}}" alt="" class="img-responsive" >
                                             <br>
                                            <input type="file" name="image29" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode29" class="form-control input-sm" value="{{$b002_png[1]['qrcode']}}">
                                            <input type="hidden" value="{{$b002_png[1]['qrcode']}}" name="qrcode29_ori">    
                                            <!-- <input type="hidden" value="{{$b002_png[1]['id']}}" name="b14"> -->
                                            <span class="help-block">Size : {{$b002_png[1]['max_width']}}px (W) x {{$b002_png[1]['max_height']}}px (H)</span> 
                                        </td>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b002_png[2]['file_name']}}" alt="" class="img-responsive">
                                             <br>
                                            <input type="file" name="image30" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode30" class="form-control input-sm" value="{{$b002_png[2]['qrcode']}}">
                                            <input type="hidden" value="{{$b002_png[2]['qrcode']}}" name="qrcode30_ori">    
                                            <!-- <input type="hidden" value="{{$b002_png[2]['id']}}" name="b15"> -->
                                            <span class="help-block">Size : {{$b002_png[2]['max_width']}}px (W) x {{$b002_png[2]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                </table></center>
                            </div>
                                
                        </div>
                    </div>
                    <div class="panel panel-default B001_hq_temp" id="B001_hq_temp" style="display:none;">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B003(PNG)</h2>
                        </div>
                        <div class="panel-body">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <div class="col-lg-2 pull-right">
                                        <select class="form-control" name="B003_png">
                                             @foreach($status as $statuses=>$value)
                                                <option value="{{$statuses}}" <?php if ($statuses == $actives[10]['active_status']) echo 'selected="selected"'?>>{{ ucwords($value)}}</option>
                                                @endforeach
                                        </select>
                                        <input type="hidden" name="B003_png_ori" value="{{$actives[10]['active_status']}}">
                                    </div>
                                </div>
                                <center><table class="table  table-responsive">
                                    <tr>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b003_png[0]['file_name']}}" alt="" class="img-responsive">
                                            <br>
                                            <input type="file" name="image31" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode31" class="form-control input-sm" value="{{$b003_png[0]['qrcode']}}">
                                            <input type="hidden" value="{{$b003_png[0]['qrcode']}}" name="qrcode31_ori">    
                                            <!-- <input type="hidden" value="{{$b003_png[0]['id']}}" name="b3"> -->
                                            <span class="help-block">Size : {{$b003_png[0]['max_width']}}px (W) x {{$b003_png[0]['max_height']}}px (H)</span> 
                                        </td>
                                        <td rowspan="2" style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b003_png[2]['file_name']}}" alt="" class="img-responsive">
                                            <br>
                                            <input type="file" name="image32" class="form-control input-sm" >
                                            <br>
                                            <input type="text" name="qrcode32" class="form-control input-sm" value="{{$b003_png[2]['qrcode']}}">
                                            <input type="hidden" value="{{$b003_png[2]['qrcode']}}" name="qrcode32_ori">    
                                            <!-- <input type="hidden" value="{{$b003_png[2]['id']}}" name="b2"> -->
                                            <span class="help-block">Size : {{$b003_png[2]['max_width']}}px (W) x {{$b003_png[2]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b003_png[1]['file_name']}}" alt="" class="img-responsive center-block">
                                            <br>
                                            <input type="file" name="image33" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode33" class="form-control input-sm" value="{{$b003_png[1]['qrcode']}}">
                                            <input type="hidden" value="{{$b003_png[1]['qrcode']}}" name="qrcode33_ori">    
                                            <!-- <input type="hidden" value="{{$b003_png[1]['id']}}" name="b1">  -->

                                            <span class="help-block">Size : {{$b003_png[1]['max_width']}}px (W) x {{$b003_png[1]['max_height']}}px (H)</span>      
                                        </td>     
                                    </tr>
                    
                                </table></center>
                            </div>          
                        </div>
                    </div>
                    <div class="panel panel-default" id="B002_hq_temp" style="display:none;">
                        <div class="panel-heading">
                            <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B004(PNG)</h2>
                        </div>
                        <div class="panel-body">
                            <div class="col-lg-12 ">
                                <div class="form-group">
                                    <div class="col-lg-2 pull-right">
                                        <select class="form-control" name="B004_png">
                                             @foreach($status as $statuses=>$value)
                                                <option value="{{$statuses}}" <?php if ($statuses == $actives[11]['active_status']) echo 'selected="selected"'?>>{{ ucwords($value)}}</option>
                                                @endforeach
                                        </select>
                                        <input type="hidden" name="B004_png_ori" value="{{$actives[11]['active_status']}}">
                                    </div>
                                </div>
                               <center><table class="table  table-responsive">
                                    <tr>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b004_png[0]['file_name']}}" alt="" class="img-responsive" >
                                             <br>
                                            <input type="file" name="image34" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode34" class="form-control input-sm" value="{{$b004_png[0]['qrcode']}}">
                                            <input type="hidden" value="{{$b004_png[0]['qrcode']}}" name="qrcode34_ori">    
                                            <!-- <input type="hidden" value="{{$b004_png[0]['id']}}" name="b4"> -->
                                            <span class="help-block">Size : {{$b004_png[0]['max_width']}}px (W) x {{$b004_png[0]['max_height']}}px (H)</span> 
                                        </td>
                                        <td style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b004_png[1]['file_name']}}" alt="" class="img-responsive">
                                             <br>
                                            <input type="file" name="image35" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode35" class="form-control input-sm" value="{{$b004_png[1]['qrcode']}}">
                                            <input type="hidden" value="{{$b004_png[1]['qrcode']}}" name="qrcode35_ori">    
                                            <!-- <input type="hidden" value="{{$b004_png[1]['id']}}" name="b5"> -->
                                            <span class="help-block">Size : {{$b004_png[1]['max_width']}}px (W) x {{$b004_png[1]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="border: 1px solid rgba(128, 128, 128, 0.36);">
                                            <img src="/images/manage_banners/{{$b004_png[2]['file_name']}}" alt="" class="img-responsive" >
                                            <br>
                                            <input type="file" name="image36" class="form-control input-sm">
                                            <br>
                                            <input type="text" name="qrcode36" class="form-control input-sm" value="{{$b004_png[2]['qrcode']}}">
                                            <input type="hidden" value="{{$b004_png[2]['qrcode']}}" name="qrcode36_ori">    
                                            <!-- <input type="hidden" value="{{$b004_png[2]['id']}}" name="b16"> -->
                                            <span class="help-block">Size : {{$b004_png[2]['max_width']}}px (W) x {{$b004_png[2]['max_height']}}px (H)</span> 
                                        </td>
                                    </tr>
                                </table></center>
                            </div>
                                
                        </div>
                    </div>
                </div>
                <!-- END PNG-->
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
@stop
