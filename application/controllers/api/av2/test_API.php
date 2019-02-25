<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';
require APPPATH . '/libraries/Carbon/Carbon.php';

use Carbon\Carbon;

class Test_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this -> load -> model('api/av2/date_api_model');
        $this -> load -> model('api/av2/user_api_model');
        $this -> load -> model('api/av2/date_type_api_model');
        $this -> load -> model('api/av2/merchant_api_model');
    }

    public function index_get() {

        $from_user_id = 3119;
        $to_user_id = 3;
        $date_id = 1079;
        $date_applicant_id = 12;

        $user = $this -> user_api_model -> get_user($from_user_id);
        $date = $this -> date_api_model -> get_date($date_id);
        $merchant = $this -> merchant_api_model -> get_merchant($date -> merchant_id);

        $date_time = Carbon::createFromFormat('Y-m-d H:i:s', $date -> date_time);

        $message = "You have a date in less than 12 hours.";
        $meta = array(
            'notification_type' => 'hosted_date_in_12_hours',
            'date_id' => $date_id
        );
        $meta_response = $this -> user_api_model -> send_push_notification_to_user($to_user_id, $message, $meta);

        $this -> response($meta_response, 200);
    }

}