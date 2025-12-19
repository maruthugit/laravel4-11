<?php

class MailchimpReportController extends BaseController {

    public function __construct() {
        $this->beforeFilter('auth');
    }

    public function anyLists() {

        $URL = Config::get('constants.MAILCHIMP_REPORT_ENDPOINT');
        $header = array(
            'Content-Type: application/json',
            'Authorization: apikey ' . Config::get('constants.MAILCHIMP_API_KEY'),
        );

        $ch = curl_init($URL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
       
        $result = curl_exec($ch);
        $reports = json_decode($result)->reports;

        $array = array();
        $count = 0;

        foreach ($reports as $report) {
            $count++;
            $id = $report->id;
            $title = $report->campaign_title;
            $subject_line = $report->subject_line;
            $send_time = date_format(date_create($report->send_time), 'D, j F Y g:i A');
            $view = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="/mailchimp-report/view/'.$id.'"><i class="fa fa-eye"></i></a>';

            array_push($array, [$count, $title, $subject_line, $send_time, $view]);
        }

        return ['recordsTotal' => $count, 'recordsFiltered' => $count, 'data' => $array];
    }

    public function anyIndex() {
        return View::make('mailchimp-report.index');
    }

    public function getView($id) {

        $URL = Config::get('constants.MAILCHIMP_REPORT_ENDPOINT') . $id;
        $header = array(
            'Content-Type: application/json',
            'Authorization: apikey ' . Config::get('constants.MAILCHIMP_API_KEY'),
        );

        $ch = curl_init($URL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
       
        $result = curl_exec($ch);

        $reports = json_decode($result);

        return View::make('mailchimp-report.view')->with(array(
            'report' => $reports
        ));
    }

    public function getTimeseries($id) {
        $URL = Config::get('constants.MAILCHIMP_REPORT_ENDPOINT') . $id;
        $header = array(
            'Content-Type: application/json',
            'Authorization: apikey ' . Config::get('constants.MAILCHIMP_API_KEY'),
        );

        $ch = curl_init($URL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
       
        $result = curl_exec($ch);

        $reports = json_decode($result);

        foreach ($reports->timeseries as $timeseries) {
            $timeseries->timestamp = date_format(date_create($timeseries->timestamp), 'g:i A');
        }

        return $reports->timeseries;
    }

    public function getClicks($id) {
        $URL = Config::get('constants.MAILCHIMP_REPORT_ENDPOINT') . $id . '/click-details?count=1000';
        $header = array(
            'Content-Type: application/json',
            'Authorization: apikey ' . Config::get('constants.MAILCHIMP_API_KEY'),
        );

        $ch = curl_init($URL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
       
        $result = curl_exec($ch);
        $reports = json_decode($result);

        $index_arr = array();

        for ($i = 0; $i < count($reports->urls_clicked); $i++) {
            $index_arr[$i] = $reports->urls_clicked[$i]->unique_clicks;
        }

        arsort($index_arr);

        $clicks = array();
        $counter = 0;

        foreach ($index_arr as $index => $value) {
            if ($counter < 5) {
                array_push($clicks, $reports->urls_clicked[$index]);
            }
            $counter++;
        }

        return $clicks;
    }

    public function getOpens($id) {
        $URL = Config::get('constants.MAILCHIMP_REPORT_ENDPOINT') . $id . '/open-details?count=1000';
        $header = array(
            'Content-Type: application/json',
            'Authorization: apikey ' . Config::get('constants.MAILCHIMP_API_KEY'),
        );

        $ch = curl_init($URL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
       
        $result = curl_exec($ch);
        $reports = json_decode($result);

        $index_arr = array();

        for ($i = 0; $i < count($reports->members); $i++) {
            $index_arr[$i] = $reports->members[$i]->opens_count;
        }

        arsort($index_arr);

        $opens = array();
        $counter = 0;

        foreach ($index_arr as $index => $value) {
            if ($counter < 5) {
                array_push($opens, $reports->members[$index]);
            }
            $counter++;
        }

        return $opens;
    }

    public function getLocations($id) {
        $URL = Config::get('constants.MAILCHIMP_REPORT_ENDPOINT') . $id . '/locations?count=1000';
        $header = array(
            'Content-Type: application/json',
            'Authorization: apikey ' . Config::get('constants.MAILCHIMP_API_KEY'),
        );

        $ch = curl_init($URL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
       
        $result = curl_exec($ch);
        $reports = json_decode($result);

        $index_arr = array();

        for ($i = 0; $i < count($reports->locations); $i++) {
            $index_arr[$i] = $reports->locations[$i]->opens;
        }

        arsort($index_arr);

        $locations = array();
        $counter = 0;

        foreach ($index_arr as $index => $value) {
            if ($counter < 5) {
                array_push($locations, $reports->locations[$index]);
            }
            $counter++;
        }

        return $locations;
    }
}
?>