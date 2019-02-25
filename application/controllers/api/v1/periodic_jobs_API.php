<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Periodic_Jobs_API extends  REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this -> load -> model('api/v1/date_api_model');
        $this -> load -> model('api/v1/date_invite_api_model');
        $this -> load -> model('api/v1/date_type_api_model');
        $this -> load -> model('api/v1/merchant_api_model');
        $this -> load -> model('api/v1/user_api_model');
        $this -> load -> model('api/v1/user_decision_api_model');
    }

    public function execute_get() {

        $this -> send_remind_push_notifications_to_inactive_users();

        $this -> response(true);
    }

    public function test_get() {

    }

    private function send_remind_push_notifications_to_inactive_users() {

        $inactive_users = $this -> user_api_model -> find_inactive_users();

        if (!empty($inactive_users)) {

            // Send push notifications
            foreach ($inactive_users as $inactive_user) {

                $message = "{$inactive_user['first_name']}, post a date now to meet new people tonight";
                $meta = array(
                    'notification_type' => 'need_to_post_date'
                );

                $meta_response = $this -> user_api_model -> send_push_notification_to_user($inactive_user['user_id'], $message, $meta);

                if ($meta_response['status'] == TRUE) {

                    // Push notification is sent successfully
                    $update_user_array['last_inactive_status_remind_time'] = SQL_DATETIME;
                    $this -> user_api_model -> update_user($inactive_user['user_id'], $update_user_array);
                }
            }
        }

    }
}