@extends('layouts.master')
@section('title') Article Posts @stop
@section('content')
<div class="loading"><span id="load-message"></span></div>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
           <h1 class="page-header">Article Posts</h1>
             
        </div>
        <div class=" btn-group  col-lg-12" style="margin-bottom:20px;padding: 0px;">

            <div class="col-md-12 col-xs-12">
                <div class="btn-group btn-sm pull-right" >
                    <a type="button"  href="/blog/create" class="btn btn-default " > <i class="fa fa-plus"></i> Create New Post</a>
                </div>
                <!--<button type="button" id="" class="btn btn-default pull-right" style=""><i class="fa fa-plus-circle"></i> Create New Posts</button>-->
            </div>
                    
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> Posts </h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive table-responsive" style="overflow-x: hidden;" >
                <table class="table table-striped table-striped table-hover" id="dataTables-posts">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="30%">Title</th>
                            <th width="10%">Category</th>
                            <th width="10%">Author</th>
                            <th width="15%">Create</th>
                            <th width="10%">Status</th>
                            <th width="10%">Publish Date</th>   
                            <th width="10%" style="text-align:right;"></th>
                        </tr>
                    </thead>

                    
                </table>
                
                <!--                        <tr>
                            <td>10</td>
                            <td>Jocom Achieved 30Mil in 2018!</td>
                            <td>News</td>
                            <td>Wira Izkandar</td>
                            <td>2018/06/05 10:56:58</td>
                            <td>Active</td>
                            <td><span class="label label-success">Published</span></td>
                            <td style="text-align:center;">
                                <a class="btn btn-default"><i class="fa fa-pencil"></i></a>
                            </td>
                        </tr>-->
            </div>
        </div>
    </div>
    
</div>

@stop
@section('inputjs')
<script src="/js/asset/blog.js" ></script>
@stop
@section('script')

@stop