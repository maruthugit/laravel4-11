@extends('layouts.master')

@section('title') History @stop

@section('content')

<div id="page-wrapper">
   
    <div class="row">
        <div class="col-lg-12">
         <span class="pull-right">
                    <a class="btn btn-primary" href="{{ url('pallet') }}"><i class="fa fa-reply"></i></a>
                   
                </span>
       
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                           <h3 class="panel-title"><i class="fa fa-pencil"></i>Pallet HIstory</h3>
                </div>
<br>
                    <h3 class="panel-title">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pallet Code history</h3>
               
                <div class="panel-body">
                    <div class="col-lg-12">   
                                     
                 
                 {{ Form::open(['url' => "pallet/{$pallet->id}", 'method' => 'patch','class' => 'form-horizontal']) }}


                        

                            <div class="form-group @if ($errors->has('lid')) has-error @endif">
                             
                                <div class="col-sm-12">
                                  
                                    <br />
                               
                                    <table class="table table-bordered">
                                        <thead>
                                              <tr>
                                               <th class="cell-small col-sm-1"> Date </th>
                                                <th class="cell-small col-sm-1">Pallet Code </th>
                                                
                                                <th class="cell-small col-sm-1">Supplier </th>
                                                  <th class="cell-small col-sm-1">TYPE </th>

                                                 <th class="col-sm-2">Price</th>
                                                 <th class="col-sm-2">Description</th>
                                                     <th class="col-sm-2">Stock Out</th>
                                                     <th class="col-sm-2">Stock In</th>
                                                   <th class="col-sm-2">To Date Balance </th>
                                                    <th class="col-sm-5">To Date Debt Stock</th>
                                                       <th class="col-sm-2">Remarks </th>
                                                <th class="cell-small col-sm-1">Modify By </th>

                                            
                                                  
                                               
                                                
                                               
                                             
                                            
                                            </tr>
                                        </thead>
                                        <tbody id="ptb">
                     
                                            @if (($pallet) && sizeof($pallet) > 0)
                                          
                                            @foreach ($pallet as $pskg)
                                          <tr class="product">
                                      <td class="hidden-xs hidden-sm col-xs-1"> <p class="form-control-static">{{$pskg->date}}</p></td>
                                            <td class="hidden-xs hidden-sm col-xs-1"> <p class="form-control-static">{{$pskg->pallet_code}}</p></td>
                                       
                                            <td class="hidden-xs hidden-sm col-xs-1"> <p class="form-control-static">{{$pskg->supplier_name}}</p></td>
                                             <td class="hidden-xs hidden-sm col-xs-1"> <p class="form-control-static">{{$pskg->type}}</p></td>
                                               <td><b>{{ $pskg->pallet_price }}</b></td>
                                             <td><b>{{ $pskg->pallet_Description }}</b></td>
                                          
                                                <td class="hidden-xs hidden-sm col-xs-1"> <p class="form-control-static" style="color: red">{{$pskg->stockout}}</p></td>

                                                <td class="hidden-xs hidden-sm col-xs-1"> <p class="form-control-static" style="color: green">{{$pskg->stockin}}</p></td>
                       <td class="hidden-xs hidden-sm col-xs-1"> <p class="form-control-static" >{{$pskg->debtstock}}</p></td>
                          <td class="hidden-xs hidden-sm col-xs-2"> <p class="form-control-static">{{$pskg->debtstock}}</p></td>
                                         <td class="hidden-xs hidden-sm col-xs-1"> <p class="form-control-static">{{$pskg->remarks}}</p></td>
                                                 <td class="hidden-xs hidden-sm col-xs-1"> <p class="form-control-static">{{$pskg->modify_by}}</p></td>
                                               
                                                
                                             
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr id="emptyproduct">
                                                <td colspan="6">No product added.</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>


                            </div>



                         
                            <hr/>
                      
                        {{ Form::close() }}
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>


  
@stop
