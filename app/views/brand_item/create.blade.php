@extends('layouts.master')

@section('title') Brands @stop

@section('content')
<div id="page-wrapper">
    @if ($errors->any())
        {{ implode('', $errors->all('<div class=\'bg-danger alert\'>:message</div>')) }}
    @endif
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><i class="fa fa-comments-o"></i> Brands Management 
                <!-- <span class="pull-right"><a class="btn btn-default" title="" data-toggle="tooltip" href="/hot_item"><i class="fa fa-reply"></i></a></span> -->
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        {{ Form::open(array('url' => 'brands/store', 'class' => 'form-horizontal', 'files'=> true)) }}

        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i>Brand Details</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
<!--                        <div class='form-group'>
                            {{ Form::label('Pltaform', 'platform', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-6">
                                <select class="form-control" id="platform" name="platform" style="margin-top: 10px;">
                                    <option value="JOC" >JOCOM</option>
                                    <option value="JUE" >JUEPIN</option>
                                </select>
                            </div>
                        </div>-->
                        <div class="form-group">
					{{ Form::label('banner_region', 'Region', ['class' => 'col-lg-2 control-label']) }}
						<div class="col-lg-3">
							<select class="form-control" id="region_country_id" name="region_country_id">
                                <?php foreach ($countries as $key => $value) { ?>
                                    <option value="<?php echo $value->id; ?>" <?php if($banner->region_country_id == $value->id){ echo "selected";} ?>><?php echo $value->name;?></option>
                                <?php } ?>
                            </select>
                            <select class="form-control" id="region_id" name="region_id" style="margin-top: 10px;">
                            	<?php if(Session::get('branch_access') != 1){?>
                            	<option value="">All region</option>
                                <?php } ?>
                                <?php foreach ($regions as $key => $value)  { ?>
                                    <option value="<?php echo $value->id; ?>" <?php if($value->id == $banner->region_id) { echo "selected";} ?>><?php echo $value->region; ?></option>
                                <?php  } ?>
                            </select>
						</div>
						</div>   
                        <div class='form-group'>
                        {{ Form::label('qrcode', 'QR Code', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-6">
                            {{ Form::text('qrcode', null, array('placeholder' => 'e.g. JC2110 or JC1000, JC2000, JC3000', 'class' => 'form-control') ) }}
                            <p class="text-info">* (blank): It will shows the Brand Image.<br>* Otherwise, it will shows the list of products based on the numbers of QR Code entered.</p>
                            </div>
                        </div> 
                        <div class='form-group'>
                            {{ Form::label('pos', 'Position', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                            {{ Form::text('pos','', array('class' => 'form-control') ) }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-image"></i> Phone Images</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12 table-responsive">
                        <table class="table table-hover" style="text-align:center;">
                            <thead>
                                <tr>
                                    <th>Languages</th>
                                    <th style="text-align:center;">Actual Images</th>
                                    <th style="text-align:center;">Thumbnails</th>
                                </tr>
                            </thead>
                            <tbody>
                        
                            @foreach ($language as $code => $name)
                            <tr>
                                @if($code == "en")
                                <td>{{ Form::label('brand_item', '* '.$name, array('class' => 'col-lg-3 control-label')) }}</td>
                                @else
                                <td>{{ Form::label('brand_item', $name, array('class' => 'col-lg-3 control-label')) }}</td>
                                @endif
                                <td>
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 320px; line-height: 20px;"></div>
                                        <div>
                                        <div style="color:#428bca"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span><br><br></div>
                                        <span class="btn btn-default btn-file">
                                            <span class="fileinput-new"><i class="fa fa-folder-open"></i> Browse</span>
                                            <span class="fileinput-exists"><i class="fa fa-folder-open"></i> Change</span>
                                            <input type="file" name="branditem_{{$code}}" id="branditem_{{$code}}" />
                                        </span>
                                        <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash-o"></i> Remove</a>
                                      </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                                            <div>
                                                <div style="color:#428bca"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span><br><br></div>
                                                <span class="btn btn-default btn-file" onchange="get_file_info(this)">
                                                    <span class="fileinput-new"><i class="fa fa-folder-open"></i> Browse</span>
                                                    <span class="fileinput-exists"><i class="fa fa-folder-open"></i> Change</span>
                                                    <input type="file" name="branditem_thumb_{{$code}}" id="branditem_thumb_{{$code}}" />
                                                </span>
                                                <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash-o"></i> Remove</a>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-image"></i> Tablet Images</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12 table-responsive">
                        <table class="table table-hover" style="text-align:center;">
                            <thead>
                                <tr>
                                    <th>Languages</th>
                                    <th style="text-align:center;">Actual Images</th>
                                    <th style="text-align:center;">Thumbnails</th>
                                </tr>
                            </thead>
                            <tbody>
                        
                            @foreach ($language as $code => $name)
                            <tr>
                                @if($code == "en")
                                <td>{{ Form::label('brand_item', '* '.$name, array('class' => 'col-lg-3 control-label')) }}</td>
                                @else
                                <td>{{ Form::label('brand_item', $name, array('class' => 'col-lg-3 control-label')) }}</td>
                                @endif
                                <td>
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 320px; line-height: 20px;"></div>
                                        <div>
                                        <div style="color:#428bca"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span><br><br></div>
                                        <span class="btn btn-default btn-file">
                                            <span class="fileinput-new"><i class="fa fa-folder-open"></i> Browse</span>
                                            <span class="fileinput-exists"><i class="fa fa-folder-open"></i> Change</span>
                                            <input type="file" name="branditem_tab_{{$code}}" id="branditem_tab_{{$code}}" />
                                        </span>
                                        <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash-o"></i> Remove</a>
                                      </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                                            <div>
                                                <div style="color:#428bca"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span><br><br></div>
                                                <span class="btn btn-default btn-file" onchange="get_file_info(this)">
                                                    <span class="fileinput-new"><i class="fa fa-folder-open"></i> Browse</span>
                                                    <span class="fileinput-exists"><i class="fa fa-folder-open"></i> Change</span>
                                                    <input type="file" name="branditem_tab_thumb_{{$code}}" id="branditem_tab_thumb_{{$code}}" />
                                                </span>
                                                <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput"><i class="fa fa-trash-o"></i> Remove</a>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
            <div class="form-group">
                <div class="col-lg-10">
                   <!--  <a class="btn btn-default" href="/hot_item"><i class="fa fa-reply"></i> Cancel</a> -->
                    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 7, 5, 'AND'))
                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                    <button id="buttonSave" type="submit" value="Save" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- /.col-lg-12 -->
    </div>
    {{ Form::close() }}
    <!-- /.row -->  
@stop

@section('inputjs')
{{ HTML::script('js/fileinput.min.js') }}
<script>
$(document).ready(function() {
    
    $('body').on('change', '#region_country_id', function() {
        loadOptionRegion($(this).val());
    });
    
    function loadOptionRegion(countryID){
        
        $.ajax({
                method: "POST",
                url: "/region/country",
                dataType:'json',
                data: {
                    'country_id':countryID
                },
                beforeSend: function(){
                },
                success: function(data) {
                    console.log(data.data.region);
                    var regionList = data.data.region;
                    var str = '<option value="0">All Region</option>';
                    $.each(regionList, function (index, value) {
                        str = str + "<option value='"+value.id+"'>"+value.region+"</option>";
                       console.log(str);
                    });
                    $("#region_id").html(str);
                    
                }
          })
        
    }
});
</script>

@stop
