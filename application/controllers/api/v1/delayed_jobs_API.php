<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Carbon/Carbon.php';

use Carbon\Carbon;

define('FAILED_JOB_ATTEMPT_INTERVAL_MINUTES', 10);

class Delayed_Jobs_API extends  REST_Controller {

    private $executing_delayed_job = NULL;

    function __construct()
    {
        parent::__construct();

        $this -> load -> model('api/v1/date_api_model');
        $this -> load -> model('api/v1/date_invite_api_model');
        $this -> load -> model('api/v1/date_type_api_model');
        $this -> load -> model('api/v1/delayed_job_api_model');
        $this -> load -> model('api/v1/merchant_api_model');
        $this -> load -> model('api/v1/user_api_model');
        $this -> load -> model('api/v1/user_decision_api_model');
    }

    public function execute_get() {

        // Get first queued delayed job
        $this->executing_delayed_job = $this->delayed_job_api_model->get_first_delayed_job_in_queue();

        // Execute
        if ($this->executing_delayed_job) {

            if ($this->executing_delayed_job->function_name) {

                // If failed job, check time duration after failed_at
                if ($this -> executing_delayed_job -> failed_at) {

                    $current_time = new DateTime();
                    $diff = $current_time -> diff(new DateTime($this -> executing_delayed_job -> failed_at));

                    $diff_minutes = $diff -> days * 24 * 60;
                    $diff_minutes += $diff -> h * 60;
                    $diff_minutes += $diff -> i;

                    if ($diff_minutes < FAILED_JOB_ATTEMPT_INTERVAL_MINUTES) {
                        $this -> response($this -> executing_delayed_job);
                    }
                }

                // Reset last error
                $this->delayed_job_api_model->reset_last_error_by_id($this->executing_delayed_job->delayed_job_id);

                // Lock the job
                $this->delayed_job_api_model->lock_delayed_job_by_id($this->executing_delayed_job->delayed_job_id);

                // Set run_at
                $this->delayed_job_api_model->set_run_at_now_by_id($this->executing_delayed_job->delayed_job_id);

                // Set custom error handler
                set_error_handler(function ($errno, $errstr, $errfile, $errline, array $errcontext) {
                    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
                });

                // Execute the job
                $function_name = $this->executing_delayed_job->function_name;
                $function_parameters_str = $this->executing_delayed_job->function_parameters;

                try {

                    $this->$function_name($function_parameters_str);

                    // Job was done successfully, so we remove job record from db
                    $this->delayed_job_api_model->delete_delayed_job($this->executing_delayed_job->delayed_job_id);

                } catch (ErrorException $e) {   // An error occurred while executing the job

                    // Set last error
                    $error = "Message: {$e -> getMessage()}\n";
                    $error .= "Filename: {$e -> getFile()}\n";
                    $error .= "Line number: {$e -> getLine()}";

                    $this->delayed_job_api_model->append_last_error_with_params($this->executing_delayed_job->delayed_job_id, $error);

                    // Set failed_at
                    $this->delayed_job_api_model->set_failed_at_now_by_id($this->executing_delayed_job->delayed_job_id);

                    // Unlock the job
                    $this->delayed_job_api_model->unlock_delayed_job_by_id($this->executing_delayed_job->delayed_job_id);

                    // Increase attempts count
                    $this->delayed_job_api_model->increase_attempts_count_by_id($this->executing_delayed_job->delayed_job_id);

                    // Increase queue_number to the last
                    $this->delayed_job_api_model->set_queue_number_to_the_last_by_id($this->executing_delayed_job->delayed_job_id);

                    // Set updated_at
                    $this->delayed_job_api_model->set_updated_at_now_by_id($this->executing_delayed_job->delayed_job_id);
                }

                // Restore error handler
                restore_error_handler();

            } else {

                // Function name is empty, so we remove the job from db
                $this->delayed_job_api_model->delete_delayed_job($this->executing_delayed_job->delayed_job_id);
            }

        } else {

            // No job in the db
        }

        $this -> response($this -> executing_delayed_job);
    }

    public function test_get() {

    }

    private function send_invitation_push_notifications_to_profile_matches($params_str) {

        $params = json_decode($params_str);

        $user_id = $params -> user_id;
        $date_id = $params -> date_id;
        $num_profile_matches_count = $params -> num_profile_matches_count;

        $user = $this -> user_api_model -> get_user($user_id);
        $date = $this -> date_api_model -> get_date($date_id);

        $profile_matches = $this -> user_api_model -> find_profile_matches($user_id, $num_profile_matches_count);
        if (!empty($profile_matches)) {

            $date_type = $this -> date_type_api_model -> get_date_type($date -> date_type_id);
            $merchant = $this -> merchant_api_model -> get_merchant($date -> merchant_id);

            $formatted_date_time = $this -> get_formatted_date_time($date -> date_time);

            $message = "{$user -> first_name} wants to meet you for {$date_type -> description} @ '{$merchant -> name}' at {$formatted_date_time}";
            $meta = array(
                'notification_type' => 'date_invite',
                'date_id' => $date_id
            );

            // Send push notifications
            foreach ($profile_matches as $profile_match) {
                $meta_response = $this -> user_api_model -> send_push_notification_to_user($profile_match['user_id'], $message, $meta);

                if ($meta_response['status'] == TRUE) {

                    // Insert date invite
                    $insert_date_invite_array['date_id'] = $date_id;
                    $insert_date_invite_array['invite_user_id'] = $profile_match['user_id'];
                    $insert_date_invite_array['invite_time'] = SQL_DATETIME;
                    $insert_date_invite_array['status'] = 0;

                    $this -> date_invite_api_model -> delete_date_invite_with_params($date_id, $profile_match['user_id']);
                    $this -> date_invite_api_model -> insert_date_invite($insert_date_invite_array);

                    // Insert user decision
                    $insert_user_decision_array['target_user_id'] = $profile_match['user_id'];
                    $insert_user_decision_array['user_id'] = $user_id;
                    $insert_user_decision_array['decision'] = 1;
                    $insert_user_decision_array['decision_time'] = SQL_DATETIME;

                    $this -> user_decision_api_model -> delete_user_decision_with_params($profile_match['user_id'], $user_id);
                    $this -> user_decision_api_model -> insert_user_decision($insert_user_decision_array);
                }
            }
        }
    }

    private function send_new_date_near_you_push_notifications($params_str) {

        $params = json_decode($params_str);

        $user_id = $params -> user_id;
        $date_id = $params -> date_id;

        $user = $this -> user_api_model -> get_user($user_id);
        $date = $this -> date_api_model -> get_date($date_id);
        $merchant = $this -> merchant_api_model -> get_merchant($date -> merchant_id);

        if (empty($user -> gps_lat) || empty($user -> gps_lng))
            return;

        $friends_near_user = $this -> user_api_model -> find_friends_near_user($user_id);

        if (!empty($friends_near_user)) {

            $message = "{$user -> first_name} has posted a date at {$merchant -> name}";
            $meta = array(
                'notification_type' => 'new_date_near_you',
                'date_id' => $date_id
            );

            foreach ($friends_near_user as $friend_near_user) {

                // Send push notification to the friend
                $this -> user_api_model -> send_push_notification_to_user($friend_near_user['user_id'], $message, $meta);
            }
        }
    }

    private function get_formatted_date_time($date_time_str) {

        $date_time = Carbon::createFromFormat('Y-m-d H:i:s', $date_time_str);

        // Check if today
        if ($date_time -> isToday()) {
            return $date_time -> format('H:i');
        }

        // Check if tomorrow
        if ($date_time -> isTomorrow()) {
            return 'tomorrow';
        }

        // Check if this week
        if ($date_time -> isFuture()) {

            $end_of_week = Carbon::now() -> endOfWeek();

            if ($date_time <= $end_of_week) {
                return $date_time -> format('l');
            }
        }

        // Otherwise
        return $date_time -> format('m/d/y');
    }
}