@extends('layouts.master')

@section('title') Role @stop

@section('content')

<div id="page-wrapper">
    @if ($errors->has())
        @foreach ($errors->all() as $error)
            <div class='bg-danger alert'>{{ $error }}</div>
        @endforeach
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Role</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(array('url' => 'sysadmin/role/store/' , 'class' => 'form-horizontal')) }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Add Role </h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-10">
                <div class='form-group'>
                    {{ Form::label('role_name', 'Role Name', array('class' => 'col-lg-3 control-label')) }}
                    <div class="col-lg-4">
                        {{ Form::text('role_name', null, ['placeholder' => 'Role', 'class' => 'form-control']) }}
                    </div>
                </div>

                
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-key"></i> Permissions - CMS Modules</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-10">
                @foreach ($modules as $m)
                @if(isset($m->group) && $m->module != 'System Administration')  
                    <div class='form-group'>
                        {{ Form::label($m->group,  $m->module.' ['.$m->id.'] :', array('class' => 'col-lg-3 control-label')) }}
                    <?php
                        $bit_count  = 1;
                        $count      = 0;
                        $bit        = array();
                        
                        foreach ($permission_bit as $pbit){
                            echo '<label class="col-sm-1 checkbox-inline">';
                            echo '<input id='. $m->group .' type="checkbox" name="permission['.$m->id.'][]" value="'.$pbit->bit.'" ';
                            echo '>'.$pbit->name;
                            echo '</label>';    
                        }
                    ?>
                    </div>
                @endif
                @endforeach
                
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title" style="color:green"><i class="fa fa-key"></i> Permissions - Administrative Modules </h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-10">
                @foreach ($modules as $m)
                @if(isset($m->group) && $m->module == 'System Administration')  
                    <div class='form-group'>
                        {{ Form::label($m->group,  $m->module.' ['.$m->id.'] :', array('class' => 'col-lg-3 control-label')) }}
                    <?php
                        $bit_count  = 1;
                        $count      = 0;
                        $bit        = array();
                        foreach ($permission_bit as $pbit){
                            echo '<label class="col-sm-1 checkbox-inline">';
                            echo '<input id='. $m->group .' type="checkbox" name="permission['.$m->id.'][]" value="'.$pbit->bit.'" ';
                            echo '>'.$pbit->name ;
                            echo '</label>';
                        }
                    ?>
                    </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
    @if(Permission::CheckAccessLevel(Session::get('role_id'), 10, 5, 'AND'))
    <div class='form-group'>
        <div class="col-lg-10">
        {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
        {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
        </div>
    </div>
    @endif
    {{ Form::close() }}

</div>

@stop