@extends('layouts.master')

@section('title') JocomMy Layout @stop

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

            <h1 class="page-header">JocomMy Layout Management</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-image"></i> Layouts </h2>
        </div>
        <div class="panel-body test1">
            <div class="col-lg-12">
                <div class="col-lg-3">
                    <div>
                        <center><label>JocomMy</label></center>
                    </div>
                    <div class="element1">
                        <img src="/images/data/thumbs/layout6.png" class="img-thumbnail"><br><br>
                    </div>
                    <a class="btn btn-primary clone1"><i class="fa fa-plus" aria-hidden="true"></i> Add</a>
                    <a class="btn btn-danger remove1">Remove</a>
                </div>
                <div class="col-lg-3">
                    <div>
                        <center><label>Festive Combo</label></center>
                    </div>
                    <div class="element2">
                        <img src="/images/data/thumbs/layout5.png" class="img-thumbnail"><br><br>
                    </div>
                    <a class="btn btn-primary clone2"><i class="fa fa-plus" aria-hidden="true"></i> Add</a>
                    <a class="btn btn-danger remove2">Remove</a>
                </div>
                <div class="col-lg-3">
                    <div>
                        <center><label>Cross Border</label></center>
                    </div>
                    <div class="element3">
                        <img src="/images/data/thumbs/layout5.png" class="img-thumbnail"><br><br>
                    </div>
                    <a class="btn btn-primary clone3"><i class="fa fa-plus" aria-hidden="true"></i> Add</a>
                    <a class="btn btn-danger remove3">Remove</a>
                </div>
                <div class="col-lg-3">
                    <div>
                        <center><label>Jocom Voucher</label></center>
                    </div>
                    <div class="element4">
                        <img src="/images/data/thumbs/layout5.png" class="img-thumbnail"><br><br>
                    </div>
                    <a class="btn btn-primary clone4"><i class="fa fa-plus" aria-hidden="true"></i> Add</a>
                    <a class="btn btn-danger remove4">Remove</a>
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
        $('.clone1').closest('.test1').find('.element1').first().clone().appendTo('.results').append('Seq : <input type="text" class="form-control" id="seq" data-typetest="1" required></input><br>');
    });
    //BOO2
    $('.test1').on('click', '.remove2', function() {
    $('.remove2').closest('.test1').find('.element2').not(':first').last().remove();
    });
    $('.test1').on('click', '.clone2', function() {
        $('.clone2').closest('.test1').find('.element2').first().clone().appendTo('.results').append('Seq : <input type="text" class="form-control" id="seq" data-typetest="2" required></input><br>');
    });
    //BOO3
    $('.test1').on('click', '.remove3', function() {
    $('.remove3').closest('.test1').find('.element3').not(':first').last().remove();
    });
    $('.test1').on('click', '.clone3', function() {
        $('.clone3').closest('.test1').find('.element3').first().clone().appendTo('.results').append('Seq : <input type="text" class="form-control" id="seq" data-typetest="3" required></input><br>');
    });
    //BOO4
    $('.test1').on('click', '.remove4', function() {
    $('.remove4').closest('.test1').find('.element4').not(':first').last().remove();
    });
    $('.test1').on('click', '.clone4', function() {
        $('.clone4').closest('.test1').find('.element4').first().clone().appendTo('.results').append('Seq : <input type="text" class="form-control" id="seq" data-typetest="4" required></input><br>');
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

            $.ajax({
                method: "POST",
                url: "/jocommy/layoutupdate",
                data: {
                    'type': values
                },
                beforeSend: function() {
                  $('.loading').show();
                },
                success: function(data) {
                    $('.loading').hide();

                    alert('Banner Added Successfully!');
                    location.reload();
                },
            })
        });
    });
</script>

@stop
