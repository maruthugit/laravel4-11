@extends('layouts.master')

@section('title') Individual Permission @stop

@section('content')

<div id="page-wrapper">
    @if ($errors->has())
        @foreach ($errors->all() as $error)
            <div class='bg-danger alert'>{{ $error }}</div>
        @endforeach
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Edit Individual Permission</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(array('url' => array('sysadmin/indvPermission/update/' . $indvPermission->user_id . '/' . $indvPermission->mod_id ) , 'class' => 'form-horizontal', 'method' => 'PUT')) }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Edit User </h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-10">
                <div class='form-group'>
                    {{ Form::label('user', 'User * :', array('class'=> 'col-lg-3 control-label')) }}
                    <input type="hidden" id="user_id" name="user_id" value="{{ $indvPermission->user_id }}">
                    <input type="hidden" id="mod_id" name="mod_id" value="{{ $indvPermission->mod_id }}">
                    <input type="hidden" id="indvPermission" name="indvPermission" value="indvPermission">
                    <div class="col-lg-3">
                        <div class="input-group">
                        {{ Form::text('username', $indvPermission->username, ['placeholder' => 'Username', 'class' => 'form-control', 'disabled']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-key"></i> Permissions for Sub Modules - CMS Modules</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-10">
                <div class='form-group'>
                    {{ Form::label('modules', 'Modules [ID] * :', array('class' => 'col-lg-3 control-label')) }}
                    <div class="col-lg-3">
                        <select class='col-sm-1 form-control' name='modules' id='modules' disabled>
                            
                            <option value="{{ $indvPermission->module }}" > {{ $indvPermission->module }} [{{ $indvPermission->mod_id }}]</option>
                            {{-- @foreach ($modules as $module)
                                <option name='module' value='{{ $module->id }}'>{{ $module->module }} [{{ $module->id }}]</option>
                            @endforeach --}}
                        </select>
                        {{ $errors->first('module', '<p class="help-block">:message</p>') }}
                    </div>
                </div>   
                <div class='form-group'>   
                        @foreach ($subModules as $subModule)
                        {{-- <input type="hidden" id="subModule2" name="subModule2[{{ $subModule->id }}]" value="{{ $subModule->id }}"> --}}
                            <div class='form-group'>
                                {{-- {{ Form::label($subModules->page_link,  $subModule->sub_module.' ['.$subModule->id.'] :', array('class' => 'col-lg-3 control-label')) }} --}}
                                <label value="{{ $subModules->page_link }}" class='col-lg-3 control-label'>{{ $subModule->sub_module }} [{{ $subModule->id }}] : </label>
                                <label class='col-sm-1 checkbox-inline'>
                                    <input id="{{ $subModules->page_link }}" type='checkbox' name="sub_module[{{ $subModule->id }}]" value="{{ $subModule->id }}" {{ $hasPermission = ($subModule->status == 1) ? 'checked' : ''; }}>
                                </label>
                            </div>
                        @endforeach
                </div>
            </div>
        </div>
    </div>

    @if(Permission::CheckAccessLevel(Session::get('role_id'), 10, 5, 'AND'))
    <div class='form-group'>
        <div class="col-lg-10">
    {{-- {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }} --}}
    {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary', 'id' => 'submit']) }}
        </div>
    </div>  
@endif
{{ Form::close() }}

@stop

@section('script')

$('#selectUserBtn').colorbox({
    iframe:true, width:"90%", height:"90%",
    onClosed: function() {
        localStorage.clear();
        const role = document.getElementById('role_id').value;
        loadModule(role);
    }
});


function loadModule(roleID){
    console.log("masuk 2");
        
    $.ajax({
            method: "POST",
            url: "module",
            dataType:'json',
            data: {
                'role_id':roleID
            },
            beforeSend: function(){
            },
            success: function(data) {
                // console.log(data.data.module);
                var moduleList = data.data.module;
                console.log(moduleList);
                var str = '';
                $.each(moduleList, function (index, value) {
                    str = str + "<option value='"+value.id+"'>"+value.module+" ["+value.id+"]</option>";
                });
                console.log(str);
                $("#modules").html(str);
                const user = document.getElementById('user_id').value;
                const role = document.getElementById('role_id').value;
                loadSubModule(moduleList[0].id, user, role);
                
            }
      })
    
};

$('body').on('change', '#modules', function() {
    const user = document.getElementById('user_id').value;
    const role = document.getElementById('role_id').value;
    loadSubModule($(this).val(), user, role);
    console.log(user);
    console.log(role);
    {{-- loadSubModule($(this).val()); --}}
});

function loadSubModule(moduleID, userId, roleId){
    {{-- console.log(moduleID);
    console.log(userId); --}}
    
    $.ajax({
            method: "POST",
            url: "submodule",
            dataType:'json',
            data: {
                'module_id':moduleID,
                'user_id':userId,
                'role_id':roleId
            },
            beforeSend: function(){
            },
            success: function(data) {
                {{-- var subModuleList = data.data.subModule; --}}
                var subModuleList = data.data.subModule;
                var permissionList = data.data.permission_bit;
                var str = '';
                permission = '';
                var hasPermission;
                {{-- console.log(subModuleList);
                console.log(permissionList); --}}

                $.each(subModuleList, function (index, value) {
                    hasPermission = (value.status == 1) ? 'checked' : '';
                    {{-- console.log(hasPermission); --}}

                    str = str + 
                    "<br><label value='"+value.page_link+"' class='col-lg-3 control-label'>"+value.sub_module+' ['+value.id+']'+' :'+"</label>" + 
                    "<label class='col-sm-1 checkbox-inline'><input id='"+value.page_link+"' type='checkbox' name='sub_module["+value.id+"]' value='"+value.id+"'  "+hasPermission+" ></label>" +
                    "<br>";
                });
                $("#sub_module_id").html(str);
                
            }
      })
    
}

@stop