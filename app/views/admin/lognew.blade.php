@extends('layouts.master')
@section('title', 'Log') @stop
@section('content')

<!-- <script src="//code.jquery.com/jquery-1.10.2.js"></script> -->

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">LOG
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}search"><i class="fa fa-refresh"></i></a>
                </span>
            </h1>
        </div>
    </div>
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

            {{ Form::open(['role' => 'form', 'url' => '/transaction/lognewimport', 'class' => 'form-horizontal', 'files' => true]) }}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-list"></i> Upload Details</h2>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        <div class='form-group'>
                            <label class="col-lg-2">Upload Files</label>
                            <div class="col-lg-6">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">Select file</span>
                                        <span class="fileinput-exists">Change</span>
                                        <input type="file" name="csv" id="csv">
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                </div>
                                <p class="text-danger">* Save the file as <b>"Windows Comma Seperated (.csv)"</b> before import the CSV.</p>
                            </div>
                          
                        </div>
                        <div class='form-group'>
                            <input type="hidden" name="import" id="import">
                            <div class="col-lg-10 col-lg-offset-2">
                                {{ Form::submit('Import CSV', ['class' => 'btn btn-large btn-primary btn-success']) }}
                         
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
    <div class="col-lg-12" >
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 class="panel-title"><i class="fa fa-list"></i> Process Listing</h2>
            </div>
            <div class="panel-body">
                <div class="col-lg-12">
                   <table class="table table-bordered">
                        <thead>
                            <th class="text-center">Successful ID</th>
                            <th class="text-center">Unsuccessful ID</th>
                        </thead>

                        <tbody>  
                            <td><?php
                              if(isset($result)){
                                    foreach ($result['suc'] as $keyUnsuc => $valueUnsuc) {
                                    echo $valueUnsuc['transaction_id']."<br>";
                            } 
                          }
                            ?></td>
     
                          <td><?php 
                              if(isset($result)){
                                    foreach ($result['unsuc'] as $keyUnsuc => $valueUnsuc) {
                                    echo $valueUnsuc['transaction_id']."<br>";
                              } 
                          }
                         ?></td>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
@stop

