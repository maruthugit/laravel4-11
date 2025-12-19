@extends('layouts.master')

@section('title') Role @stop

@section('content')
<div id="page-wrapper">
	
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header"><i class='fa fa-gears'></i> Role</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" id="dataTable-role">
 
            <thead>
                <tr>
                	<th>ID</th>
                    <th>Role</th>
                    <th>Created DateTime</th>
                    <th></th>
                </tr>
            </thead>
 
            <tbody>
                @foreach ($roles as $role)
                <tr>
                	<td>{{ $role->id }} </td>
                    <td>{{ $role->role_name }} </td>
                    <td>{{ $role->created_at }} </td>
                    <td>
                        <a href="/user/roleedit/{{ $role->id }}" class="btn btn-primary" style="margin-right: 3px;"><i class="fa fa-pencil"></i></a>
                        <a href="/user/roledelete/{{ $role->id }}" class="btn btn-danger" style="margin-right: 3px;"><i class="fa fa-times"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
 
        </table>
    </div>
        
    <a href="/user/rolecreate" class="btn btn-large btn-success">Add Role</a>
     
</div>
@stop