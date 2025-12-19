<?php

class ApiCharityController extends BaseController
{
    public function anyIndex()
    {
        $get = $_POST;

        if ($get['req'] == 'login')
        {
            $data = ApiCharity::getLogin($get);
        }
        else
        {
            if ($get['charity_id'] == "" or $get['charity_id'] == NULL)
            {
                $data['message'] = "error";
            }
            else
            {
                switch ($get['req'])
                {
                    case 'dashboard':
                        $data = ApiCharity::getDashboard($get);
                        break;

                    case 'donation':
                        $data = ApiCharity::getDonation($get);
                        break;

                    case 'product':
                        $data = ApiCharity::getProduct($get);
                        break;

                    case 'update_product':
                        $data = ApiCharity::updateProduct($get);
                        break;

                    case 'profile':
                        $data = ApiCharity::getProfile($get);
                        break;

                    case 'update_profile':
                        $data = ApiCharity::updateProfile($get);
                        break;
                    
                    default:
                        $data['message'] = "error";
                        break;
                }
            }
        }

               

        return $data;
    }

}
