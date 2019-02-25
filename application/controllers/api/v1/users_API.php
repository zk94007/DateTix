<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Users_API extends MY_REST_Controller {

    function __construct() {
        parent::__construct();

        $this -> load -> model('api/v1/api_key_model');

        $this -> load -> model('api/v1/annual_income_range_api_model');
        $this -> load -> model('api/v1/body_type_api_model');

        $this -> load -> model('api/v1/date_api_model');
        $this -> load -> model('api/v1/date_invite_api_model');
        $this -> load -> model('api/v1/date_review_api_model');
        $this -> load -> model('api/v1/date_type_api_model');
        $this -> load -> model('api/v1/merchant_api_model');

        $this -> load -> model('api/v1/ethnicity_api_model');
        $this -> load -> model('api/v1/gender_api_model');

        $this -> load -> model('api/v1/user_api_model');
        $this -> load -> model('api/v1/user_decision_api_model');
        $this -> load -> model('api/v1/user_fb_friend_api_model');
        $this -> load -> model('api/v1/user_photo_api_model');

        $this -> load -> model('api/v1/user_interest_api_model');
        $this -> load -> model('api/v1/interest_api_model');
        $this -> load -> model('api/v1/interest_category_api_model');

        $this -> load -> model('api/v1/user_job_api_model');
        $this -> load -> model('api/v1/user_school_api_model');

        $this -> load -> model('api/v1/relationship_type_api_model');
        $this -> load -> model('api/v1/user_want_relationship_type_api_model');
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

        $found_user_ids = $this -> user_api_model -> find_people_ids($this -> rest -> user_id, $sort_by_distance, $limit, $offset);

        $found_users = array();
        if (!empty($found_user_ids)) {

            foreach($found_user_ids as $found_user_id) {

                $user = $this -> user_api_model -> get_user($found_user_id);
                $user_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($found_user_id);

                unset($found_user);
                $found_user['attributes'] = $user;
                $found_user['relationships']['user_photos'] = $user_photos;

                $found_users[] = $found_user;
            }
        }

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
            $message = "{$user -> first_name} has invited you to date for {$date_type -> description} @ '{$merchant -> name}' at {$date -> date_time}";
            $meta = $this -> user_api_model -> send_push_notification_to_user($user_id, $message);
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
            'meta' => !empty($meta) ? $meta : null
        ), 200);
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
        $user = $this -> user_api_model -> get_user($last_disliked_user_decision -> target_user_id);

        // Delete user decision
        $this -> user_decision_api_model -> delete_user_decision($last_disliked_user_decision -> user_decision_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user
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

        $user_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($user_id);

        $date_reviews = $this -> date_review_api_model -> get_date_reviews_by_user_id($user_id);

        $away_in_km = 0;
        if (!empty($me -> gps_lat) &&
            !empty($me -> gps_lng) &&
            !empty($user -> gps_lat) &&
            !empty($user -> gps_lng)) {

            $away_in_km = $this -> user_api_model -> distance_between_two_points($me -> gps_lat, $me -> gps_lng, $user -> gps_lat, $user -> gps_lng, 'K');
        }

        $common_interests = $this -> user_interest_api_model -> get_common_interests($this -> rest -> user_id, $user_id);
        $interest_categories = $this -> interest_category_api_model -> get_interest_categories($this -> rest -> language_id);

        $mutual_friends = $this -> user_fb_friend_api_model -> get_mutual_friends($this -> rest -> user_id, $user_id);

        $ethnicity = $this -> ethnicity_api_model -> get_ethnicity($user -> ethnicity_id);

        $user_want_relationship_type_ids = $this -> user_want_relationship_type_api_model -> get_relationship_type_ids_by_user_id($user_id);
        $user_want_relationship_types = array();
        foreach ($user_want_relationship_type_ids as $user_want_relationship_type_id) {

            $relationship_type = $this -> relationship_type_api_model -> get_relationship_type($user_want_relationship_type_id);
            $user_want_relationship_types[] = $relationship_type;
        }

        $user_schools = $this -> user_school_api_model -> get_user_schools_by_user_id($user_id);

        $user_jobs = $this -> user_job_api_model -> get_user_jobs_by_user_id($user_id);

        $annual_income_range = $this -> annual_income_range_api_model -> get_annual_income_range($user -> annual_income_range_id);

        $body_type = $this -> body_type_api_model -> get_body_type($user -> body_type_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(
                    'gender' => $gender,
                    'user_photos' => $user_photos,
                    'date_reviews' => $date_reviews,
                    'common_interests' => $common_interests,
                    'mutual_friends' => $mutual_friends,
                    'ethnicity' => $ethnicity,
                    'user_want_relationship_types' => $user_want_relationship_types,
                    'user_schools' => $user_schools,
                    'user_jobs' => $user_jobs,
                    'annual_income_range' => $annual_income_range,
                    'body_type' => $body_type
                )
            ),
            'included' => array(
                'interest_categories' => $interest_categories
            ),
            'meta' => array(
                'away_in_km' => $away_in_km
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
}