@extends('layouts.master')

@section('title') Banner Template @stop

@section('content')

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<style>
    .table-responsive{
        max-width: 800px;
    }

    .nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus{
        border-bottom-color: #ddd !important; 
    }

</style>

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            @if (Session::has('message'))
            <div class="alert alert-danger">
                <i class="fa fa-exclamation"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
            @endif
            @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
            @endif

            <h1 class="page-header">Banner Template Management
                <span class="pull-right">
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="/bannertemplate/template"><i class="fa fa-refresh"></i></a>
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="/bannertemplate/layout"><i class="fa fa-plus"></i></a>
                </span>
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(array('url' => 'bannertemplate/templateupdate/' , 'class' => 'form-horizontal', 'files' => true, 'method'=>'POST', 'enctype' => "multipart/form-data")) }}

    <div class="row" style="margin-top: 15px;">
        <div class="col-lg-12">
            <ul class="nav nav-tabs" style="background: rgba(221, 221, 221, 0.38);border: 1px solid #ddd;border-top-right-radius: 9px;border-top-left-radius: 9px;">
                <li class="active"><a data-toggle="tab" href="#home"><center><i class="fa fa-picture-o fa-lg fa-lg" aria-hidden="true"></i></center>HQ</a></li>
                <li><a data-toggle="tab" href="#menu1"><center><i class="fa fa-picture-o fa-lg fa-lg"></i></center>JOHOR</a></li>
                <li><a data-toggle="tab" href="#menu2"><center><i class="fa fa-picture-o fa-lg fa-lg"></i></center>PNG</a></li>
                <li><a data-toggle="tab" href="#menu3"><center><i class="fa fa-picture-o fa-lg fa-lg"></i></center>CHINA</a></li>
                <li><a data-toggle="tab" href="#menu4"><center><i class="fa fa-picture-o fa-lg fa-lg"></i></center>AUSTRALIA</a></li>
            </ul> 
            <div class="tab-content" style="padding-left: 5px;padding-right: 5px;">
                <div id="home" class="tab-pane fade in active">
                    <br>
                    <!-- START HQ-->
                    <?php 
                        $groups =array_chunk($region1, 3);
      
                        foreach ($groups as $key => $value) {

                            $divtype = $value[0]->type;
                            if ($divtype == "B001") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default test" id="B001_hq_temp">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B001(HQ)
                                            <span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq1[]" style="width: 70px;  margin-top: -8px;"></span>
                                        </h2>

                                    </div>
                                    <div class="panel-body ">
                                        <div class="col-lg-12 ">
                                           <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID1[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B001_hq[]">';
                                                            foreach ($status as $key => $valstat){
                                                                
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "
                                            <center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td rowspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image1[]' class='form-control input-sm' >
                                                        <br>
                                                        <input type='text' name='qrcode1[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id1[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 2) 
                                                echo "<td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image2[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode2[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id2[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";
                                                if($rowNum == 3) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image3[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode3[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id3[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                                           
                                            }
                                            echo "</table></center></div>          
                                        </div>
                                    <div class='results'></div>
                                </div>";
                            }

                            if ($divtype == "B002") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default" id="B002_hq_temp">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B002(HQ)
                                            <span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq2[]" style="width: 70px;  margin-top: -8px;"></span>
                                        </h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID2[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B002_hq[]">';
                                                                foreach ($status as $key => $valstat){
                                                                
                                                                    echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                    if( $key == $divstat){
                                                                        echo ' selected="selected"';
                                                                    }
                                                                    echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                                }
                                                                

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td colspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image4[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode4[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id4[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td></tr>";
                                                if($rowNum == 2) 
                                                echo "
                                                <tr><td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image5[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode5[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id5[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 3) 
                                                echo "
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'> <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image6[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode6[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id6[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                                           
                                            }
                                            echo "</table></center>
                                        </div>                               
                                    </div>
                                </div>";

                            }

                            if ($divtype == "B003") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default B001_hq_temp" id="B001_hq_temp">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B003(HQ)
                                            <span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq3[]" style="width: 70px;  margin-top: -8px;"> </span>
                                        </h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID3[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B003_hq[]">';
                                                            foreach ($status as $key => $valstat){
                                                           
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image7[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode7[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id7[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 2) 
                                                echo "
                                                <td rowspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image8[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode8[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id8[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td></tr>";
                                                if($rowNum == 3) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'> <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image9[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode9[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id9[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                                           
                                            }
                                            echo "</table></center>
                                            </div>          
                                        </div>
                                    </div>";
                            }

                            if ($divtype == "B004") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default" id="B002_hq_temp">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B004(HQ)
                                            <span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq4[]" style="width: 70px;  margin-top: -8px;"></span>
                                        </h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID4[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B004_hq[]">';
                                                            foreach ($status as $key => $valstat){
                                                            
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image10[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode10[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id10[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 2) 
                                                echo "
                                                <td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image11[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode11[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id11[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td></tr>";
                                                if($rowNum == 3) 
                                                echo "
                                                <tr>
                                                    <td colspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'> <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image12[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode12[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id12[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                                           
                                            }
                                            echo "</table></center>
                                        </div>                               
                                    </div>
                                </div>";
                            }
                            if ($divtype == "B005") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default" id="B005_hq_temp">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B005(HQ)
                                            <span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq13[]" style="width: 70px;  margin-top: -8px;"></span>
                                        </h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID13[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B005_hq[]">';
                                                            foreach ($status as $key => $valstat){
                                                            
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image37[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode37[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id37[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td></tr>";                                      
                                            }
                                            echo "</table></center>
                                        </div>                               
                                    </div>
                                </div>";
                            }
                            
                        }                                                                       
                    ?>

                </div>
                <!-- END HQ-->
                <!-- START JOHOR-->
                <div id="menu1" class="tab-pane fade">
                    <br>
                    <?php 
                        $groups =array_chunk($region2, 3);
                                    
                        foreach ($groups as $key => $value) {

                            $divtype = $value[0]->type;
                            if ($divtype == "B001") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B001(JOHOR)<span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq5[]" style="width: 70px;  margin-top: -8px;"></span></h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                           <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID5[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B001_jb[]">';
                                                            foreach ($status as $key => $valstat){
                                                               
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td rowspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image13[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode13[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id13[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span> 
                                                    </td>";
                                                if($rowNum == 2) 
                                                echo "<td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image14[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode14[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id14[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";
                                                if($rowNum == 3) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image15[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode15[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id15[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";  

                                            }
                                            echo "</table></center>
                                        </div>          
                                    </div>
                                </div>";
                            }

                            if ($divtype == "B002") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B002(JOHOR)<span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq6[]" style="width: 70px;  margin-top: -8px;"></span></h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID6[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B002_jb[]">';
                                                            foreach ($status as $key => $valstat){
                                                            
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td colspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image16[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode16[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id16[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td></tr>";
                                                if($rowNum == 2) 
                                                echo "
                                                <tr><td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image17[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode17[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id17[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 3) 
                                                echo "
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image18[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode18[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id18[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                                           
                                            }     
                                            echo "</table></center> 
                                        </div>                               
                                    </div>
                                </div>";
                            }

                            if ($divtype == "B003") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default B001_hq_temp" id="B001_hq_temp">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B003(JOHOR)<span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq7[]" style="width: 70px;  margin-top: -8px;"></span></h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID7[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B003_jb[]">';
                                                            foreach ($status as $key => $valstat){
                                                            
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image19[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode19[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id19[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 2) 
                                                echo "<td rowspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image20[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode20[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id20[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";
                                                if($rowNum == 3) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image21[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode21[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id21[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                                  
                                            }
                                            echo "</table></center> 
                                        </div>          
                                    </div>
                                </div>";
                            }

                            if ($divtype == "B004") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default" id="B002_hq_temp">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B004(JOHOR)<span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq8[]" style="width: 70px;  margin-top: -8px;"></span></h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">    
                                                    <input type="hidden" name="bannerID8[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B004_jb[]">';
                                                            foreach ($status as $key => $valstat){
                                                            
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image22[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode22[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id22[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 2) 
                                                echo "<td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image23[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode23[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id23[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";
                                                if($rowNum == 3) 
                                                echo "
                                                <tr>
                                                    <td colspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image24[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode24[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id24[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                           
                                            }
                                            echo "</table></center>
                                        </div>                              
                                    </div>
                                </div>";
                            }

                            if ($divtype == "B005") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default" id="B005_jb_temp">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B005(JB)
                                            <span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq14[]" style="width: 70px;  margin-top: -8px;"></span>
                                        </h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID14[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B005_jb[]">';
                                                            foreach ($status as $key => $valstat){
                                                            
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image38[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode38[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id38[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td></tr>";                                      
                                            }
                                            echo "</table></center>
                                        </div>                               
                                    </div>
                                </div>";
                            }
                            
                        }                                                                       
                    ?>

                    

                </div>
                <!-- END JOHOR-->
                <!-- START PNG-->
                <div id="menu2" class="tab-pane fade">
                    <br>
                    <?php 
                        $groups =array_chunk($region3, 3);
                                    
                        foreach ($groups as $key => $value) {

                            $divtype = $value[0]->type;
                            if ($divtype == "B001") 
                            {   
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B001(PNG)<span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq9[]" style="width: 70px;  margin-top: -8px;"></span></h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID9[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B001_png[]">';
                                                            foreach ($status as $key => $valstat){
                                                            
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td rowspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image25[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode25[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id25[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 2) 
                                                echo "<td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image26[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode26[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id26[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";
                                                if($rowNum == 3) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image27[]' class='form-control input-sm' value='$val->file_name'> 
                                                        <br>
                                                        <input type='text' name='qrcode27[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'> 
                                                        <input type='hidden' name='id27[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                                           
                                            }
                                            echo "</table></center>
                                        </div>          
                                    </div>
                                </div>";
                            }

                            if ($divtype == "B002") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B002(PNG)<span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq10[]" style="width: 70px;  margin-top: -8px;"></span></h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID10[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B002_png[]">';
                                                            foreach ($status as $key => $valstat){
                                                            
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td colspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image28[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode28[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id28[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td></tr>";
                                                if($rowNum == 2) 
                                                echo "
                                                <tr><td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image29[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode29[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id29[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 3) 
                                                echo "
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image30[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode30[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id30[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                       
                                            }
                                            echo "</table></center>
                                        </div>                               
                                    </div>
                                </div>";
                            }

                            if ($divtype == "B003") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default B001_hq_temp" id="B001_hq_temp">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B003(PNG)<span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq11[]" style="width: 70px;  margin-top: -8px;"></span></h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID11[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B003_png[]">';
                                                            foreach ($status as $key => $valstat){
                                                            
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image31[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode31[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id31[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 2) 
                                                echo "<td rowspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image32[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode32[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id32[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";
                                                if($rowNum == 3) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image33[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode33[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id33[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                                                
                                            }
                                            echo "</table></center>
                                        </div>          
                                    </div>
                                </div>";
                            }

                            if ($divtype == "B004") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default" id="B002_hq_temp">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B004(PNG)<span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq12[]" style="width: 70px;  margin-top: -8px;"></span></h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID12[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B004_png[]">';
                                                            foreach ($status as $key => $valstat){
                                                              
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image34[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode34[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id34[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 2) 
                                                echo "<td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image35[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode35[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id35[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";
                                                if($rowNum == 3) 
                                                echo "
                                                <tr>
                                                    <td colspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image36[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode36[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id36[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                              
                                            }
                                            echo "</table></center>
                                        </div>                               
                                    </div>
                                </div>";
                            }

                            if ($divtype == "B005") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default" id="B005_jb_temp">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B005(PNG)
                                            <span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq15[]" style="width: 70px;  margin-top: -8px;"></span>
                                        </h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID15[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B005_png[]">';
                                                            foreach ($status as $key => $valstat){
                                                            
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image39[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode39[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id39[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td></tr>";                                      
                                            }
                                            echo "</table></center>
                                        </div>                               
                                    </div>
                                </div>";
                            }
                            
                        }                                                                       
                    ?>

                </div>
                <!-- END PNG-->
                <!-- START CHINA-->
                <div id="menu3" class="tab-pane fade">
                    <br>
                    <?php 
                        $groups =array_chunk($region4, 3);
                                    
                        foreach ($groups as $key => $value) {

                            $divtype = $value[0]->type;
                            if ($divtype == "B001") 
                            {   
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B001(CHQ)<span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq16[]" style="width: 70px;  margin-top: -8px;"></span></h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID16[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B001_chq[]">';
                                                            foreach ($status as $key => $valstat){
                                                            
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td rowspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image40[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode40[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id40[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 2) 
                                                echo "<td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image41[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode41[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id41[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";
                                                if($rowNum == 3) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image42[]' class='form-control input-sm' value='$val->file_name'> 
                                                        <br>
                                                        <input type='text' name='qrcode42[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'> 
                                                        <input type='hidden' name='id42[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                                           
                                            }
                                            echo "</table></center>
                                        </div>          
                                    </div>
                                </div>";
                            }

                            if ($divtype == "B002") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B002(CHQ)<span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq17[]" style="width: 70px;  margin-top: -8px;"></span></h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID17[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B002_chq[]">';
                                                            foreach ($status as $key => $valstat){
                                                            
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td colspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image43[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode43[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id43[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td></tr>";
                                                if($rowNum == 2) 
                                                echo "
                                                <tr><td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image44[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode44[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id44[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 3) 
                                                echo "
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image45[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode45[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id45[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                       
                                            }
                                            echo "</table></center>
                                        </div>                               
                                    </div>
                                </div>";
                            }

                            if ($divtype == "B003") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default B001_hq_temp" id="B001_hq_temp">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B003(CHQ)<span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq18[]" style="width: 70px;  margin-top: -8px;"></span></h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID18[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B003_chq[]">';
                                                            foreach ($status as $key => $valstat){
                                                            
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image46[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode46[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id46[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 2) 
                                                echo "<td rowspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image47[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode47[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id47[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";
                                                if($rowNum == 3) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image48[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode48[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id48[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                                                
                                            }
                                            echo "</table></center>
                                        </div>          
                                    </div>
                                </div>";
                            }

                            if ($divtype == "B004") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default" id="B002_hq_temp">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B004(CHQ)<span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq19[]" style="width: 70px;  margin-top: -8px;"></span></h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID19[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B004_chq[]">';
                                                            foreach ($status as $key => $valstat){
                                                              
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image49[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode49[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id49[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 2) 
                                                echo "<td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image50[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode50[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id50[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";
                                                if($rowNum == 3) 
                                                echo "
                                                <tr>
                                                    <td colspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image51[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode51[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id51[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                              
                                            }
                                            echo "</table></center>
                                        </div>                               
                                    </div>
                                </div>";
                            }

                            if ($divtype == "B005") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default" id="B005_jb_temp">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B005(CHQ)
                                            <span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq20[]" style="width: 70px;  margin-top: -8px;"></span>
                                        </h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID20[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B005_chq[]">';
                                                            foreach ($status as $key => $valstat){
                                                            
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image52[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode52[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id52[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td></tr>";                                      
                                            }
                                            echo "</table></center>
                                        </div>                               
                                    </div>
                                </div>";
                            }
                            
                        }                                                                       
                    ?>

                </div>
                <!-- END CHINA-->
                
                <!-- START Australia HQ-->
                
                <div id="menu4" class="tab-pane fade">
                    <br>
                    <?php 
                        $groups =array_chunk($region5, 3);
                            // print_r($groups);        
                        foreach ($groups as $key => $value) {
                        // print_r($value);
                            $divtype = $value[0]->type;
                            // echo $divtype.'<br>';
                            if ($divtype == "B001") 
                            {   
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B001(AZHQ)<span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq21[]" style="width: 70px;  margin-top: -8px;"></span></h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID21[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B001_azhq[]">';
                                                            foreach ($status as $key => $valstat){
                                                            
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td rowspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image53[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode53[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id53[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 2) 
                                                echo "<td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image54[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode54[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id54[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";
                                                if($rowNum == 3) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image55[]' class='form-control input-sm' value='$val->file_name'> 
                                                        <br>
                                                        <input type='text' name='qrcode55[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'> 
                                                        <input type='hidden' name='id55[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                                           
                                            }
                                            echo "</table></center>
                                        </div>          
                                    </div>
                                </div>";
                            }

                            if ($divtype == "B002") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B002(AZHQ)<span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq22[]" style="width: 70px;  margin-top: -8px;"></span></h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID22[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B002_azhq[]">';
                                                            foreach ($status as $key => $valstat){
                                                            
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td colspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image56[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode56[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id56[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td></tr>";
                                                if($rowNum == 2) 
                                                echo "
                                                <tr><td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image57[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode57[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id57[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 3) 
                                                echo "
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image58[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode58[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id58[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                       
                                            }
                                            echo "</table></center>
                                        </div>                               
                                    </div>
                                </div>";
                            }

                            if ($divtype == "B003") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default B001_hq_temp" id="B001_hq_temp">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B003(AZHQ)<span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq23[]" style="width: 70px;  margin-top: -8px;"></span></h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID23[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B003_azhq[]">';
                                                            foreach ($status as $key => $valstat){
                                                            
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image59[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode59[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id59[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 2) 
                                                echo "<td rowspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image60[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode60[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id60[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";
                                                if($rowNum == 3) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image61[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode61[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id61[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                                                
                                            }
                                            echo "</table></center>
                                        </div>          
                                    </div>
                                </div>";
                            }

                            if ($divtype == "B004") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                echo '
                                <div class="panel panel-default" id="B002_hq_temp">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B004(AZHQ)<span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq24[]" style="width: 70px;  margin-top: -8px;"></span></h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID24[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B004_azhq[]">';
                                                            foreach ($status as $key => $valstat){
                                                              
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image62[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode62[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id62[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>";
                                                if($rowNum == 2) 
                                                echo "<td style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image63[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode63[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id63[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";
                                                if($rowNum == 3) 
                                                echo "
                                                <tr>
                                                    <td colspan='2' style='border: 1px solid rgba(128, 128, 128, 0.36);'> 
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image64[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode64[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id64[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td>
                                                </tr>";                              
                                            }
                                            echo "</table></center>
                                        </div>                               
                                    </div>
                                </div>";
                            }

                            if ($divtype == "B005") 
                            {
                                $divstat = $value[0]->active_status;
                                $divbannerID = $value[0]->banner_id;
                                $divSeq = $value[0]->seq;
                                // echo 'Banner ID'.$divbannerID ;
                                echo '
                                <div class="panel panel-default" id="B005_jb_temp">
                                    <div class="panel-heading">
                                        <h2 class="panel-title"><i class="fa fa-pencil"></i> Banner B005(AZHQ)
                                            <span class="pull-right"><input class="form-control" type="number" min="1" value="'.htmlspecialchars( $divSeq).'" name="seq25[]" style="width: 70px;  margin-top: -8px;"></span>
                                        </h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="col-lg-12 ">
                                            <div class="form-group">
                                                <div class="col-lg-2 pull-right">
                                                    <input type="hidden" name="bannerID25[]" class="form-control input-sm" value="' . htmlspecialchars( $divbannerID) .'">
                                                    <select class="form-control" name="B005_azhq[]">';
                                                            foreach ($status as $key => $valstat){
                                                            
                                                                echo '<option value="' . htmlspecialchars( $key) .'"';
                                                                if( $key == $divstat){
                                                                    echo ' selected="selected"';
                                                                }
                                                                echo '>' . htmlspecialchars( $valstat) . '</option>';
                                                            }
                                                            

                                                echo '</select>
                                                </div>
                                            </div>';

                                            echo '<a id="deleteBan" class="btn btn-danger pull-right" title="" data-toggle="tooltip" data-value="' . htmlspecialchars( $divbannerID) .'" href="/bannertemplate/layoutdelete/' . htmlspecialchars( $divbannerID) .'"><i class="fa fa-times"></i></a>';

                                            $rowNum = 0;
                                            echo "<center class='element'><table class='table  table-responsive'>";
                                            // echo '<pre>';
                                            // print_r($value);
                                            // echo '</pre>';
                                            foreach ($value as $key => $val) {
                                                $rowNum ++;
                                                if($rowNum == 1) 
                                                echo "
                                                <tr>
                                                    <td style='border: 1px solid rgba(128, 128, 128, 0.36);'>
                                                        <img src='/images/manage_banners/$val->file_name' alt='' class='img-responsive center-block'>
                                                        <br>
                                                        <input type='file' name='image65[]' class='form-control input-sm' value='$val->file_name'>
                                                        <br>
                                                        <input type='text' name='qrcode65[]' class='form-control input-sm' value='$val->qrcode' placeholder='Qrcode'>
                                                        <input type='hidden' name='id65[]' class='form-control input-sm' value='$val->id'>
                                                        <span class='help-block'>Size : $val->max_width px (W) x $val->max_height px (H)</span>
                                                    </td></tr>";                                      
                                            }
                                            echo "</table></center>
                                        </div>                               
                                    </div>
                                </div>";
                            }
                            
                        }                                                                       
                    ?>

                </div>
              
                <!-- END Australia HQ-->
            </div>
        </div>
    </div>
    <br>
    <div class='form-group'>
        <div class="col-lg-10">
            {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
            {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
        </div>
    </div>
    <br>
    {{ Form::close() }}

</div>

<script>
    $('.test').on('click', '.remove', function() {
    $('.remove').closest('.test').find('.element').not(':first').last().remove();
    });
    $('.test').on('click', '.clone', function() {
        $('.clone').closest('.test').find('.element').first().clone().find("input:text").val("").end().appendTo('.results');
    });

    $(document).on("click", "#deleteBan", function(e) {
        var link = $(this).attr("href");
        e.preventDefault();
        bootbox.confirm({
            title: "Delete entry",
            message: "Are you sure to delete this banner template - " + $(this).attr("data-value") + " ?",
            callback: function(result) {
                if (result === true) {
                    console.log("Delete banner id");
                    window.location = link;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    }); 
</script>
@stop
