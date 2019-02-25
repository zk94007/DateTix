<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Merchants_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this -> load -> model('api/av2/date_api_model');
        $this -> load -> model('api/av2/merchant_api_model');
        $this -> load -> model('api/av2/user_api_model');
        $this -> load -> model('api/av2/user_follow_merchant_api_model');
    }

    public function index_get() {

        $budget_id = $this -> get('budget_id');
        $sort_by = $this -> get('sort_by');
        $cuisine_ids = $this -> get('cuisine_ids');
        $neighborhood_ids = $this -> get('neighborhood_ids');
        $date_type_id = $this -> get('date_type_id');

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $merchants = $this -> merchant_api_model -> filter_merchants($budget_id, $sort_by, $cuisine_ids, $neighborhood_ids, $date_type_id, $user -> gps_lat, $user -> gps_lng);

        $this -> response(array(
            'data' => $merchants
        ), 200);
    }

    public function merchant_get($merchant_id) {

        if (empty($merchant_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to get merchant',
                        'detail' => 'Merchant id is not provided.'
                    )
                )
            ), 200);
        }

        $merchant = $this -> merchant_api_model -> get_merchant_with_photo($merchant_id);

        if (empty($merchant)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to get merchant',
                        'detail' => 'Merchant id is invalid.'
                    )
                )
            ), 200);
        }

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        // Distance
        if (!empty($user -> gps_lat) &&
            !empty($user -> gps_lng) &&
            !empty($merchant -> gps_lat) &&
            !empty($merchant -> gps_long)) {

            $distance = $this -> user_api_model -> distance_between_two_points($user -> gps_lat, $user -> gps_lng, $merchant -> gps_lat, $merchant -> gps_long, 'K');
            $merchant -> away_in_km = $distance;
        }

        // Upcoming dates count
        $upcoming_dates = $this -> date_api_model -> get_upcoming_dates_by_merchant_id($merchant_id);
        $upcoming_dates_count = count($upcoming_dates);
        $merchant -> upcoming_dates_count = $upcoming_dates_count;

        // Past dates count
        $past_dates = $this -> date_api_model -> get_past_dates_by_merchant_id($merchant_id);
        $past_dates_count = count($past_dates);
        $merchant -> past_dates_count = $past_dates_count;

        $follow_time = $this -> user_follow_merchant_api_model -> get_follow_time_with_params($this -> rest -> user_id, $merchant_id);
        $merchant -> follow_time = $follow_time;

        $this -> response(array(
            'data' => $merchant
        ), 200);
    }

    public function follow_post($merchant_id) {

        if (empty($merchant_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to follow merchant',
                        'detail' => 'Merchant id is not provided.'
                    )
                )
            ), 200);
        }

        $this -> user_follow_merchant_api_model -> do_follow_with_params($this -> rest -> user_id, $merchant_id);

        $this -> response(array(
            'meta' => array(
                'status' => TRUE,
                'detail' => 'Your operation was done successfully.'
            )
        ), 200);
    }

    public function unfollow_post($merchant_id) {

        if (empty($merchant_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to follow merchant',
                        'detail' => 'Merchant id is not provided.'
                    )
                )
            ), 200);
        }

        $this -> user_follow_merchant_api_model -> do_unfollow_with_params($this -> rest -> user_id, $merchant_id);

        $this -> response(array(
            'meta' => array(
                'status' => TRUE,
                'detail' => 'Your operation was done successfully.'
            )
        ), 200);
   }
}