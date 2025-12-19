@extends('layouts.master')

@section('title', 'Add BCard Reward')

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Add BCard Reward</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Add BCard Reward</h3>
                </div>
                <div class="panel-body">
                    {{ Form::open(['url' => "points/bcard/store", 'method'=> 'POST', 'class' => 'form-horizontal']) }}
                        <div class="form-horizontal">
                            <div class="form-group @if ($errors->has('bcard')) has-error @endif">
                                <label class="col-lg-2 control-label">BCard No.</label>
                                <div class="col-lg-3">
                                    {{ Form::text('bcard', Input::old('bcard'), ['class' => 'form-control', 'placeholder' => '6298430000000000']) }}
                                    {{ $errors->first('bcard', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <div class="form-group @if ($errors->has('type')) has-error @endif">
                                <label class="col-lg-2 control-label">Type</label>
                                <div class="col-lg-3">
                                    {{ Form::select('type', ['' => '-', 'transaction' => 'Transaction', 'conversion' => 'Conversion'], Input::old('type'), ['class' => 'form-control', 'id' => 'type']) }}
                                    {{ $errors->first('type', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <div class="transaction @if (Input::old('type') != 'transaction') hide @endif form-group @if ($errors->has('transaction_id')) has-error @endif">
                                <label class="col-lg-2 control-label">Transaction ID</label>
                                <div class="col-lg-3">
                                    {{ Form::text('transaction_id', Input::old('transaction_id'), ['class' => 'form-control']) }}
                                    {{ $errors->first('transaction_id', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <div class="conversion @if (Input::old('type') != 'conversion') hide @endif form-group @if ($errors->has('customer')) has-error @endif">
                                <label class="col-lg-2 control-label">Customer</label>
                                <div class="col-lg-3">
                                    {{ Form::text('customer', Input::old('customer'), ['autocomplete' => 'off', 'class' => 'form-control', 'id' => 'customer']) }}
                                    <div id="customerAutoComplete" class="list-group autocomplete"></div>
                                    {{ $errors->first('customer', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <div class="conversion @if (Input::old('type') != 'conversion') hide @endif form-group @if ($errors->has('jpoint')) has-error @endif">
                                <label class="col-lg-2 control-label">JPoint</label>
                                <div class="col-lg-3">
                                    {{ Form::text('jpoint', Input::old('jpoint'), ['class' => 'form-control']) }}
                                    {{ $errors->first('jpoint', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-3 col-lg-offset-2">
                                    <input class="btn btn-default" type="reset" value="Reset">
                                    @if (Permission::CheckAccessLevel(Session::get('role_id'), 2, 3, 'AND'))
                                        <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('script')
    var timer;

    $('#customer').keydown(function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            if ($('#customer').val()) {
                $.ajax({
                    url: '{{ url('report/customersearch?keyword=') }}' + $('#customer').val() + '&limit=10',
                    success: function (result) {
                        if ($('#customer').is(":focus")) {
                            var candidates = $.parseJSON(result);
                            var size = 0;

                            $('#customerAutoComplete').html('');

                            $.each(candidates, function (i, candidate) {
                                var customerName = candidate.full_name;

                                if (customerName.search($('#customer').val())) {
                                    var clickAction = "$('#customerAutoComplete').html(''); $('#customer').val('" + candidate.username + "'); return false;";

                                    $('#customerAutoComplete').append('<a href="#" onclick="' + clickAction + '" class="list-group-item">' + candidate.full_name + ' ('  + candidate.username + ')' + '</a>');
                                    size++;
                                }
                            });

                            if ($('#customer').is(':focus') && size > 0) {
                                $('#customerAutoComplete').show();
                            }
                        }
                    }
                });
            } else {
                $('#customerAutoComplete').html('');
            }
        }, 200);
    });

    $('html').click(function () {
        $('#customerAutoComplete').html('').hide();
    });

    $('#type').change(function () {
        var type = $(this).val();

        switch (type) {
            case 'transaction':
                $('.conversion').addClass('hide');
                $('.transaction').removeClass('hide');
                break;
            case 'conversion':
                $('.transaction').addClass('hide');
                $('.conversion').removeClass('hide');
                break;
            default:
                $('.conversion').addClass('hide');
                $('.transaction').addClass('hide');
                break;
        }
    });

    var type = $('#type').val();

    switch (type) {
        case 'transaction':
            $('.conversion').addClass('hide');
            $('.transaction').removeClass('hide');
            break;
        case 'conversion':
            $('.transaction').addClass('hide');
            $('.conversion').removeClass('hide');
            break;
        default:
            $('.conversion').addClass('hide');
            $('.transaction').addClass('hide');
            break;
    }
@stop
