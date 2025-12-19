@extends('layouts.master')

@section('title') Address Type Keywords Setting @stop

@section('extra-css')
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
    </style>
@endsection

@section('content')
    <div id="page-wrapper">

        <div class="row">
            <nav class="navbar navbar-custom">
              <div class="container-fluid">
                <div class="navbar-header" style="padding-left: 10px">
                  <a href="/sysadmin/address-keywords" class="btn btn-default navbar-btn pull-left"><span class="glyphicon glyphicon-chevron-left"></span> Back</a>
                  <div class="navbar-text navbar-brand-centered"><h3>Edit Address Keyword</h3></div>
                </div>
              </div>
            </nav>
        </div>

        @if(count($errors) > 0 || Session::has('message'))
            <div class="alert alert-{{ !empty(Session::has('message')) ? 'success' : 'warning' }}">
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

        <div class="row">
            <div class="col-md-12">
                <div id="signupbox" style="margin-top:50px" class="mainbox col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="panel-title">Edit</div>
                        </div>
                        <div class="panel-body" >
                            {{ Form::model( $keyword, ['route' => ['keyword.update', $keyword->id], 'method' => 'put', 'role' => 'form', 'class' => "form-horizontal"] ) }}
                                <div class="form-group">
                                    <label for="email" class="col-md-3 control-label">Keyword Title</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" required name="keyword" value="{{ $keyword->title }}" placeholder="Keyword">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="firstname" class="col-md-3 control-label">Type</label>
                                    <div class="col-md-9">
                                        <select name="keyword_type" id="keyword_type" class="form-control">
                                            <option value="office" {{ ($keyword->type == 'office') ? 'selected' : '' }}>Office</option>
                                            <option value="house" {{ ($keyword->type == 'house') ? 'selected' : '' }}>House</option>
                                        </select>
                                    </div>
                                </div>
                                @include('sysadmin.keywords._fields')
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('extra-js')
@endsection