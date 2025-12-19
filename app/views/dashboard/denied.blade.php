@extends('layouts.default')

@section('title') Access Denied @stop

@section('content')

<div class='col-md-8 col-md-offset-2'>
    <div class='panel panel-danger'>
         @if ($errors->has())
            @foreach ($errors->all() as $error)
                <div class="panel panel-danger">
                    <div class="panel-heading"> {{ $error }} </div>
                </div>
            @endforeach
        @endif

        <!-- Success-Messages -->
        @if ($message = Session::get('success'))
            <div class="panel panel-danger">
                <div class="panel-heading"> {{ $message }} </div>
            </div>
        @endif
            
        <div class="panel-heading">
            <h1><i class='fa fa-ban'></i> Access Denied</h1>
        </div>
        <div class="panel-body">
            <div class='col-md-8 col-md-offset-2'>
                <div class="form-group">
                  <p><h3> Sorry, you do not have the permission to access '{{ $module }} Module'. </h3></p>
                </div>
            </div>
        </div>
    </div>
</div>

@stop