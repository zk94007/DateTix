<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';
require APPPATH . '/libraries/Carbon/Carbon.php';

use Carbon\Carbon;

class Users_API extends MY_REST_Controller {

    function __construct() {
        parent::__construct();

        $this -> load -> model('api/av2/api_key_model');

        $this -> load -> model('api/av2/annual_income_range_api_model');
        $this -> load -> model('api/av2/body_type_api_model');
        $this -> load -> model('api/av2/child_status_api_model');
        $this -> load -> model('api/av2/child_plan_api_model');

        $this -> load -> model('api/av2/date_api_model');
        $this -> load -> model('api/av2/date_applicant_api_model');
        $this -> load -> model('api/av2/date_decision_api_model');
        $this -> load -> model('api/av2/date_invite_api_model');
        $this -> load -> model('api/av2/date_review_api_model');
        $this -> load -> model('api/av2/date_type_api_model');
        $this -> load -> model('api/av2/drinking_status_api_model');
        $this -> load -> model('api/av2/ethnicity_api_model');
        $this -> load -> model('api/av2/exercise_frequency_api_model');
        $this -> load -> model('api/av2/gender_api_model');
        $this -> load -> model('api/av2/merchant_api_model');
        $this -> load -> model('api/av2/relationship_status_api_model');
        $this -> load -> model('api/av2/smoking_status_api_model');
        $this -> load -> model('api/av2/super_date_api_model');

        $this -> load -> model('api/av2/user_api_model');
        $this -> load -> model('api/av2/user_decision_api_model');
        $this -> load -> model('api/av2/user_fb_friend_api_model');
        $this -> load -> model('api/av2/user_membership_option_api_model');
        $this -> load -> model('api/av2/user_photo_api_model');
        $this -> load -> model('api/av2/user_spoken_language_api_model');

        $this -> load -> model('api/av2/user_descriptive_word_api_model');
        $this -> load -> model('api/av2/user_interest_api_model');
        $this -> load -> model('api/av2/interest_api_model');
        $this -> load -> model('api/av2/interest_category_api_model');

        $this -> load -> model('api/av2/user_job_api_model');
        $this -> load -> model('api/av2/user_school_api_model');

        $this -> load -> model('api/av2/relationship_type_api_model');
        $this -> load -> model('api/av2/user_want_relationship_type_api_model');

        $this -> load -> model('api/av2/user_want_ethnicity_api_model');
        $this -> load -> model('api/av2/user_want_gender_api_model');

        $this -> load -> model('api/av2/user_decision_undo_request_api_model');
        $this -> load -> model('api/av2/user_follow_date_api_model');
        $this -> load -> model('api/av2/user_follow_merchant_api_model');
        $this -> load -> model('api/av2/user_follow_user_api_model');
        $this -> load -> model('api/av2/user_visit_user_api_model');

        $this -> load -> model('model_user');
    }

    public function sign_out_post() {

        $this -> api_key_model -> delete_api_key_for_user_id($this -> rest -> user_id);

        // Remove device token from user
        $update_user_array['device_token'] = '';
        $this -> user_api_model -> update_user($this -> rest -> user_id, $update_user_array);

        $this -> response(array(
            'meta' => array(
                'status' => TRUE,
                'detail' => 'API Key and Device Token are removed successfully.'
            )
        ), 200);
    }

    /* ============================= Start - People =========================*/

    public function find_get() {

        $offset = $this -> get('offset');
        $limit = $this -> get('limit');
        $sort_by_distance = $this -> get('sort_by_distance');

        if (empty($offset)) $offset = 0;
        if (empty($limit)) $limit = 0;
        if (empty($sort_by_distance)) $sort_by_distance = 0;

        $found_users = $this -> find_users($offset, $limit, $sort_by_distance);

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $this -> response(array(
            'data' => $found_users,
            'included' => array(
                'user' => array(
                    'attributes' => $user
                )
            )
        ), 200);
    }

    private function find_users($offset = 0, $limit = 0, $sort_by_distance = 0) {

        $me = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $found_user_ids = $this -> user_api_model -> find_people_ids($this -> rest -> user_id, $sort_by_distance, $limit, $offset);

        $found_users = array();
        if (!empty($found_user_ids)) {

            foreach($found_user_ids as $found_user_id) {

                $user = $this -> user_api_model -> get_user($found_user_id);
                $user_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($found_user_id);

                $common_interests = $this -> user_interest_api_model -> get_common_interests($this -> rest -> user_id, $found_user_id);
                $mutual_friends = $this -> user_fb_friend_api_model -> get_mutual_friends($this -> rest -> user_id, $found_user_id);

                $away_in_km = 0;
                if (!empty($me -> gps_lat) &&
                    !empty($me -> gps_lng) &&
                    !empty($user -> gps_lat) &&
                    !empty($user -> gps_lng)) {

                    $away_in_km = $this -> user_api_model -> distance_between_two_points($me -> gps_lat, $me -> gps_lng, $user -> gps_lat, $user -> gps_lng, 'K');
                }

                unset($found_user);
                $found_user['attributes'] = $user;
                $found_user['relationships']['user_photos'] = $user_photos;
                $found_user['relationships']['common_interests'] = $common_interests;
                $found_user['relationships']['mutual_friends'] = $mutual_friends;
                $found_user['meta']['away_in_km'] = $away_in_km;

                $found_users[] = $found_user;
            }
        }

        return $found_users;
    }

    public function find_people_filter_params_get() {

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $user_want_gender_ids = $this -> user_want_gender_api_model -> get_gender_ids_by_user_id($this -> rest -> user_id);
        $user_want_genders = array();
        if (!empty($user_want_gender_ids)) {
            foreach ($user_want_gender_ids as $user_want_gender_id) {
                $gender = $this->gender_api_model->get_gender($user_want_gender_id);
                $user_want_genders[] = $gender;
            }
        }

        $user_want_ethnicity_ids = $this -> user_want_ethnicity_api_model -> get_ethnicity_ids_by_user_id($this -> rest -> user_id);
        $user_want_ethnicities = array();
        if (!empty($user_want_ethnicity_ids)) {
            foreach ($user_want_ethnicity_ids as $user_want_ethnicity_id) {
                $ethnicity = $this->ethnicity_api_model->get_ethnicity($user_want_ethnicity_id);
                $user_want_ethnicities[] = $ethnicity;
            }
        }

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(
                    'user_want_genders' => $user_want_genders,
                    'user_want_ethnicities' => $user_want_ethnicities
                )
            )
        ), 200);
    }

    public function find_people_filter_params_post() {

        $want_gender_ids = $this -> post('want_gender_ids');
        $want_age_range_lower = $this -> post('want_age_range_lower');
        $want_age_range_upper = $this -> post('want_age_range_upper');
        $want_ethnicity_ids = $this -> post('want_ethnicity_ids');

        // Build update array
        if (!empty($want_age_range_lower))
            $update_array['want_age_range_lower'] = $want_age_range_lower;

        if (!empty($want_age_range_upper))
            $update_array['want_age_range_upper'] = $want_age_range_upper;

        if (!empty($update_array)) {

            // Update user data
            $this -> user_api_model -> update_user($this -> rest -> user_id, $update_array);
        }

        if (empty($want_gender_ids)) {
            // User selects no gender
            $this -> user_want_gender_api_model -> delete_user_want_genders_by_user_id($this -> rest -> user_id);
        } else {
            $this -> user_want_gender_api_model -> update_user_want_records_with_gender_ids($this -> rest -> user_id, $want_gender_ids);
        }

        if (empty($want_ethnicity_ids)) {
            // User selects no ethnicity
            $this -> user_want_ethnicity_api_model -> delete_user_want_ethnicities_by_user_id($this -> rest -> user_id);
        } else {
            $this -> user_want_ethnicity_api_model -> update_user_want_records_with_ethnicity_ids($this -> rest -> user_id, $want_ethnicity_ids);
        }

        // Get updated user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user
            )
        ), 200);
    }

    public function invite_post($user_id) {

        if (empty($user_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to process dislike',
                        'detail' => 'User id is not provided.'
                    )
                )
            ), 200);
        }

        $date_id = $this -> post('date_id');

        if (empty($date_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to process dislike',
                        'detail' => 'Date id is not provided.'
                    )
                )
            ), 200);
        }

        $date = $this -> date_api_model -> get_date($date_id);

        // Insert date invite
        $insert_date_invite_array['date_id'] = $date_id;
        $insert_date_invite_array['invite_user_id'] = $user_id;
        $insert_date_invite_array['invite_time'] = SQL_DATETIME;
        $insert_date_invite_array['status'] = '0';

        $this -> date_invite_api_model -> delete_date_invite_with_params($date_id, $user_id);
        $date_invite_id = $this -> date_invite_api_model -> insert_date_invite($insert_date_invite_array);
        $date_invite = $this -> date_invite_api_model -> get_date_invite($date_invite_id);

        // Insert new user decision
        $insert_user_decision_array['target_user_id'] = $user_id;
        $insert_user_decision_array['user_id'] = $this -> rest -> user_id;
        $insert_user_decision_array['decision'] = '1';
        $insert_user_decision_array['decision_time'] = SQL_DATETIME;

        $this -> user_decision_api_model -> delete_user_decision_with_params($user_id, $this -> rest -> user_id);
        $user_decision_id = $this -> user_decision_api_model -> insert_user_decision($insert_user_decision_array);
        $user_decision = $this -> user_decision_api_model -> get_user_decision($user_decision_id);

        // Send Push Notification
        $friend = $this -> user_api_model -> get_user($user_id);
        if ($friend -> want_pn_date_invite == 1) {  // Check friend's settings

            $user = $this -> user_api_model -> get_user($this -> rest -> user_id);
            $date_type = $this -> date_type_api_model -> get_date_type($date -> date_type_id);
            $merchant = $this -> merchant_api_model -> get_merchant($date -> merchant_id);

            $formatted_date_time = $this -> get_formatted_date_time($date -> date_time);

            $message = "{$user -> first_name} wants to meet you for {$date_type -> description} @ '{$merchant -> name}' at {$formatted_date_time}";
            $meta = array(
                'notification_type' => 'date_invite',
                'date_id' => $date_id
            );
            $meta_response = $this -> user_api_model -> send_push_notification_to_user($user_id, $message, $meta);
        }

        /* Temporarily commented [TODO]
        // Send email
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);
        $invited_user = $this -> user_api_model -> get_user($user_id);
        $invited_user_email = $this -> user_email_api_model -> get_user_email_by_user_id($user_id);

        if(!empty($invited_user_email)) {

            $subject = $dateDetail['first_name'] . translate_phrase(" has invited you to date ") . $gender_type . translate_phrase(" for ") . $dateDetail['date_type'] . " @ " . $dateDetail['name'] . " " . print_date_daytime($dateDetail['date_time']);
            $email_content = $dateDetail['first_name'] . translate_phrase(" has invited you to date ") . $gender_type . translate_phrase(" for ") . $dateDetail['date_type'] . " @ " . $dateDetail['name'] . " " . print_date_daytime($dateDetail['date_time']) . translate_phrase(" Click on the button below to meet ") . $gender_type . " for the date:";
            $data['email_content'] = $email_content;
            $data['btn_text'] = translate_phrase('Apply Date');
            $return_url = base_url() . "dates/find_dates/" . $date_id;

            $user_link = $this -> utility -> encode($inviteUserDetail['user_id']);
            if ($inviteUserDetail['password']) {
                $user_link .= '/' . $inviteUserDetail['password'];
            }
            $data['btn_link'] = base_url() . 'home/mail_action/' . $user_link . '?return_to='.$return_url;


            $data['email_title'] = '';
            $email_template = $this -> load -> view('email/common', $data, true);

            $this -> datetix -> mail_to_user($inviteUserDetail['email_address'], $subject, $email_template);
            return $inviteUserDetail['first_name'];
        }
        */

        $this -> response(array(
            'data' => array(
                'attributes' => $date,
                'relationships' => array(
                    'date_invite' => $date_invite
                )
            ),
            'meta' => !empty($meta_response) ? $meta_response : null
        ), 200);
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

    public function dislike_post($user_id) {

        if (empty($user_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to process dislike',
                        'detail' => 'User id is not provided.'
                    )
                )
            ), 200);
        }

        // Insert user decision
        $insert_array['target_user_id'] = $user_id;
        $insert_array['user_id'] = $this -> rest -> user_id;
        $insert_array['decision'] = 0;
        $insert_array['decision_time'] = SQL_DATETIME;

        $user_decision_id = $this -> user_decision_api_model -> insert_user_decision($insert_array);
        $user_decision = $this -> user_decision_api_model -> get_user_decision($user_decision_id);

        $this -> response(array(
            'data' => $user_decision
        ), 200);
    }


    public function revert_last_dislike_post() {

        // For non-premium user, check undo limit
        $is_premium_member = $this -> user_membership_option_api_model -> is_upgraded_user($this -> rest -> user_id);
        if (!$is_premium_member) {

            $undo_requests_count_today = $this -> user_decision_undo_request_api_model -> get_user_decision_undo_requests_count_with_params($this -> rest -> user_id, SQL_DATE1);

            if ($undo_requests_count_today >= 1) {

                $this->response(array(
                    'meta' => array(
                        'status' => FALSE,
                        'detail' => 'As a non-premium user, you cannot undo any more user decisions.',
                        'undo_requests_count_today' => $undo_requests_count_today
                    )
                ), 200);
            }
        }

        $last_disliked_user_decision = $this -> user_decision_api_model -> get_last_disliked_user_decision($this -> rest -> user_id);

        if (empty($last_disliked_user_decision)) {

            $this -> response(array(
                'meta' => array(
                    'status' => TRUE,
                    'detail' => 'There is no previous user you disliked.'
                )
            ), 200);
        }

        // Get disliked user
        $disliked_user = $this -> user_api_model -> get_user($last_disliked_user_decision -> target_user_id);

        // Delete user decision
        $this -> user_decision_api_model -> delete_user_decision($last_disliked_user_decision -> user_decision_id);

        // For non-premium user, save undo record
        if (!$is_premium_member) {

            $insert_user_decision_undo_request_array['user_id'] = $this->rest->user_id;
            $insert_user_decision_undo_request_array['user_decision_id'] = $last_disliked_user_decision->user_decision_id;
            $insert_user_decision_undo_request_array['requested_time'] = SQL_DATETIME;

            $this->user_decision_undo_request_api_model->insert_user_decision_undo_request($insert_user_decision_undo_request_array);
        }

        // Find new set of matches
        $offset = $this -> post('offset');
        $limit = $this -> post('limit');
        $sort_by_distance = $this -> post('sort_by_distance');

        if (empty($offset)) $offset = 0;
        if (empty($limit)) $limit = 0;
        if (empty($sort_by_distance)) $sort_by_distance = 0;

        $found_users = $this -> find_users($offset, $limit, $sort_by_distance);

        $this -> response(array(
            'data' => array(
                'attributes' => $disliked_user
            ),
            'included' => array(
                'users' => $found_users
            )
        ), 200);
    }

    public function super_date_post($user_id) {

        if (empty($user_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to process super date',
                        'detail' => 'User id is not provided.'
                    )
                )
            ), 200);
        }

        $num_date_tickets = $this -> post('num_date_tickets');

        if (empty($num_date_tickets)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to process super date',
                        'detail' => 'Number of date tickets is not provided.'
                    )
                )
            ), 200);
        }

        $me = $this -> user_api_model -> get_user($this -> rest -> user_id);
        $user = $this -> user_api_model -> get_user($user_id);

        if ($me -> num_date_tix < $num_date_tickets) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => '1001',
                        'title' => 'Failed to process super date',
                        'detail' => 'You don\'t have enough date tickets to process super date.'
                    )
                )
            ), 200);
        }

        $insert_super_date_array['request_user_id'] = $this -> rest -> user_id;
        $insert_super_date_array['target_user_id'] = $user_id;
        $insert_super_date_array['request_time'] = SQL_DATETIME;
        $insert_super_date_array['num_date_tickets'] = $num_date_tickets;

        $super_date_id = $this -> super_date_api_model -> insert_super_date($insert_super_date_array);

        // Get created data
        $super_date = $this -> super_date_api_model -> get_super_date($super_date_id);

        // Update user data for num_date_tickets
        $update_user_array['num_date_tix'] = $me -> num_date_tix - $num_date_tickets;
        $this -> user_api_model -> update_user($this -> rest -> user_id, $update_user_array);

        // Get updated user data
        $me = $this -> user_api_model -> get_user($this -> rest -> user_id);

        // Insert user decision
        $insert_user_decision_array['target_user_id'] = $user_id;
        $insert_user_decision_array['user_id'] = $this -> rest -> user_id;
        $insert_user_decision_array['decision'] = '1';
        $insert_user_decision_array['decision_time'] = SQL_DATETIME;

        $this -> user_decision_api_model -> delete_user_decision_with_params($user_id, $this -> rest -> user_id);
        $this -> user_decision_api_model -> insert_user_decision($insert_user_decision_array);

        // Send email
        $subject = translate_phrase("New super date has been requested.");
        $user_email_address = "mikeye27@gmail.com";
        $email_template = 'new super date request by '.$me->first_name.' at '.$me->mobile_phone_number.' for '.$user->first_name.' at '.$user->mobile_phone_number;
        $this -> model_user -> send_email(INFO_EMAIL,$user_email_address, $subject , $email_template,"html","DateTix");

        $this -> response(array(
            'data' => $super_date,
            'included' => array(
                'user' => array(
                    'attributes' => $me
                )
            )
        ), 200);
    }

    public function user_profile_get($user_id) {

        if (empty($user_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to get user profile',
                        'detail' => 'User id is not provided.'
                    )
                )
            ), 200);
        }

        $me = $this -> user_api_model -> get_user($this -> rest -> user_id);
        $user = $this -> user_api_model -> get_user($user_id);

        $gender = $this -> gender_api_model -> get_gender($user -> gender_id);
        $relationship_status = $this -> relationship_status_api_model -> get_relationship_status($user -> relationship_status_id);
        $child_status = $this -> child_status_api_model -> get_child_status($user -> child_status_id);
        $child_plan = $this -> child_plan_api_model -> get_child_plan($user -> child_plan_id);
        $smoking_status = $this -> smoking_status_api_model -> get_smoking_status($user -> smoking_status_id);
        $drinking_status = $this -> drinking_status_api_model -> get_drinking_status($user -> drinking_status_id);
        $exercise_frequency = $this -> exercise_frequency_api_model -> get_exercise_frequency($user -> exercise_frequency_id);
        $annual_income_range = $this -> annual_income_range_api_model -> get_annual_income_range($user -> annual_income_range_id);
        $body_type = $this -> body_type_api_model -> get_body_type($user -> body_type_id);
        $ethnicity = $this -> ethnicity_api_model -> get_ethnicity($user -> ethnicity_id);

        $user_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($user_id);
        $date_reviews = $this -> date_review_api_model -> get_date_reviews_by_user_id($user_id);
        $interests = $this -> user_interest_api_model -> get_user_interests_by_user_id($user_id);
        $descriptive_words = $this -> user_descriptive_word_api_model -> get_user_descriptive_words_by_user_id($user_id);
        $spoken_languages = $this -> user_spoken_language_api_model -> get_user_spoken_languages_by_user_id($user_id);
        $user_schools = $this -> user_school_api_model -> get_user_schools_by_user_id($user_id);
        $user_jobs = $this -> user_job_api_model -> get_user_jobs_by_user_id($user_id);

        $common_interests = $this -> user_interest_api_model -> get_common_interests($this -> rest -> user_id, $user_id);
        $interest_categories = $this -> interest_category_api_model -> get_interest_categories($this -> rest -> language_id);

        $mutual_friends = $this -> user_fb_friend_api_model -> get_mutual_friends($this -> rest -> user_id, $user_id);

        $user_want_relationship_type_ids = $this -> user_want_relationship_type_api_model -> get_relationship_type_ids_by_user_id($user_id);
        $user_want_relationship_types = array();
        if (!empty($user_want_relationship_type_ids)) {
            foreach ($user_want_relationship_type_ids as $user_want_relationship_type_id) {

                $relationship_type = $this->relationship_type_api_model->get_relationship_type($user_want_relationship_type_id);
                $user_want_relationship_types[] = $relationship_type;
            }
        }

        $away_in_km = 0;
        if (!empty($me -> gps_lat) &&
            !empty($me -> gps_lng) &&
            !empty($user -> gps_lat) &&
            !empty($user -> gps_lng)) {

            $away_in_km = $this -> user_api_model -> distance_between_two_points($me -> gps_lat, $me -> gps_lng, $user -> gps_lat, $user -> gps_lng, 'K');
        }

        $follow_time = $this -> user_follow_user_api_model -> get_follow_time_with_params($me -> user_id, $user -> user_id);

        $count_applied_to_my_dates = 0;
        $chosen_for_my_date = FALSE;
        $applied_dates_to_my_dates = $this -> date_api_model -> get_applied_dates_with_params($this -> rest -> user_id, $user_id);
        if (!empty($applied_dates_to_my_dates)) {
            $count_applied_to_my_dates = count($applied_dates_to_my_dates);
            foreach ($applied_dates_to_my_dates as $applied_date_to_my_dates) {
                if ($applied_date_to_my_dates['is_chosen'] == 1) {
                    $chosen_for_my_date = TRUE;
                    break;
                }
            }
        }

        $count_applied_to_user_dates = 0;
        $chosen_for_user_date = FALSE;
        $applied_dates_to_user_dates = $this -> date_api_model -> get_applied_dates_with_params($user_id, $this -> rest -> user_id);
        if (!empty($applied_dates_to_user_dates)) {
            $count_applied_to_user_dates = count($applied_dates_to_user_dates);
            foreach ($applied_dates_to_user_dates as $applied_date_to_user_dates) {
                if ($applied_date_to_user_dates['is_chosen'] == 1) {
                    $chosen_for_user_date = TRUE;
                    break;
                }
            }
        }

        $last_date = $this -> date_api_model -> get_last_date_between_users($this -> rest -> user_id, $user_id);

        // Insert user_visit_user record
        $insert_user_visit_user_array['user_id'] = $this -> rest -> user_id;
        $insert_user_visit_user_array['visited_user_id'] = $user_id;
        $insert_user_visit_user_array['visit_time'] = SQL_DATETIME;
        $this -> user_visit_user_api_model -> insert_user_visit_user($insert_user_visit_user_array);

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(
                    'gender' => $gender,
                    'user_photos' => $user_photos,
                    'date_reviews' => $date_reviews,
                    'interests' => $interests,
                    'common_interests' => $common_interests,
                    'mutual_friends' => $mutual_friends,
                    'ethnicity' => $ethnicity,
                    'user_want_relationship_types' => $user_want_relationship_types,
                    'user_schools' => $user_schools,
                    'user_jobs' => $user_jobs,
                    'annual_income_range' => $annual_income_range,
                    'body_type' => $body_type,
                    'descriptive_words' => $descriptive_words,
                    'spoken_languages' => $spoken_languages,
                    'relationship_status' => $relationship_status,
                    'child_status' => $child_status,
                    'child_plan' => $child_plan,
                    'smoking_status' => $smoking_status,
                    'drinking_status' => $drinking_status,
                    'exercise_frequency' => $exercise_frequency
                ),
                'meta' => array(
                    'away_in_km' => $away_in_km,
                    'follow_time' => $follow_time,
                    'count_applied_to_my_dates' => $count_applied_to_my_dates,
                    'count_applied_to_user_dates' => $count_applied_to_user_dates,
                    'chosen_for_my_date' => $chosen_for_my_date,
                    'chosen_for_user_date' => $chosen_for_user_date,
                    'last_date_time' => empty($last_date) ? NULL : $last_date -> date_time
                )
            ),
            'included' => array(
                'interest_categories' => $interest_categories
            )
        ), 200);
    }

    public function user_common_interests_get($user_id) {

        if (empty($user_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to get common interests',
                        'detail' => 'User id is not provided.'
                    )
                )
            ), 200);
        }

        $common_interests = $this -> user_interest_api_model -> get_common_interests($this -> rest -> user_id, $user_id);
        $interest_categories = $this -> interest_category_api_model -> get_interest_categories($this -> rest -> language_id);

        $this -> response(array(
            'data' => $common_interests,
            'included' => array(
                'interest_categories' => $interest_categories
            )
        ), 200);
    }

    public function user_mutual_friends_get($user_id) {

        if (empty($user_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to get common interests',
                        'detail' => 'User id is not provided.'
                    )
                )
            ), 200);
        }

        $mutual_friends = $this -> user_fb_friend_api_model -> get_mutual_friends($this -> rest -> user_id, $user_id);

        $this -> response(array(
            'data' => $mutual_friends,
            'meta' => array(
                'mutual_app_friends_count' => count($mutual_friends)
            )
        ), 200);
    }

    public function follow_post($user_id) {

        if (empty($user_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to follow user',
                        'detail' => 'User id is not provided.'
                    )
                )
            ), 200);
        }

        $is_premium_member = $this -> user_membership_option_api_model -> is_upgraded_user($this -> rest -> user_id);
        if (!$is_premium_member) {
            $user_follow_users = $this -> user_follow_user_api_model -> get_user_follow_users_by_user_id($this -> rest -> user_id);
            if (count($user_follow_users) >= 10) {

                $this -> response(array(
                    'meta' => array(
                        'status' => FALSE,
                        'required_premium_member' => TRUE,
                        'detail' => 'You are not allowed to follow more than 10 people with basics membership.'
                    )
                ), 200);
            }
        }

        $this -> user_follow_user_api_model -> do_follow_with_params($this -> rest -> user_id, $user_id);

        $this -> response(array(
            'meta' => array(
                'status' => TRUE,
                'detail' => 'Your operation was done successfully.'
            )
        ), 200);
    }

    public function unfollow_post($user_id) {

        if (empty($user_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to follow user',
                        'detail' => 'User id is not provided.'
                    )
                )
            ), 200);
        }

        $this -> user_follow_user_api_model -> do_unfollow_with_params($this -> rest -> user_id, $user_id);

        $this -> response(array(
            'meta' => array(
                'status' => TRUE,
                'detail' => 'Your operation was done successfully.'
            )
        ), 200);
    }

    /* ============================= End - People =========================*/

    public function user_get($user_id) {

        if (empty($user_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to get user',
                        'detail' => 'User ID is not provided.'
                    )
                )
            ), 200);
        }

        $user = $this -> user_api_model -> get_user($user_id);

        if (empty($user)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to get user',
                        'detail' => 'User ID is invalid.'
                    )
                )
            ), 200);
        }

        $user_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($user_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(
                    'user_photos' => $user_photos
                )
            )
        ), 200);
    }

    /* ============================= Start - My Follows/Visitors =========================*/

    public function my_follows_dates_get() {

        $followed_date_ids = $this -> user_follow_date_api_model -> get_followed_date_ids_by_user_id($this -> rest -> user_id);

        $followed_dates = array();
        if (!empty($followed_date_ids)) {
            foreach ($followed_date_ids as $followed_date_id) {

                $date = $this -> date_api_model -> get_date($followed_date_id);

                $requested_user = $this -> user_api_model -> get_user($date -> requested_user_id);
                $requested_user_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($requested_user -> user_id);
                $date_applicants = $this -> date_applicant_api_model -> get_date_applicants_by_date_id($date -> date_id);
                $date_type = $this -> date_type_api_model -> get_date_type($date -> date_type_id);
                $merchant = $this -> merchant_api_model -> get_merchant($date -> merchant_id);
                $date_decisions = $this -> date_decision_api_model -> get_date_decisions_by_date_id($date -> date_id);

                unset($followed_date);
                $followed_date['attributes'] = json_decode(json_encode($date), true);
                $followed_date['attributes']['views_count'] = count($date_decisions);

                $followed_date['relationships']['date_type'] = $date_type;
                $followed_date['relationships']['merchant'] = $merchant;

                $followed_date['relationships']['requested_user']['attributes'] = $requested_user;
                if (!empty($requested_user_photos))
                    $followed_date['relationships']['requested_user']['relationships']['user_photos'] = $requested_user_photos;

                if (!empty($date_applicants)) {
                    $date_applicant_objects = array();

                    foreach ($date_applicants as $date_applicant) {

                        $applicant_user = $this -> user_api_model -> get_user($date_applicant['applicant_user_id']);

                        unset($date_applicant_object);
                        $date_applicant_object['attributes'] = $date_applicant;
                        $date_applicant_object['relationships']['applicant_user']['attributes'] = $applicant_user;

                        $date_applicant_objects[] = $date_applicant_object;
                    }
                    $followed_date['relationships']['date_applicants'] = $date_applicant_objects;
                }

                $followed_dates[] = $followed_date;
            }
        }

        $this -> response(array(
            'data' => $followed_dates
        ), 200);
    }

    public function my_follows_people_get() {

        $user_follow_users = $this -> user_follow_user_api_model -> get_user_follow_users_by_user_id($this -> rest -> user_id);

        $followed_friends = array();
        if (!empty($user_follow_users)) {
            foreach ($user_follow_users as $user_follow_user) {

                $friend = $this -> user_api_model -> get_user($user_follow_user['follow_user_id']);

                $friend_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($friend -> user_id);

                unset($followed_friend);
                $followed_friend['attributes'] = $friend;

                if (!empty($friend_photos))
                    $followed_friend['relationships']['user_photos'] = $friend_photos;

                $followed_friend['meta']['follow_time'] = $user_follow_user['follow_time'];

                $followed_friends[] = $followed_friend;
            }
        }

        $this -> response(array(
            'data' => $followed_friends
        ), 200);
    }

    public function my_follows_merchants_get() {

        $followed_merchant_ids = $this -> user_follow_merchant_api_model -> get_followed_merchant_ids_by_user_id($this -> rest -> user_id);

        $followed_merchants = array();
        if (!empty($followed_merchant_ids)) {
            foreach ($followed_merchant_ids as $followed_merchant_id) {

                $followed_merchant = $this -> merchant_api_model -> get_merchant_with_photo($followed_merchant_id);

                if (!empty($followed_merchant)) {
                    $followed_merchants[] = $followed_merchant;
                }
            }
        }

        $this -> response(array(
            'data' => $followed_merchants
        ), 200);
    }

    public function my_visitors_get() {

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $user_visit_users = $this -> user_visit_user_api_model -> get_user_visit_users_by_visited_user_id($this -> rest -> user_id);

        $my_visitors = array();
        if (!empty($user_visit_users)) {
            foreach ($user_visit_users as $user_visit_user) {

                $friend = $this -> user_api_model -> get_user($user_visit_user['user_id']);

                $friend_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($friend -> user_id);

                $away_in_km = 0;
                if (!empty($user -> gps_lat) &&
                    !empty($user -> gps_lng) &&
                    !empty($friend -> gps_lat) &&
                    !empty($friend -> gps_lng)) {

                    $away_in_km = $this -> user_api_model -> distance_between_two_points($user -> gps_lat, $user -> gps_lng, $friend -> gps_lat, $friend -> gps_lng, 'K');
                }

                $follow_time = $this -> user_follow_user_api_model -> get_follow_time_with_params($this -> rest -> user_id, $user_visit_user['user_id']);

                unset($my_visitor);
                $my_visitor['attributes'] = $friend;

                if (!empty($friend_photos))
                    $my_visitor['relationships']['user_photos'] = $friend_photos;

                $my_visitor['meta']['visit_time'] = $user_visit_user['visit_time'];
                $my_visitor['meta']['away_in_km'] = $away_in_km;

                if (!empty($follow_time))
                    $my_visitor['meta']['follow_time'] = $follow_time;

                $my_visitors[] = $my_visitor;
            }
        }

        $this -> response(array(
            'data' => $my_visitors
        ), 200);
    }

    /* ============================= End - My Follows/Visitors =========================*/
}