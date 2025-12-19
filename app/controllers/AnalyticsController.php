<?php

use Helper\ImageHelper as Image;

class AnalyticsController extends BaseController {

    /**
     * Default index for apps feed
     * @return [type] [description]
     */
    public function anyIndex()
    {
        echo "Invalid URL";
    }

    public function google_analytics($id = null)
    {
        if (isset($id))
            return View::make('analytics.google_analytics')->with('id', $id);
        else
            echo "Error: Invalid Transaction ID";
    }
    
    private function getService(){
        
            // Creates and returns the Analytics service object.

            // Load the Google API PHP Client Library.
            require_once app_path('library/google-api-php-client/src/Google/autoload.php');

            // Use the developers console and replace the values with your
            // service account email, and relative location of your key file.
            $service_account_email = Config::get('constants.GOOGLE_SERVICE_ACCOUNT_EMAIL');
            $key_file_location = Config::get('constants.GOOGLE_CLIENT_SECRET');

            // Create and configure a new client object.
            $client = new Google_Client();
            $client->setApplicationName("HelloAnalytics");
            $analytics = new Google_Service_Analytics($client);

            // Read the generated client_secrets.p12 key.
            $key = file_get_contents($key_file_location);
            $cred = new Google_Auth_AssertionCredentials(
                $service_account_email,
                array(Google_Service_Analytics::ANALYTICS_READONLY),
                $key
            );
            $client->setAssertionCredentials($cred);
            if($client->getAuth()->isAccessTokenExpired()) {
              $client->getAuth()->refreshTokenWithAssertion($cred);
}

            return $analytics;
          
    }
    
    private function getFirstprofileId(&$analytics) {
        // Get the user's first view (profile) ID.

        // Get the list of accounts for the authorized user.
        $accounts = $analytics->management_accounts->listManagementAccounts();

        if (count($accounts->getItems()) > 0) {
          $items = $accounts->getItems();
          $firstAccountId = $items[0]->getId();

          // Get the list of properties for the authorized user.
          $properties = $analytics->management_webproperties
              ->listManagementWebproperties($firstAccountId);

          if (count($properties->getItems()) > 0) {
            $items = $properties->getItems();
            $firstPropertyId = $items[0]->getId();

            // Get the list of views (profiles) for the authorized user.
            $profiles = $analytics->management_profiles
                ->listManagementProfiles($firstAccountId, $firstPropertyId);

            if (count($profiles->getItems()) > 0) {
              $items = $profiles->getItems();

              // Return the first view (profile) ID.
              return $items[0]->getId();

            } else {
              throw new Exception('No views (profiles) found for this user.');
            }
          } else {
            throw new Exception('No properties found for this user.');
          }
        } else {
          throw new Exception('No accounts found for this user.');
        }
    }


    /*
     * Desc : To get top 10 products were clicked/viewed from google Analytics for API call
     * Return : XML format
     */
        
    public function getTop10ViewedItems(){
        
        
        $collection = array();
        $data = array();
        $counter = 0;
        $max_record = 10;
        $data['enc'] = 'UTF-8';
        $get['lang'] = 'en';
        
        if(Input::has('lang')) {
            $get['lang'] = strtolower(trim(Input::get('lang'))) ;    
        }
        
        // Call GA service
        $analytics = $this->getService();
        // Get Google Analytics Profile ID
        $profileId = $this->getFirstProfileId($analytics);
        
        // Call Google Analytic API
        $month_ini = new DateTime("first day of last month");
        $month_end = new DateTime("last day of last month");
        $fromDate = $month_ini->format('Y-m-d');
        $toDate = $month_end->format('Y-m-d');
        
        $results = $analytics->data_ga->get(
            'ga:'.$profileId,
            $fromDate,
            $toDate,
            'ga:totalEvents',
                array('dimensions' => 'ga:eventCategory,ga:eventAction',
                        'filters'=>'ga:eventAction=~Show_product_detail',
                        'sort' => '-ga:totalEvents', // (-) for DESC order
                        'max-results'=>'20')
                );

        /* 
         * Filter the returned result from Google Analytics
         */
        foreach ($results->getRows() as $row) { 	 	
                
            $productName = trim($row[1],"Show_product_detail_");
            $ProductInfo = Product::getByProductName($productName);

            if(count($ProductInfo) > 0 ){
                if($ProductInfo->status == 1){ // if status is active
                    
                    switch ($get['lang']) {
                        case "cn":
                            $ProductName = $ProductInfo->name_cn;
                            break;
                        case "my":
                            $ProductName = $ProductInfo->name_my;
                            break;
                        default:
                            $ProductName = $ProductInfo->name;
                            break;
                    }
                    
                    $subCol = array(
                        "id" => $ProductInfo->id,
                        "sku" => $ProductInfo->sku,
                        "name" => $ProductName,
                        "thumbnail" => Image::link(Config::get('constants.PRODUCT_IMAGE_THUMB_FILE_PATH').$ProductInfo->img_1), // Image Link
                        "qrcode" => $ProductInfo->qrcode,
                    );

                    array_push($collection, $subCol); 
                    $counter++;
                }
            }
            // Stop if the total items reached the max record
            if($counter == $max_record){
                break;
            }
        }
        
        //return $collection;
        
        $data['xml_data']['title'] = "Last month most viewed items";
        $data['xml_data']['item'] = $collection;

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
        
    }


    
    
}
?>
