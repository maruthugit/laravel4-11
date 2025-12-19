@extends('layouts.master')

@section('title') Users @stop

@section('content')

<div id="page-wrapper">
   
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><i class='fa fa-gears'></i> User Administration</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <div class="row">
        <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="dataTable-user">
     
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Fullname</th>            
                        <th>Email</th>
                        <th>Role</th>
                        <th>Date/Time Added</th>
                        <th></th>
                    </tr>
                </thead>
     
                <tbody>
                    @foreach ($users as $user)
                    @if ($user->active_status == '1')
                    <tr>
                        <td>{{ $user->username }} ({{ $user->id }})</td>
                        <td>{{ $user->full_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role_name }}</td>
                        <td>{{ $user->modify_date }}</td>
                        <td>
                            <a href="/user/edit/{{ $user->id }}" class="btn btn-primary" style="margin-right: 3px;"><i class="fa fa-pencil"></i></a>
                            <a href="/user/destroy/{{ $user->id }}" class="btn btn-danger" style="margin-right: 3px;"><i class="fa fa-times"></i></a>
                            {{ Form::close() }}
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
     
            </table>
        </div>
    </div>
    </div>
    <a href="/user/create" class="btn btn-success">Add User</a>
     
</div>
@stop