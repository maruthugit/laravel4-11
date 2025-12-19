<?php use Helper\ImageHelper as Image; ?>
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
    <?php 
//    
//    echo "<pre>";
//    print_r($popup);
//    echo "</pre>";
    
    ?>
	{{ Form::open(array('url' => 'banner/savepopup/' , 'class' => 'form-horizontal', 'files' => true)) }}
        <input type="hidden" name="id" value="<?php echo $popup->id;?>">
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
                                    <option value="<?php echo $value->id; ?>" <?php if($popup->country_id == $value->id){ echo "selected";} ?>><?php echo $value->name;?></option>
                                <?php } ?>
                            </select>
                            <select class="form-control" id="region_id" name="region_id" style="margin-top: 10px;">
                            	<?php if(Session::get('branch_access') != 1){?>
                                <option value="">All region</option>
                                <?php } ?>
                                <?php foreach ($regions as $key => $value)  { ?>
                                    <option value="<?php echo $value->id; ?>" <?php if($value->id == $popup->region_id) { echo "selected";} ?>><?php echo $value->region; ?></option>
                                <?php  } ?>
                            </select>
						</div>
                     
				</div>
                                <h4 class="ui horizontal divider header"><i class="tag icon"></i>Banner Information</h4>
                                <div class='form-group'>
                                    
				{{ Form::label('description', 'Title Description', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-3">
					{{ Form::text('description', $popup->description, array('placeholder' => '', 'class' => 'form-control') ) }}
					</div>
				</div>
				<div class='form-group'>
                                    
				{{ Form::label('qrcode', 'QR Code', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-3">
					{{ Form::text('qrcode', $popup->qr_code, array('placeholder' => 'Ex: JC2110 or JC1000, JC2000, JC3000', 'class' => 'form-control') ) }}
					<p class="text-info">* Related product.</p>
					</div>
                                {{ Form::label('category_id', 'Category ID', array('class' => 'col-lg-2 control-label')) }}
					<div class="col-lg-3">
					{{ Form::text('category_id',  $popup->category_id, array('placeholder' => 'Ex: 288', 'class' => 'form-control') ) }}
					<p class="text-info">* Category ID. Enter only one category ID</p>
					</div>
				</div>
                              
                                <div class='form-group'>
                                    <label for="effective_date" class="col-lg-2 control-label">Effective From Date</label>
                                    <div class="col-lg-3">
                                        <div class="input-group" id="datetimepicker_from">
                                            <input id="from_date" class="from_date form-control" tabindex="1" value="<?php  echo $popup->from_date; ?>" name="from_date" type="text" value="">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                    
                                     <label for="effective_date" class="col-lg-2 control-label">Effective To Date</label>
                                    <div class="col-lg-3">
                                        <div class="input-group" id="datetimepicker_to">
                                            <input id="from_date" class="from_date form-control" tabindex="1" value="<?php  echo $popup->to_date; ?>" name="to_date" type="text" value="">
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
                                            <input type="hidden" id="image_rmv_1" name="image_rmv[]" class="form-control" placeholder="" aria-describedby="basic-addon1">
                                            <?php if($popup->image_1 != ''){ ?>
                                            <span class="input-group-addon view-img" id="basic-addon1" img-src="<?php echo Image::link($path. $popup->image_1) ; ?>"><i class="fa fa-image"></i></span>
<!--                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button"><i class="fa fa-trash-o"></i></button>
                                            </span>-->
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label for="effective_date" class="col-lg-2 control-label"></label>
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon1">Image 2</span>
                                            <input type="file" id="image_2" name="image_2" class="form-control" placeholder="" aria-describedby="basic-addon1">
                                            <input type="hidden" id="image_rmv_2" name="image_rmv[]" class="form-control" placeholder="" aria-describedby="basic-addon1">
                                            <?php if($popup->image_2 != ''){ ?>
                                            <span class="input-group-addon view-img" id="basic-addon1" img-src="<?php echo Image::link($path. $popup->image_2) ; ?>"><i class="fa fa-image"></i></span>
                                            <span class="input-group-btn">
                                                <button class="btn btn-default btn-remove" type="button" pos="2" status="1"><i class="fa fa-trash-o"></i></button>
                                            </span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label for="effective_date" class="col-lg-2 control-label"></label>
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon1">Image 3</span>
                                            <input type="file" id="image_3" name="image_3" class="form-control" placeholder="" aria-describedby="basic-addon1">
                                            <input type="hidden" id="image_rmv_3" name="image_rmv[]" class="form-control" placeholder="" aria-describedby="basic-addon1">
                                            <?php if($popup->image_3 != ''){ ?>
                                            <span class="input-group-addon view-img" id="basic-addon1" img-src="<?php echo Image::link($path. $popup->image_3) ; ?>"><i class="fa fa-image"></i></span>
                                            <span class="input-group-btn">
                                                <button class="btn btn-default btn-remove" type="button" pos="3" status="1"><i class="fa fa-trash-o"></i></button>
                                            </span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label for="effective_date" class="col-lg-2 control-label"></label>
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon1">Image 4</span>
                                            <input type="file" id="image_4" name="image_4" class="form-control" placeholder="" aria-describedby="basic-addon1">
                                            <input type="hidden" id="image_rmv_4" name="image_rmv[]" class="form-control" placeholder="" aria-describedby="basic-addon1">
                                            <?php if($popup->image_4 != ''){ ?>
                                            <span class="input-group-addon view-img" id="basic-addon1" img-src="<?php echo Image::link($path. $popup->image_4) ; ?>"><i class="fa fa-image"></i></span>
                                            <span class="input-group-btn">
                                                <button class="btn btn-default btn-remove" type="button" pos="4" status="1"><i class="fa fa-trash-o"></i></button>
                                            </span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label for="effective_date" class="col-lg-2 control-label"></label>
                                    <div class="col-lg-3">
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon1">Image 5</span>
                                            <input type="file" id="image_5" name="image_5" class="form-control" placeholder="" aria-describedby="basic-addon1">
                                            <input type="hidden" id="image_rmv_5" name="image_rmv[]" class="form-control" placeholder="" aria-describedby="basic-addon1">
                                            <?php if($popup->image_5 != ''){ ?>
                                            <span class="input-group-addon view-img" id="basic-addon1" img-src="<?php echo Image::link($path. $popup->image_5) ; ?>"><i class="fa fa-image"></i></span>
                                            <span class="input-group-btn">
                                                <button class="btn btn-default btn-remove" type="button" pos="5" status="1"><i class="fa fa-trash-o"></i></button>
                                            </span>
                                            <?php } ?>
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

	
<div  class="modal fade" id="vm1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog " role="document">
    <div class="modal-content">
       
        <div class="modal-body" style="padding: 0px;">
            <img id="vm1-img" style="width:100%; height:450px;" src="http://uat.all.jocom.com.my/images/popup_banners/2_image1.png">
        </div>
        <div class="modal-footer" style="background-color: #efefef;">
            
            <!-- /input-group -->
            <button type="button" class="btn btn-default" data-dismiss="modal" style="width: 100px;">Close</button>
        </div>
        
    </div>
  </div>
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
        
        $(".view-img").click(function(){
            var source = $(this);
            $("#vm1-img").attr("src",$(this).attr("img-src"))
            $("#vm1").modal('show');
        });
        
        $(".btn-remove").click(function(){
            
            if($(this).attr("status") == 1){
                $(this).removeClass('btn-default');
                $(this).addClass('btn-danger');  //
                var newstatus = 2;
                $("#image_"+$(this).attr("pos")).attr('disabled','disabled');
                $("#image_rmv_"+$(this).attr("pos")).val($(this).attr("pos"));
            }
//            
            if($(this).attr("status") == 2){
                $(this).removeClass('btn-danger');
                $(this).addClass('btn-default');
                var newstatus = 1;
                $("#image_"+$(this).attr("pos")).removeAttr('disabled');
                $("#image_rmv_"+$(this).attr("pos")).val('');
            }
            
            $(this).attr("status",newstatus)
            console.log($(this));
        })
        
       $('[data-toggle="popover"]').popover();
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

