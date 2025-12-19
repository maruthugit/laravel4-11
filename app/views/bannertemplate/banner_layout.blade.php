@extends('layouts.master')

@section('title') Banner Layout @stop

@section('content')


<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<style>
     .loading {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999999;
        /*background: #3d464d;*/
        background: #FFF;
        opacity: 1.00;
        display: none;
        }
    .loading #load-message {
        width: 40px;
        height: 40px;
        position: absolute;
        left: 50%;
        right: 50%;
        bottom: 50%;
        top: 50%;
        margin: -20px;
    }
</style>

<div class="loading"><span id="load-message"></span></div>
<div id="page-wrapper">
    @if ($errors->any())
        {{ implode('', $errors->all('<div class=\'bg-danger alert\'>:message</div>')) }}
    @endif

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

            <h1 class="page-header">Banner Management</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner Details</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12 ">
                <div class="form-group">
                    {{ Form::label('banner_region', 'Region', ['class' => 'col-lg-2 control-label']) }}
                        <div class="col-lg-3">
                            <select class="form-control" name="region_country_id" id="region_country_id">
                                <?php foreach($regions as $region){?>
                                    <option value="<?php echo $region->id; ?>"><?php echo ucwords($region->region); ?></option>
                                <?php } ?>
                            </select>         
                        </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-image"></i> Layouts </h2>
        </div>
        <div class="panel-body test1">
            <div class="col-lg-12">
                <div class="col-lg-2">
                    <div>
                        <center><label>B001</label></center>
                    </div>
                    <div class="element1">
                        <img src="/images/asset/banner_layout/layout1.png" class="img-thumbnail"><br><br>
                    </div>
                    <a class="btn btn-primary clone1"><i class="fa fa-plus" aria-hidden="true"></i> Add</a>
                    <a class="btn btn-danger remove1">Remove</a>
                </div>
                <div class="col-lg-2">
                    <div>
                        <center><label>B002</label></center>
                    </div>
                    <div class="element2">
                        <img src="/images/asset/banner_layout/layout2.png" class="img-thumbnail"><br><br>
                    </div>
                    <a class="btn btn-primary clone2"><i class="fa fa-plus" aria-hidden="true"></i> Add</a>
                    <a class="btn btn-danger remove2">Remove</a>
                </div>
                <div class="col-lg-2">
                    <div>
                        <center><label>B003</label></center>
                    </div>
                    <div class="element3">
                        <img src="/images/asset/banner_layout/layout3.png" class="img-thumbnail"><br><br>
                    </div>
                    <a class="btn btn-primary clone3"><i class="fa fa-plus" aria-hidden="true"></i> Add</a>
                    <a class="btn btn-danger remove3">Remove</a>
                </div>
                <div class="col-lg-2">
                    <div>
                        <center><label>B004</label></center>
                    </div>
                    <div class="element4">
                        <img src="/images/asset/banner_layout/layout4.png" class="img-thumbnail"><br><br>
                    </div>
                    <a class="btn btn-primary clone4"><i class="fa fa-plus" aria-hidden="true"></i> Add</a>
                    <a class="btn btn-danger remove4">Remove</a>
                </div>
                <div class="col-lg-2">
                    <div>
                        <center><label>B005</label></center>
                    </div>
                    <div class="element5">
                        <img src="/images/asset/banner_layout/layout5.png" class="img-thumbnail"><br><br>
                    </div>
                    <a class="btn btn-primary clone5"><i class="fa fa-plus" aria-hidden="true"></i> Add</a>
                    <a class="btn btn-danger remove5">Remove</a>
                </div>
            </div> 

            <div class="row">
                <div class="col-lg-12">
                <hr>
                    <div class="col-lg-3 results">
                    </div>
                </div> 
            </div>
             
            <div class='form-group'>
                <div class="col-lg-10">
                    <button type="button" class="btn btn-default" data-toggle="tooltip">Reset</button>
                    <button type="button" id="save" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>           
    </div>  
</div>

</div>
<script>
    //BOO1
    $('.test1').on('click', '.remove1', function() {
    $('.remove1').closest('.test1').find('.element1').not(':first').last().remove();
    });
    $('.test1').on('click', '.clone1', function() {
        $('.clone1').closest('.test1').find('.element1').first().clone().appendTo('.results').append('Seq : <input type="text" class="form-control" id="seq" data-typetest="B001" required></input><br>');
    });
    //BOO2
    $('.test1').on('click', '.remove2', function() {
    $('.remove2').closest('.test1').find('.element2').not(':first').last().remove();
    });
    $('.test1').on('click', '.clone2', function() {
        $('.clone2').closest('.test1').find('.element2').first().clone().appendTo('.results').append('Seq : <input type="text" class="form-control" id="seq" data-typetest="B002" required></input><br>');
    });

    //BOO3
    $('.test1').on('click', '.remove3', function() {
    $('.remove3').closest('.test1').find('.element3').not(':first').last().remove();
    });
    $('.test1').on('click', '.clone3', function() {
        $('.clone3').closest('.test1').find('.element3').first().clone().appendTo('.results').append('Seq : <input type="text" class="form-control" id="seq" data-typetest="B003" required></input><br>');
    });

    //BOO4
    $('.test1').on('click', '.remove4', function() {
    $('.remove4').closest('.test1').find('.element4').not(':first').last().remove();
    });
    $('.test1').on('click', '.clone4', function() {
        $('.clone4').closest('.test1').find('.element4').first().clone().appendTo('.results').append('Seq : <input type="text" class="form-control" id="seq" data-typetest="B004" required></input><br>');
    });

    //BOO5
    $('.test1').on('click', '.remove5', function() {
    $('.remove5').closest('.test1').find('.element5').not(':first').last().remove();
    });
    $('.test1').on('click', '.clone5', function() {
        $('.clone5').closest('.test1').find('.element5').first().clone().appendTo('.results').append('Seq : <input type="text" class="form-control" id="seq" data-typetest="B005" required></input><br>');
    });
</script>

<script>

    $(document).ready(function() {    

        $("#save").click(function(){

            var values = $("input[id='seq']").map(function(){
                var type = $(this).val();
                var type2 = $(this).data('typetest');
                return [type,type2];
            }).get();

            var region = $('#region_country_id').find(":selected").val();

            $.ajax({
                method: "POST",
                url: "/bannertemplate/layoutupdate",
                data: {
                    'region_id':region,
                    'type': values
                },
                beforeSend: function() {
                  $('.loading').show();
                },
                success: function(data) {
                    $('.loading').hide();

                    // jQuery(jQuery.find('.alert-success')).appendTo('alert-success').append('Banner Updated Successfully');

                    alert('Banner Added Successfully!');
                    location.reload();
                },
            })
        });
    });
</script>

@stop
