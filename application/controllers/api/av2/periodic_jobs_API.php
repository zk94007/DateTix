<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Periodic_Jobs_API extends  REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this -> load -> model('api/av2/date_api_model');
        $this -> load -> model('api/av2/date_applicant_api_model');
        $this -> load -> model('api/av2/date_invite_api_model');
        $this -> load -> model('api/av2/date_type_api_model');
        $this -> load -> model('api/av2/log_push_notification_api_model');
        $this -> load -> model('api/av2/merchant_api_model');
        $this -> load -> model('api/av2/user_api_model');
        $this -> load -> model('api/av2/user_decision_api_model');
        $this -> load -> model('api/av2/user_follow_date_api_model');
    }

    public function execute_get() {

        $this -> send_remind_push_notifications_to_inactive_users();
        $this -> send_date_remind_push_notifications_to_chosen_users();
        $this -> send_date_remind_push_notifications_to_hosting_users();
        $this -> send_follow_reminder_push_notifications_to_date_followers();

        $this -> response(true);
    }

    public function test_get() {
        $this -> send_follow_reminder_push_notifications_to_date_followers();
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

    private function send_date_remind_push_notifications_to_chosen_users() {

        $upcoming_dates = $this -> date_api_model -> get_12_hrs_upcoming_dates();

        if (!empty($upcoming_dates)) {
            foreach ($upcoming_dates as $upcoming_date) {

                // Check if push notification is already sent for this date
                $already_sent = $this -> log_push_notification_api_model -> check_notification_chosen_date_in_12_hours($upcoming_date['date_id']);

                if (!$already_sent) {

                    $requested_user = $this->user_api_model->get_user($upcoming_date['requested_user_id']);
                    $date_applicants = $this->date_applicant_api_model->get_date_applicants_by_date_id($upcoming_date['date_id']);

                    if (!empty($date_applicants)) {
                        foreach ($date_applicants as $date_applicant) {

                            if ($date_applicant['is_chosen'] == 1) {

                                $message = "You have a date with {$requested_user -> first_name} in less than 12 hours.";
                                $meta = array(
                                    'notification_type' => 'chosen_date_in_12_hours',
                                    'date_id' => $upcoming_date['date_id']
                                );
                                $this->user_api_model->send_push_notification_to_user($date_applicant['applicant_user_id'], $message, $meta);

                                // Insert log
                                $this -> log_push_notification_api_model -> insert_notification_chosen_date_in_12_hours($upcoming_date['date_id']);

                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    private function send_date_remind_push_notifications_to_hosting_users() {

        $upcoming_dates = $this -> date_api_model -> get_12_hrs_upcoming_dates();

        if (!empty($upcoming_dates)) {
            foreach ($upcoming_dates as $upcoming_date) {

                // Check if push notification is already sent for this date
                $already_sent = $this -> log_push_notification_api_model -> check_notification_hosted_date_in_12_hours($upcoming_date['date_id']);

                if (!$already_sent) {

                    $message = "You have a date in less than 12 hours.";
                    $meta = array(
                        'notification_type' => 'hosted_date_in_12_hours',
                        'date_id' => $upcoming_date['date_id']
                    );
                    $this->user_api_model->send_push_notification_to_user($upcoming_date['requested_user_id'], $message, $meta);

                    // Insert log
                    $this -> log_push_notification_api_model -> insert_notification_hosted_date_in_12_hours($upcoming_date['date_id']);
                }
            }
        }
    }

    private function send_follow_reminder_push_notifications_to_date_followers() {

        $upcoming_dates = $this -> date_api_model -> get_12_hrs_upcoming_dates();

        if (!empty($upcoming_dates)) {
            foreach ($upcoming_dates as $upcoming_date) {

                // Check if push notification is already sent for this date
                $already_sent = $this -> log_push_notification_api_model -> check_notification_following_date_in_12_hours($upcoming_date['date_id']);

                if (!$already_sent) {

                    $merchant = $this->merchant_api_model->get_merchant($upcoming_date['merchant_id']);
                    $following_user_ids = $this->user_follow_date_api_model->get_following_user_ids_by_date_id($upcoming_date['date_id']);

                    if (!empty($following_user_ids)) {
                        foreach ($following_user_ids as $following_user_id) {

                            $message = "Dating at {$merchant -> name} you followed is starting in less than 12 hours.";
                            $meta = array(
                                'notification_type' => 'following_date_in_12_hours',
                                'date_id' => $upcoming_date['date_id']
                            );
                            $this->user_api_model->send_push_notification_to_user($following_user_id, $message, $meta);

                            // Insert log
                            $this -> log_push_notification_api_model -> insert_notification_following_date_in_12_hours($upcoming_date['date_id']);
                        }
                    }
                }
            }
        }
    }
}