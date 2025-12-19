<?php

function gen_xml_data($data, $t_count=0, $array_pre="", $array_post="") {
    if(is_array($data)) {
        if(sizeof($data) > 0) {
            foreach($data as $key => $val) {
                if(is_array($val)) {
                    if(sizeof($val) > 0) {
                        echo $array_pre;
                        foreach($val as $key2 => $val2) {
                            if(!is_numeric($key)) {
                                echo "\n";
                                for($i = 0; $i < $t_count + 0; $i++)
                                    echo "\t";
                                echo "<" . $key . ">";
                            }

                            if(!is_numeric($key2)) {
                                $pre_content = "\n";
                                for($i = 0; $i < $t_count + 0; $i++)
                                    $pre_content .= "\t";
                                $pre_content .= "<" . $key2 . ">";
                                $post_content = "\n";
                                for($i = 0; $i < $t_count + 0; $i++)
                                    $post_content .= "\t";
                                $post_content .= "</" . $key2 . ">";

                                gen_xml_data($val2, $t_count+1, $pre_content, $post_content);
                            } else {
                                gen_xml_data($val2, $t_count+1);

                            }

                            if(!is_numeric($key)) {
                                echo "\n";
                                for($i = 0; $i < $t_count + 0; $i++)
                                    echo "\t";
                                echo "</" . $key . ">";
                            }
                        }
                        echo $array_post;
                    } else {
                        echo str_replace(">", " />", $array_pre);
                    }
                } else {
                    echo $array_pre;
                    echo "\n";
                    for($i = 0; $i < $t_count; $i++)
                        echo "\t";
                    if($val === '')
                        echo "<" . $key . " />";
                    else
                        echo "<" . $key . ">" . xml_escape($val) . "</" . $key . ">";
                    echo $array_post;
                }
            }
        } else {
            echo str_replace(">", " />", $array_pre);
        }
    } else {
        if($data === '') {
            echo str_replace(">", " />", $array_pre);
        } else {
            echo $array_pre;
            echo xml_escape($data);
            echo str_replace(array("\n", "\t"), "", $array_post);
        }
    }
}

function xml_escape($s) {
    // echo "<br>s : ".print_r($s);
    $s = html_entity_decode($s, ENT_QUOTES, 'UTF-8');
    $s = htmlspecialchars($s, ENT_QUOTES, 'UTF-8', false);
    return $s;
}

function create_thumbnail($img_name, $new_width, $new_height, $upload_dir, $move_to_dir) {
    $path   = $upload_dir . '/' . $img_name;
//  echo "<br>path: ".$path;
    $mime   = getimagesize($path);

    switch($mime['mime']) {
        case 'image/gif':
                    $src_img = imagecreatefromgif($path);
                    break;

        case 'image/png':
                    $src_img = imagecreatefrompng($path);
                    break;

        case 'image/x-png':
                    $src_img = imagecreatefrompng($path);
                    break;

        case 'image/jpg':
                    $src_img = imagecreatefromjpeg($path);
                    break;

        case 'image/jpeg':
                    $src_img = imagecreatefromjpeg($path);
                    break;

        case 'image/pjpeg':
                    $src_img = imagecreatefromjpeg($path);
                    break;

        default:
                $src_img = imagecreatefromjpeg($path);
                break;

    }

    $old_x  = imageSX($src_img);
    $old_y  = imageSY($src_img);

    // if ($old_x > $old_y) {
    //  $thumb_w    = $new_width;
    //  $thumb_h    = $new_height;
    // }

    // if($old_x < $old_y) {
    //  $thumb_w    = $old_x * ($new_width / $old_y);
    //  $thumb_h    = $new_height;
    // }

    // if ($old_x == $old_y) {
    //  $thumb_w    = $new_width;
    //  $thumb_h    = $new_height;
    // }

    // echo "<br>[old_x: ". $old_x ."] [old_y: ". $old_y ."] [thumb_w: ". $thumb_w ."] [thumb_h: ". $thumb_h ."]";

    $dst_img    = imagecreatetruecolor($new_width, $new_height);

    imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $new_width, $new_height, $old_x, $new_height);

    $new_thumb_loc = $move_to_dir . $img_name;
    // imagecopy($dst_img, $src_img, 0, 0, 0, 0, );
//  echo "<br>new_thumb_loc: ".$new_thumb_loc;
    switch($mime['mime']) {
        case 'image/gif':
                    $result = imagegif($dst_img, $new_thumb_loc, 8);
                    break;

        case 'image/png':
                    $result = imagepng($dst_img, $new_thumb_loc, 8);
                    break;

        case 'image/x-png':
                    $result = imagepng($dst_img, $new_thumb_loc, 80);

        case 'image/jpg':
                    $result = imagejpeg($dst_img, $new_thumb_loc, 80);
                    break;

        case 'image/jpeg':
                    $result = imagejpeg($dst_img, $new_thumb_loc, 80);
                    break;

        case 'image/pjpeg':
                    $result = imagejpeg($dst_img, $new_thumb_loc, 80);
                    break;

        default:
                $result = imagejpeg($dst_img, $new_thumb_loc, 80);
                break;

    }

    // imagedestroy($dst_img);
    // imagedestroy($src_img);

    return $result;

}

