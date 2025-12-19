@extends('layouts.master')
@section('title') Popup Banner @stop
@section('content')
<div id="page-wrapper">
	<div class="row">
            <div class="col-lg-12">
                @if (Session::get('message') != '')
                    <div class='<?php echo Session::get('success') == "1" ? 'alert-success' : 'alert-danger'?> alert'>{{Session::get('message')}}</div>
                @endif
                <h1 class="page-header">Popup Banner</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>

	{{ Form::open(array('url' => 'banner/savepopup/' , 'class' => 'form-horizontal', 'files' => true)) }}

	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="panel-title"><i class="fa fa-pencil"></i> Popup Details</h2>
        </div>
		<div class="panel-body">
			<div class="col-lg-12 ">
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
                                <h4 class="ui horizontal divider header"><i class="tag icon"></i>Banner Information</h4>
                                <div class='form-group'>
                                    
				{{ Form::label('description', 'Title Description', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-3">
					{{ Form::text('description', null, array('placeholder' => '', 'class' => 'form-control') ) }}
					</div>
				</div>
				<div class='form-group'>
                                    
				{{ Form::label('qrcode', 'QR Code', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-3">
					{{ Form::text('qrcode', null, array('placeholder' => 'Ex: JC2110 or JC1000, JC2000, JC3000', 'class' => 'form-control') ) }}
					<p class="text-info">* Related product.</p>
					</div>
                                {{ Form::label('category_id', 'Category ID', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-3">
					{{ Form::text('category_id', null, array('placeholder' => 'Ex: 288', 'class' => 'form-control') ) }}
					<p class="text-info">* Category ID. Enter only one category ID</p>
					</div>
				</div>
                              
                                <div class='form-group'>
                                    <label for="effective_date" class="col-lg-2 control-label">Effective From Date</label>
                                    <div class="col-lg-3">
                                        <div class="input-group" id="datetimepicker_from">
                                            <input id="from_date" class="from_date form-control" tabindex="1" name="from_date" type="text" value="">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                    
                                     <label for="effective_date" class="col-lg-2 control-label">Effective To Date</label>
                                    <div class="col-lg-3">
                                        <div class="input-group" id="datetimepicker_to">
                                            <input id="from_date" class="from_date form-control" tabindex="1" name="to_date" type="text" value="">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                    </div>
				</div>
                                <h4 class="ui horizontal divider header"><i class="tag icon"></i><i class="fa fa-image"></i> Banner Upload</h4>
                                <div class='form-group'>
                                    <label for="effective_date" class="col-lg-2 control-label">Popup Banner Image</label>
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon1">Image 1</span>
                                            <input type="file" name="image_1" class="form-control" placeholder="" aria-describedby="basic-addon1">
                                        </div>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label for="effective_date" class="col-lg-2 control-label"></label>
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon1">Image 2</span>
                                            <input type="file" name="image_2" class="form-control" placeholder="" aria-describedby="basic-addon1">
                                        </div>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label for="effective_date" class="col-lg-2 control-label"></label>
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon1">Image 3</span>
                                            <input type="file" name="image_3" class="form-control" placeholder="" aria-describedby="basic-addon1">
                                        </div>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label for="effective_date" class="col-lg-2 control-label"></label>
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon1">Image 4</span>
                                            <input type="file" name="image_4" class="form-control" placeholder="" aria-describedby="basic-addon1">
                                        </div>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label for="effective_date" class="col-lg-2 control-label"></label>
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon1">Image 5</span>
                                            <input type="file" name="image_5" class="form-control" placeholder="" aria-describedby="basic-addon1">
                                        </div>
                                    </div>
                                </div>
                                    
                            </div>
                           
                              
			</div>
            
		</div>
        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 5, 5, 'AND'))
	<div class='form-group'>
		<div class="col-lg-10">
			{{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
			{{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
		</div>
	</div>
	@endif
	{{ Form::close() }}
	</div>

	

</div>
@stop

@section('inputjs')
{{ HTML::script('js/fileinput.min.js') }}
<script>
$(document).ready(function() {
    
    $('body').on('change', '#region_country_id', function() {
        loadOptionRegion($(this).val());
    });
    
    $(function() {
        $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
            format: 'YYYY-MM-DD'
        });
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

