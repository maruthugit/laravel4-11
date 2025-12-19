@extends('layouts.master')

@section('title') SP Import @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Import Product Special Prices</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    @if ($message = Session::get('message'))
    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="fa fa-thumbs-up"></i> {{ $message }}
    </div>
    @endif

    {{ Form::open(['role' => 'form', 'url' => '/special_price/addimport', 'class' => 'form-horizontal', 'files' => true]) }}
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-list"></i> Select Group</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class="form-group @if ($errors->has('group')) has-error @endif">
                    {{ Form::label('group', 'Group Name', array('class'=> 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        {{ Form::select('group', array('' => '- - - Select Group Name - - -') + $groups, null, ['class' => 'form-control']) }}
                        {{ $errors->first('group', '<p class="help-block">:message</p>') }}
                    </div>
                </div>

                <div class='form-group'>
                    {{ Form::label('file', 'Upload file', array('class' => 'col-lg-2 control-label')) }}
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

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-image"></i> Import CSV File Guides</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <h3><u>Editing CSV file</u></h3>

                <p class="text-warning">* Yellow highlights: For referrence purpose.</p>
                <p class="text-danger">* Red highlights&nbsp;&nbsp;&nbsp; : Important columns, CANNOT edit.</p>
                <p class="text-success">* Green highlights: CAN edit</p>
                <div>
                    {{ HTML::image('media/guide1.png', 'Guide 1', array('class' => 'img-responsive')) }}
                    <!-- <img src="" alt="Mountain View" style="width:304px;height:228px"> -->
                </div>

                <br>
                <h3><u>Saving CSV file</u></h3>

                <p class="text-info"><b>For Windows: </b>  Save as <b>"CSV - Comma Delimited (.csv)"</b> before import the CSV.</p>
                <p class="text-info"><b>For Mac &nbsp;&nbsp;&nbsp;&nbsp;: </b> Save as <b>"Windows Comma Seperated (.csv)"</b> before import the CSV.</p>
            </div>  
        </div>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-list"></i> Process Listing</h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
               <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="col-sm-1 text-center">ID</th>
                            <th class="hidden-xs hidden-sm col-sm-3 text-center">Group Name</th>
                            <th class="hidden-xs hidden-sm col-sm-3 text-center">Uploaded File</th>
                            <th class="hidden-xs hidden-sm col-sm-2 text-center">Requested Date Time</th>
                            <th class="hidden-xs hidden-sm col-sm-1 text-center">Status</th>
                            <th class="hidden-xs hidden-sm col-sm-3 text-center">Report</th>
                        </tr>
                    </thead>
                    <tbody id="ptb">
                        @if(count($jobs) > 0)
                        @foreach ($jobs as $job)
                            <tr class="jobOption">
                                <input type="hidden" value="{{$job->id}}" name="job" id="job[]">
                                <td class="col-sm-1 text-center"> {{ $job->id }} </td>
                                <td> {{ $job->name }} </td>
                                <td>
                                @if ($job->in_file)
                                    {{ HTML::link('/special_price/upload/'.$job->in_file, $job->in_file, array('target'=>'_blank')) }}</td>
                                @endif
                                </td>
                                <td class="text-center"> {{ $job->request_at }} </td>
                                <td class="text-center"> 
                                <?php
                                    $status = $job->status;
                                    switch ($status) {
                                        case '0'    : echo "<p class=\"text-danger\">In Queue</p>";
                                            break;

                                        case '1'    : echo '<p class="text-danger">In Process</p>';
                                            break;

                                        case '2'    : echo '<p class="text-success">Complete</p>';
                                            break;
                                    }
                                ?>
                                </td>
                                <td>
                                @if ($job->out_file)
                                    {{ HTML::link('/special_price/log/'.$job->out_file, $job->out_file, array('target'=>'_blank')) }}
                                @endif
                                </td>
                                <td class="text-center">
                                    @if ($status < 2)
                                    <div class="col-lg-10">
                                        <div class="input-group">
                                            <div class="input-group-btn">
                                                <span class="pull-left"><a class="btn btn-primary" href="/special_price/process?import={{$job->id}}"></i>Process Now</a></span>
                                            </div>&nbsp;
                                            <div class="input-group-btn">
                                                <span class="pull-left"><a class="btn btn-danger" href="/special_price/cancel?import={{$job->id}}"></i>Cancel</a></span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
</div>
@stop