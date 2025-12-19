<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class General extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;
    /**
     * Table for all transaction.
     *
     * @var string
     */
    //protected $table = 'jocom_transaction';

    

    /**
     * Generate XML for Apps
     * @return [type] [description]
     */    
    public function scopeGen_xml_data($query, $data, $t_count=0, $array_pre="", $array_post="") 
    {  
        // echo "<br> xml data: ";
        // var_dump($data);
        if(is_array($data)) {
            if(sizeof($data) > 0) {
                foreach($data as $key => $val) {
                    if(is_array($val)) 
                    {
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
                                    
                                    General::gen_xml_data($val2, $t_count+1, $pre_content, $post_content);
                                    //gen_xml_data($val2, $t_count+1, $pre_content, $post_content);
                                } else {
                                    General::gen_xml_data($val2, $t_count+1);
                                    //gen_xml_data($val2, $t_count+1);
                                    
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
                            echo "<" . $key . ">" . General::xml_escape($val) . "</" . $key . ">";
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
                General::xml_escape_array($data);
                echo str_replace(array("\n", "\t"), "", $array_post);
            }
        }
    }

    /**
     * Convert String Accordingly for XML
     * @return [type] [description]
     */    
    public function scopeXml_escape($query, $s) 
    {
        $s = html_entity_decode($s, ENT_QUOTES, 'UTF-8');
        $s = htmlspecialchars($s, ENT_QUOTES, 'UTF-8', false);
        return $s;
    }

    /**
     * Convert Array Accordingly for XML
     * @return [type] [description]
     */    
    public function scopeXml_escape_array($query, $s) 
    {
        $s = html_entity_decode($s, ENT_QUOTES, 'UTF-8');
        $s = htmlspecialchars($s, ENT_QUOTES, 'UTF-8', false);
        echo $s;
    }

    /**
     * Convert String Accordingly for SQL use, only applicable if using statement such as below;
     * $where = " WHERE `buyer_username` = " . General::escape($get['buyer']); 
     * $query = DB::select('select * from jocom_transaction' . $where);
     * @return [type] [description]
     */    
    public function scopeEscape($query, $str) 
    {
        if (is_string($str))
        {
            $str = "'".$str."'";
        }
        elseif (is_bool($str))
        {
            $str = ($str === FALSE) ? 0 : 1;
        }
        elseif (is_null($str))
        {
            $str = 'NULL';
        }

        return $str;
    }

    /**
     * Audit trail for any amendment to DB
     * @param  [varchar(255)] $filename [System file name]
     * @param  [varchar(255)] $function [Function name]
     * @param  [text] $comment  [Event description]
     * @param  [varchar(255)] $username [Username]
     * @param  [varchar(30)] $usertype [CMS, APP]
     * @return [int]           [ID of audit trail inserted]
     */
    public static function audit_trail($filename = null, $function = null, $comment = null, $username = null, $usertype = null) 
    {

        $queries = DB::getQueryLog();
        $last_query = end($queries);

        $query = str_replace(array('%', '?'), array('%%', '%s'), $last_query['query']);
        $query = vsprintf($query, $last_query['bindings']);

        $details = array();
        $details['sql_str'] = $query;
        // $details['sql_str'] = serialize($last_query);
        $details['filename'] = $filename;
        $details['function'] = $function;
        $details['comment'] = $comment;
        $details['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $details['username'] = $username;
        $details['usertype'] = $usertype;
        $details['insert_date'] = date('Y-m-d H:i:s');

        $insert_id = DB::table('jocom_audit_trail')->insertGetId($details);

        return $insert_id;
        
    }

    
    
}


