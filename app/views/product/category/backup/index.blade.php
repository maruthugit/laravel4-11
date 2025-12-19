@extends('layouts.default')

@section('content')
	<div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Categories <span class="pull-right"><a class="btn btn-primary" title="" data-toggle="tooltip" href="{{ URL::route('product.category.create') }}"><i class="fa fa-plus"></i></a></span></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">            
        @if (Session::has('message'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-list"></i> Category List</h3>                    
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="table-responsive" style="overflow-x: none">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-categories">
                            <thead>
                                <tr>
                                    <th>Category Name</th>
                                    <th>Parent</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            	@foreach ($categories as $category)
									<tr>
										<td>{{ $category->category_name }}</td>
                                        <td>{{ $category->category_parent }}</td>
										<td class="text-right">
											<a class="btn btn-primary" title="" data-toggle="tooltip" href="/product/category/{{$category->id}}/edit"><i class="fa fa-pencil"></i></a>
										</td>
									</tr>
								@endforeach 
                            </tbody>
                        </table>
                    </div>                            
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
@stop