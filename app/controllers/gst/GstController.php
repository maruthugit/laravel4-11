<?php
 
class GstController extends BaseController {

    public function anyIndex()
    {
        $record = array();

        return View::make('gst.gst_listing', ['row' => $record]);
    }

    public function anySearch()
    {
        if (Input::has('report_year'))
            $year = Input::get('report_year');
        else
            $year = date('Y');

        if (Input::has('report_month'))
            $month = Input::get('report_month');
        else
            $month = '';

        $path = Config::get('constants.GST_REPORT_PATH') . '/' . $year;

        $file = glob($path . "/report_gst_" . $year . "_" .$month . "*.csv");

        // $file2 = glob($path . "/report_gst_seller_" . $year . "_" .$month . "*.csv");

        // $file = array_merge($file, $file2);

        rsort($file);

        $record = array(
                'report_year' => $year,
                'report_month' => $month,
                'type' => '-',
                'file' => $file
            );

        return View::make('gst.gst_listing', ['row' => $record]);
    }

    public function anyNewreport()
    {
        if (Input::has('report_year'))
            $year = Input::get('report_year');
        else
            $year = date('Y');

        if (Input::has('report_month'))
            $month = Input::get('report_month');
        else
            $month = '-';

        if (Input::has('report_day'))
            $day = Input::get('report_day');
        else
            $day = '-';

        if (Input::has('type'))
            $type = Input::get('type');
        else
            $type = '-';

        if ($type == 'month')
            $tenure = '3';
        else
            $tenure = '-';

        if (Input::has('report_year'))
            return Redirect::to('gstreport/generate/'.$year."/".$month."/".$day."/".$tenure."/".$type);
        else
            return View::make('gst.gst_new', ['row' => $record]);

        // $path = Config::get('constants.GST_REPORT_PATH') . '/' . $year;

        // $file = glob($path . "/report_gst_" . $year . "_" .$month . "*.csv");

        // // $file2 = glob($path . "/report_gst_seller_" . $year . "_" .$month . "*.csv");

        // // $file = array_merge($file, $file2);

        // rsort($file);

        // $record = array(
        //         'report_year' => $year,
        //         'report_month' => $month,
        //         'type' => '-',
        //         'file' => $file
        //     );

        // return View::make('gst.gst_listing', ['row' => $record]);
    }

    public function anyFiles($file=null)
    {
        $tempfile = base64_decode(urldecode($file));

        if(is_file($tempfile)) {
            return Response::download($tempfile);
        }
        else {
            echo "<br>File not exists!";
        }

    }

    public function anyRemove($file=null)
    {
        $error = false;

        if (Input::has('url'))
            $url = Input::get('url');
        else
            $error = true;

        if (Input::has('report_year'))
            $year = Input::get('report_year');
        else
            $year = '';

        if (Input::has('report_month'))
            $month = Input::get('report_month');
        else
            $month = '';

        if ($error == false)
        {
            $tempfile = base64_decode(urldecode($url));

            if(unlink($tempfile))
            {
                $temp = explode("/", $tempfile);

                return Redirect::to('gstreport/search?report_year='.$year."&report_month=".$month)->with('success', 'File: '.$temp[3].' has been deleted.');
            }
        }

        return Redirect::to('gstreport/search?report_year='.$year."&report_month=".$month)->with('message', 'Delete failed. Data has not changed');

        

    }

    public function anyGenerate($year = null, $month = null, $day = null, $tenure = null, $tenuretype = null)
    {
        if (isset($year))
        {
            $select_year = $year;
        }
        else
        {
            $select_year = date('Y');
        }

        if (isset($month) AND $month != '-')
        {
            $select_month = $month;
        }
        else
        {
            // $select_month = date('m');
            $select_month = date('m', strtotime('last month'));
        }

        $select_day = '';
        $select_tenure = '';
        $select_tenuretype = '';

        if (isset($day))
        {
            if($day != '-')
                $select_day = $day;
        }

        if (isset($tenure))
        {
            if($tenure != '-')
                $select_tenure = $tenure;
        }

        if (isset($tenuretype))
        {
            if($tenuretype != '-')
                $select_tenuretype = $tenuretype;
        }

        $period = $select_year . "-" . $select_month;

        if ($select_day != '')
            $period = $period . "-" . $select_day;

        $get['year'] = $select_year;
        $get['month'] = $select_month;
        $get['day'] = $select_day;
        $get['tenure'] = $select_tenure;
        $get['tenuretype'] = $select_tenuretype;
        $get['period'] = $period;
        
        $trans = Transaction::get_transaction($period);

        for ($x = 1; $x < $get['tenure']; $x++)
        {
            if ($get['tenuretype'] == 'month')
            {
                $tempmonth = $get['month'] + $x;

                if ($tempmonth < 10)
                    $tempmonth = '0' . $tempmonth;

                if ($tempmonth > 12)
                {
                    $tempyear = $get['year'] + 1;
                    $tempmonth--;
                }                    
                else
                    $tempyear = $get['year'];

                $period = $tempyear . "-" . $tempmonth;

                $trans2 = Transaction::get_transaction($period);

                if (count($trans2) > 0)
                    $trans = array_merge($trans, $trans2);
                
            }
            
        }

        $report = Transaction::calculate_gst($trans);
        $report_seller = Transaction::calculate_gst_seller($trans);


        if ($report != null)
        {
            $done = Transaction::gst_report($report, $get['year'], $get['month'], $get['tenure'], $get['tenuretype']);
            $done = Transaction::gst_seller_report($report_seller, $get['year'], $get['month'], $get['tenure'], $get['tenuretype']);
        }

        return Redirect::to('gstreport/search?report_year='.$get['year']."&report_month=".$get['month'])->with('success', 'Report has been generated.');
        
    }

    public function anyAutomonthly()
    {
        $select_month = date('m', strtotime('last month'));

        if ($select_month == 12)
            $select_year = date('Y', strtotime('last year'));
        else
            $select_year = date('Y');

        $period = $select_year . "-" . $select_month;

        $get['year'] = $select_year;
        $get['month'] = $select_month;
        $get['period'] = $period;
        
        $trans = Transaction::get_transaction($period);

        $report = Transaction::calculate_gst($trans);
        // $report_seller = Transaction::calculate_gst_seller($trans);

        

        if ($report != null)
        {
            $done = Transaction::gst_report($report, $get['year'], $get['month']);
            $done = Transaction::gst_seller_report($report, $get['year'], $get['month']);
        }
    }

    public function anyAutoquarterly()
    {
        $select_month = date('m', strtotime('3 months ago'));

        if ($select_month == 12)
            $select_year = date('Y', strtotime('last year'));
        else
            $select_year = date('Y');

        $period = $select_year . "-" . $select_month;

        $get['year'] = $select_year;
        $get['month'] = $select_month;
        $get['period'] = $period;
        
        $trans = Transaction::get_transaction($period);

        for ($x = 1; $x < 3; $x++)
        {            
            $tempmonth = $get['month'] + $x;

            if ($tempmonth < 10)
                $tempmonth = '0' . $tempmonth;

            if ($tempmonth > 12)
            {
                $tempyear = $get['year'] + 1;
                $tempmonth--;
            }                    
            else
                $tempyear = $get['year'];

            $period = $tempyear . "-" . $tempmonth;

            $trans2 = Transaction::get_transaction($period);

            $trans = array_merge($trans, $trans2);
            
        }

        $report = Transaction::calculate_gst($trans);

        if ($report != null)
        {
            $done = Transaction::gst_report($report, $get['year'], $get['month'], 3, 'month');
            $done = Transaction::gst_seller_report($report, $get['year'], $get['month'], 3, 'month');
        }
    }

}

?>