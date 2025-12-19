<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <title>@yield('title') | CMS </title>
        <meta name="google-site-verification" content="3dT-8sonvqNDilH-wr5CkgwxGyFErnxJg-fTqKno1pY" />
        <link href="<?=url('images/favicon.png');?>" rel="icon" type="image/png">

        {{ HTML::style('css/bootstrap.min.css') }}
        {{ HTML::style('css/plugins/morris.css') }}
        {{ HTML::style('css/plugins/metisMenu/metisMenu.min.css') }}
        {{ HTML::style('css/plugins/timeline.css') }}
        {{ HTML::style('css/sb-admin-2.css') }}
        {{ HTML::style('css/animate.css') }}
        {{ HTML::style('css/dimmer.min.css') }}
        {{ HTML::style('css/loader.min.css') }}
        <!-- DataTables CSS -->
        {{ HTML::style('css/plugins/dataTables.bootstrap.css?v=20190213') }}
        {{ HTML::style('css/jquery.lineProgressbar.min.css') }}

        <!-- Datepicker -->
        {{ HTML::style('//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css') }}

        <!-- Colorbox CSS -->
        {{ HTML::style('css/colorbox.css') }}
        {{ HTML::style('css/divider.min.css') }}

        
        <!-- Custom Fonts -->
        {{ HTML::style('font-awesome/css/font-awesome.min.css') }}

        <!-- Input CSS -->
        {{ HTML::style('css/fileinput.css') }}

        <!-- Multiselect CSS -->
        {{ HTML::style('css/multiselect.css') }}

        <!-- Custom CSS -->
        {{ HTML::style('css/custom.css?v='.Config::get('constants.CMS_VERSION')) }}
         <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

        <!-- Latest compiled and minified CSS for file upload -->
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/css/jasny-bootstrap.min.css">

        <!-- Bootstrap Date Time Picker CSS -->
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/css/bootstrap-datetimepicker.min.css">
        <!-- Summernote CSS -->
        {{ HTML::style('css/summernote/summernote.css') }}
        <script type="text/javascript" src="//laz-g-cdn.alicdn.com/sj/securesdk/0.0.3/securesdk_lzd_v1.js" id="J_secure_sdk_v2" data-appkey="105651"></script>
    <style>
    
    [v-cloak] {
  display: none;
}
        .badge-warning {
  background-color: #f89406;
}

.row-search{
    position: relative;
    height: 50px;
    padding-left: 8px;
    background-color: #ffffff;
    /* width: 400px; */
    color: #585858;
    font-size: 18px;
    /*padding-top: 1px;*/
    border-bottom: solid 1px #f9f9f9;
    cursor: pointer;
    

}

.row-search:hover{
    position: relative;
      height: 50px;
    padding-left: 8px;
        background-color: #f9f9f9;
    /* width: 400px; */
  color: #585858;
    font-size: 18px;
     cursor: pointer;
    /*padding-top: 5px;*/

}

.row-search-ico{
    font-size: 25px;
    color: #c7c7c7;
}

.row-search-box{
  
   position: absolute;
    min-height: 50px;
    background-color: #ffffff;
    width: 400px;
    border: solid 1px #e0e0e0;

}

.sp-row-search{
        padding: 10px;
    padding-left: 5px;
    border-right: solid 1px #f3f0f0;
}

.sp-row-data{
width: 100%;
padding: 5px;
    padding-left: 10px;
    display: inline-table;
}

.input-search{
    height: 35px;
    width: 300px;
    background-color: #ffffff;
    border: 0px;
    outline: none;
    color: #353535;
    padding: 10px;
    font-size: 15px;
}

.ico-search{
    position: absolute;
    right: 10px;
    color: #808080;
    z-index: 20;
    padding-top: 10px;
}

.red-mark{
    color: #fb4e4e;
    position: absolute;
    right: 15px;
    top: -2px;
}

.green-mark {
    color: #5dc575;
    position: absolute;
    right: 15px;
    top: -2px;
}

.blue-mark {
    color: #77b8ff;
    position: absolute;
    right: 15px;
    top: -2px;
}

.active-row{
    background-color: #eceff1 !important;
    color: #000000 !important;
}

@-webkit-keyframes placeHolderShimmer {
            0% {
                background-position: -468px 0
            }
            100% {
                background-position: 468px 0
            }
        }

        @keyframes placeHolderShimmer {
            0% {
                background-position: -468px 0
            }
            100% {
                background-position: 468px 0
            }
        }

        .timeline-item {
            background: #fff;
            /*border: 1px solid;*/
            border-color: #e5e6e9 #dfe0e4 #d0d1d5;
            border-radius: 0;
            padding: 0;
            margin: 0 auto;
            width: 305px;
            height: 70px;
        }
        .timeline-item .animated-background {
            -webkit-animation-duration: 1s;
            animation-duration: 1s;
            -webkit-animation-fill-mode: forwards;
            animation-fill-mode: forwards;
            -webkit-animation-iteration-count: infinite;
            animation-iteration-count: infinite;
            -webkit-animation-name: placeHolderShimmer;
            animation-name: placeHolderShimmer;
            -webkit-animation-timing-function: linear;
            animation-timing-function: linear;
            background: #f6f7f8;
            background: #eeeeee;
            background: -webkit-gradient(linear, left top, right top, color-stop(8%, #eeeeee), color-stop(18%, #dddddd), color-stop(33%, #eeeeee));
            background: -webkit-linear-gradient(left, #eeeeee 8%, #dddddd 18%, #eeeeee 33%);
            background: linear-gradient(to right, #eeeeee 8%, #dddddd 18%, #eeeeee 33%);
            -webkit-background-size: 800px 104px;
            background-size: 800px 104px;
            width: 305px;
            height: 70px;
            position: relative;
        }
        .timeline-item .animated-background .avatar-mask {
            width : 70px;
            height: 70px;
            border: 15px solid #fff;
            float : left;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }
        .timeline-item .animated-background .content-mask {
            width: 230px;
            height: 70px;
            float : left;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }
        .timeline-item .animated-background .content-mask .mask-1 {
            width : 235px;
            height: 35px;
            border-top: 20px solid #fff;
            border-bottom: 9px solid #fff;
            border-right: 10px solid #fff;
            float : left;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }
        .timeline-item .animated-background .content-mask .mask-2 {
            width : 235px;
            height: 35px;
            border-bottom: 30px solid #fff;
            border-right: 50px solid #fff;
            float : left;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }
                
        
        .not-content{
                color: #1b1b1b;
    padding: 5px;
        }
        
        .not-box{
            border-bottom: solid 1px #ece7e7;
            background-color: #fff;
        }
        
        .not-box:last-child{
            border-bottom: solid 0px #ece7e7 !important;
            background-color: #fff;
        }
        
        .not-box:hover{
            border-bottom: solid 1px #ece7e7;
            background-color: #f9f9f9;
        }
        
        .not-msg{
            /*border-bottom: solid 1px #ece7e7;*/
            background-color: #fff;
        }
        
        .not-msg:hover{
            /*border-bottom: solid 1px #ece7e7;*/
            background-color: #f9f9f9;
        }
        
        .not-act{
            margin-left: 4px;
            cursor: pointer;
        }
        
        .reply-comment{
            border-radius: 0px;resize: none;
             outline: none;
             font-size: 12px;
             -webkit-appearance: none;
             width: 100%;
             min-height: 50px;
        }
        
        #noti-bar{
            overflow-y: auto;
            max-height: 350px;
        }
        
        .loadMore{
            width: 100%;
            position: relative;
            height: auto;
            padding: 5px;
            text-align: center;
            color: #797272;
            font-size: 13px;
        }
        
        .noMore{
            width: 100%;
            position: relative;
            height: auto;
            padding: 5px;
            text-align: center;
            color: #797272;
            font-size: 13px;
        }
        
        .no_noti{
            width: 100%;
            position: relative;
            height: auto;
            padding: 5px;
            text-align: center;
            color: #797272;
            font-size: 13px;
        }
    </style>
    @yield('extra-css')
    </head>
    <body style="" >
        <div id="wrapper">
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <!--<a class="navbar-brand" href="/home/index">tmGrocer</a> -->
                    <a class="navbar-brand" href="/home/index"><img src="/media/tmgrocer_logo.png" style="height:40px; idth:80px;"></a>
                </div>
                <!-- /.navbar-header -->
                 @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 1, 'OR'))
                 <ul class="nav navbar-top-links navbar-left" style="margin-left: 160px; margin-top: 9px;">
                                    <li class="dropdown top-bar-li" id="search-bar">
                                        <div><input type="text"  v-on:keyup.13="setEnter('')"  v-on:keyup.40="moveDownList" v-on:keyup.38="moveUpList" v-model="searchKeyword" class="input-search" placeholder="Find Order .." style=" "><i class="fa fa-search ico-search" ></i></div>
                                        <div class="row-search-box" v-if="sugBox" v-cloak>
                                            <div class="row-search" v-for="item in sugList" v-if="gotResult" v-on:click="setEnter(item.id)" v-bind:class="[ navPointer == item.id ? 'active-row' : '']">
                                                <div class="sp-row-data" >
                                                    <div style="padding-top: 2px;"> <i class="fa fa-cube" style=""></i> @{{item.id}}</div>
                                                    <div style="font-size:10px;font-weight: lighter;"><strong>@{{item.buyer_username}}</strong> | @{{item.delivery_name}} <i style="" class="fa fa-bookmark fa-2x " v-bind:class="[item.status == 5 ? 'green-mark' : 'red-mark']"></i></div>
                                                </div>
                                            </div>
                                            <div class="row-search" v-if="noResult" >
                                                <div class="sp-row-data" style="text-align:left;font-size: 15px;">
                                                    <div style="padding-top: 10px;"> <span> - No Record Found - </span> </div>
                                                </div>
                                            </div>
                                        </div>

				    </li>
                                </ul>
                                  @endif
                <ul class="nav navbar-top-links navbar-right">
					@if( Session::has('user_id') )
						<li class="navbar-brand">Good day, {{ Session::get('username') }}!</li>
					@endif
					<!-- /.dropdown-item -->
					 <li class="dropdown top-bar-li">
				        <a class="dropdown-toggle top-bar" id="noti-ico" data-toggle="dropdown" href="#">
                                            <i class="fa fa-bell-o fa-fw"></i> <?php 
                                            $total = Notification::getTotalNotification(Session::get('user_id'));
                                            if($total > 0 ) {?><span id="noti-total" style="font-size: 10px;position: absolute;top: 9px; right: 5px;" class="badge badge-warning"><?php echo $total; ?></span><?php }?>
				        </a>
                                            <ul class="dropdown-messages dropdown-menu " id="noti-bar" @scroll="handleScroll">
                                                <li v-if="noti_loading">
                                                    <div class="timeline-item">
                                                        <div class="animated-background facebook">
                                                            <div class="avatar-mask"></div>
                                                            <div class="content-mask">
                                                                <div class="mask-1"></div>
                                                                <div class="mask-2"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li v-if="!noti_loading" class="not-box" v-for="noti in listNoti">
                                                    <div class="not-row">
                                                        <div style="width: 100%;position: relative;height: 45px;padding:5px; height: auto;">
                                                            <div style="padding: 10px;" class="pull-left">
                                                                 <img v-if="noti.user_photo != '' && noti.user_photo != null" class="img img-circle" style="width:30px;" v-bind:src="'/images/userprofile/'+ noti.user_photo"  >
                                                                 <img v-if="noti.user_photo == '' ||  noti.user_photo == null" class="img img-circle" style="width:30px;" src="/images/asset/icon/people.png">
                                                            </div> 
                                                            <div style="font-size: 10px; font-weight: lighter;" class=" not-content">
                                                                <div><span style="font-size: 12px;font-weight: bold;color: currentColor;">@{{noti.dower}}</span> @{{noti.wording}}<span style="font-size: 12px;font-weight: bold;color: currentColor;"> @{{noti.target}}</span> <span v-if="noti.description != '' && noti.description != null"><br>"@{{noti.description}}"</span></div>
                                                                <div>
                                                                    <span class=""><i class="fa fa-edit"></i>  @{{noti.created_at}}</span>
                                                                    <span class="not-act" style="text-align:right;" v-on:click="replyBox(noti.id,noti.target_id)"><i class="fa fa-comment-o" id="reply-comment"></i></span>
                                                                    <!--<span class="not-act" style="text-align:right;"><i class="fa fa-gear"></i></span>-->
                                                                </div>
                                                                <div class="animated fadeIn reply-box" id="reply-box" style="margin-top: 5px;" v-if="setText[noti.id]">
                                                                    <textarea v-model="setTextModel[noti.id]" class="form-control reply-comment" v-on:keyup.13="postReply(noti.id,noti.target_id)" placeholder="Write your comments .."style=""></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li v-if="!noti_loading && zeroData" class="not-msg" >
                                                    <div class="not-row"><div class="no_noti"><i class="fa fa-bell-slash-o"></i> No Notification</div></div>
                                                </li>
                                                <li v-if="noMore" class="not-msg" >
                                                    <div class="not-row"><div class="noMore"><i class="fa fa-bell-slash-o"></i> No More Notification</div></div>
                                                </li>
                                                <li v-if="loadMore" class="not-msg" >
                                                    <div class="not-row"><div class="loadMore"><i class="fa fa-circle-o-notch  fa-spin"></i></div></div>
                                                </li>
                                        </ul>
                    <!-- /.dropdown-messages -->
				    </li>
				    
				    <!-- /.dropdown -->
					<li class="dropdown">
				        <a class="dropdown-toggle top-bar" data-toggle="dropdown" href="#">
                                            <img class="img img-circle" style="width:22px;" src="{{Session::get('user_photo')}}">  <i class="fa fa-caret-down"></i>
				        </a>
				        <ul class="dropdown-menu dropdown-user">
				            <li><a href="/home/profile/{{Session::get('user_id')}}"><i class="fa fa-user fa-fw"></i> User Profile</a>
				            </li>
				            <li class="divider"></li>
				            <li><a href="/logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
				            </li>
				        </ul>
				        <!-- /.dropdown-user -->
				    </li>
				    <!-- /.dropdown -->
				</ul>
                <!-- /.navbar-top-links -->
                @include('includes.sidebar')
            </nav>
                @yield('content')
        </div>


        @include('includes.footer')

        <!-- Scripts are placed here -->
        {{ HTML::script('js/jquery.js') }}
        {{ HTML::script('js/bootstrap.min.js') }}

        <!-- Custom Theme JavaScript -->
        {{ HTML::script('js/sb-admin-2.js') }}

        <!-- Metis Menu Plugin JavaScript -->
        {{ HTML::script('js/plugins/metisMenu/metisMenu.min.js') }}

         <!-- DataTables JavaScript -->
        {{ HTML::script('js/plugins/dataTables/jquery.dataTables.js') }}
        {{ HTML::script('js/plugins/dataTables/dataTables.bootstrap.js') }}
        
        @yield('datatable_script')
        
        {{ HTML::script('js/vue.min.js') }}

        <!-- Datepicker -->
        {{ HTML::script('//code.jquery.com/ui/1.11.2/jquery-ui.js') }}

        <!-- Latest compiled and minified JavaScript -->
        <script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>

        <!-- bootbox code -->
        {{ HTML::script('js/bootbox.js') }}
        
        <!-- Asset -->
        @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 1, 'OR'))
        {{ HTML::script('js/asset/master.js') }}
         @endif
         {{ HTML::script('js/asset/noti.js') }}

        <!-- Colorbox JavaScript -->
        {{ HTML::script('js/jquery.colorbox.js') }}

        <!-- Multiselect Plugin JavaScript -->
        {{ HTML::script('js/jquery.multi-select.js') }}
        
        {{ HTML::style('js/jquery.lineProgressbar.js') }}

        <!-- Bootstrap Date Time Picker JavaScript -->
        
        {{ HTML::script('//cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js') }}
        {{ HTML::script('//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.7.14/js/bootstrap-datetimepicker.min.js') }}
        
        <!-- Summernote -->
	    {{ HTML::script('js/summernote/summernote.min.js') }}
        
        <script>
            $(function() {
                $('#datetimepicker').datetimepicker({
                    format: 'YYYY-MM-DD HH:mm:ss'
                });
            });

            $(function() {
                var date = $('#datepicker').datepicker({ dateFormat: 'yy-mm-dd'}).val();
            });

            $(function() {
                $('#country').on('change', function(e) {
                    console.log(e);
                    var country_id = e.target.value;

                    //ajax
                    $.get('/states?country_id=' + country_id , function(data){
                        $('#state').empty();
                        $('#city').empty();
                        $.each(data, function(index, stateObj){
                            $('#state').append('<option value="' + stateObj.id + '">' + stateObj.name + '</option>');
                        });
                        
                        $.get('/cities?state_id=' + data[0].id, function(data) {
                        // console.log(data);
                        $('#city').empty();
                        $.each(data, function(index, stateObj){
                            $('#city').append('<option value="' + stateObj.id + '">' + stateObj.name + '</option>');
                        });
                    });
                        
                    });
                });
                
                $('#state').on('change', function(e) {
                    console.log(e);
                    var state_id = e.target.value;

                    //ajax
                    $.get('/cities?state_id=' + state_id, function(data) {
                        // console.log(data);
                        $('#city').empty();
                        $.each(data, function(index, stateObj){
                            $('#city').append('<option value="' + stateObj.id + '">' + stateObj.name + '</option>');
                        });
                    });
                });

                $('#delivercountry').on('change', function(e) {
                    console.log(e);
                    var country_id = e.target.value;

                    //ajax
                    $.get('/states?delivercountry=' + country_id , function(data){
                        $('#deliverstate').empty();
                        $('#city').empty();
                        $.each(data, function(index, stateObj){
                            $('#deliverstate').append('<option value="' + stateObj.id + '">' + stateObj.name + '</option>');
                        });
                        
                        $.get('/cities?state=' + data[0].id, function(data) {
                        // console.log(data);
                        $('#city').empty();
                        $.each(data, function(index, stateObj){
                            $('#city').append('<option value="' + stateObj.id + '">' + stateObj.name + '</option>');
                        });
                    });
                    });
                    
                    
                });

                

                $('#deliverstate').on('change', function(e) {
                    console.log(e);
                    var state_id = e.target.value;

                    //ajax
                    $.get('/cities?state=' + state_id, function(data) {
                        // console.log(data);
                        $('#city').empty();
                        $.each(data, function(index, stateObj){
                            $('#city').append('<option value="' + stateObj.id + '">' + stateObj.name + '</option>');
                        });
                    });
                });
            });
        </script>

        @yield('inputjs')

        <script type="text/javascript">
            $(document).ready(function() {
                @yield('script')
            });
            $(document).on('click', '.dropdown-messages', function (e) {
                e.stopPropagation();
            });
        </script>
        @yield('extra-js')
    </body>
</html>
