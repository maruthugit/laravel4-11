@extends('layouts.master')

@section('title', 'Festival Campaigns List')
@stop

@section('extra-css')
    {{-- <link rel="stylesheet" href="https://bootswatch.com/4/flatly/bootstrap.min.css"> --}}
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
        .alert{ padding: 5px !important; margin: 1px !important; font-size: 12px !important; font-weight: 700 !important;  }
        .label-custm{ font-size: 13px !important;  }
    </style>
@stop

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <nav class="navbar navbar-custom">
              <div class="container-fluid">
                <div class="navbar-header" style="padding-left: 10px">
                  <a href="/campaigns/festival-campaigns/create" class="btn btn-default navbar-btn pull-left"><span class="glyphicon glyphicon-plus"></span> Create New</a>
                  <div class="navbar-text navbar-brand-centered"><h3>Festival Campaigns</h3></div>
                </div>
              </div>
            </nav>
        </div>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-body">
                    <table id="campaign_table" class="table table-hover table-bordered table-responsive" style="width:100%"></table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('extra-js')
    {{ HTML::script('/js/plugins/dataTables/jquery.dataTables.js') }}
    {{ HTML::script('/js/plugins/dataTables/dataTables.bootstrap.js') }}
    <script>
        (function(){
            $('#campaign_table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "/campaigns/festivals/get-data",
                "columnDefs": [ { //this prevents errors if the data is null
                    "targets": "_all",
                    "defaultContent": ""
                } ],
                "language": {"search": "Search Records: "},
                "order": [[0,'desc']],
                "columns": [
                    {data: 'id', "title": 'ID', "orderable":true, "searchable": true, 'className' : 'text-center', "width": "5%" },
                    {data: 'title', title: 'TITLE', orderable: false, searchable: true, "width": "20%"},
                    {data: 'qrcode', title: 'ITEMS', orderable: false, searchable: true, "width": "30%"},
                    {data: 'effect', title: 'EFFECT', orderable: true, searchable: false, 'className' : 'text-center', "width": "15%"},
                    {data: 'related_effect', title: 'RELATED EFFECT', orderable: true, searchable: false, 'className' : 'text-center', "width": "15%"},
                    {data: 'status', title: 'STATUS', orderable: true, searchable: true, 'className' : 'text-center', "width": "15%"},
                    {data: 'action', title: 'ACTION', orderable: false, searchable: false, 'className' : 'text-center'}
                ],
                "buttons": [
                    'pdf'
                ]
            });
        })();
    </script>
@stop