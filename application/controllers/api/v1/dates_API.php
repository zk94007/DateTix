<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Dates_API extends MY_REST_Controller {

    function __construct() {
        parent::__construct();

        $this -> load -> model('api/v1/budget_api_model');
        $this -> load -> model('api/v1/country_api_model');
        $this -> load -> model('api/v1/country_ethnicity_api_model');
        $this -> load -> model('api/v1/cuisine_api_model');
        $this -> load -> model('api/v1/date_api_model');
        $this -> load -> model('api/v1/date_applicant_api_model');
        $this -> load -> model('api/v1/date_decision_api_model');
        $this -> load -> model('api/v1/date_invite_api_model');
        $this -> load -> model('api/v1/date_payer_api_model');
        $this -> load -> model('api/v1/date_type_api_model');
        $this -> load -> model('api/v1/date_review_api_model');
        $this -> load -> model('api/v1/delayed_job_api_model');
        $this -> load -> model('api/v1/ethnicity_api_model');
        $this -> load -> model('api/v1/gender_api_model');
        $this -> load -> model('api/v1/log_user_date_ticket_api_model');
        $this -> load -> model('api/v1/merchant_api_model');
        $this -> load -> model('api/v1/relationship_type_api_model');
        $this -> load -> model('api/v1/user_api_model');
        $this -> load -> model('api/v1/user_decision_api_model');
        $this -> load -> model('api/v1/user_email_api_model');
        $this -> load -> model('api/v1/user_fb_friend_api_model');
        $this -> load -> model('api/v1/user_interest_api_model');
        $this -> load -> model('api/v1/user_membership_option_api_model');
        $this -> load -> model('api/v1/user_photo_api_model');
    }

    /* ================================= Begin - Find Dates ======================== */
    function find_get() {

        $offset = $this -> get('offset');
        $limit = $this -> get('limit');
        $sort_by_distance = $this -> get('sort_by_distance');

        if (empty($offset))  $offset = 0;
        if (empty($limit))  $limit = 0;
        if (empty($sort_by_distance)) $sort_by_distance = 0;

        $found_date_ids = $this -> date_api_model -> find_date_ids($this -> rest -> user_id, $sort_by_distance, $limit, $offset);

        $found_dates = array();
        if (!empty($found_date_ids)) {

            foreach($found_date_ids as $found_date_id) {

                $date = $this -> date_api_model -> get_date($found_date_id);
                $date_type = $this -> date_type_api_model -> get_date_type($date -> date_type_id);
                $relationship_type = $this -> relationship_type_api_model -> get_relationship_type($date -> date_relationship_type_id);
                $date_payer = $this -> date_payer_api_model -> get_date_payer($date -> date_payer_id);
                $merchant = $this -> merchant_api_model -> get_merchant_with_photo($date -> merchant_id);

                $requested_user = $this -> user_api_model -> get_user($date -> requested_user_id);
                $is_requested_user_premium_member = $this -> user_membership_option_api_model -> is_upgraded_user($date -> requested_user_id);
                $requested_user -> is_premium_member = $is_requested_user_premium_member;

                $requested_user_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($date -> requested_user_id);

                $date_applicant = $this -> date_applicant_api_model -> get_date_applicant_with_params($this -> rest -> user_id, $date -> date_id);

                unset($found_date);
                $found_date['attributes'] = $date;

                $found_date['relationships']['date_type'] = $date_type;
                $found_date['relationships']['relationship_type'] = $relationship_type;
                $found_date['relationships']['date_payer'] = $date_payer;
                $found_date['relationships']['merchant'] = $merchant;

                if (!empty($date_applicant)) {
                    $found_date['relationships']['date_applicants'][]['attributes'] = $date_applicant;
                }

                $found_date['relationships']['requested_user']['attributes'] = $requested_user;
                if (!empty($requested_user_photos)) {
                    $found_date['relationships']['requested_user']['relationships']['user_photos'] = $requested_user_photos;
                }

                $found_dates[] = $found_date;
            }
        }

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $this -> response(array(
            'data' => $found_dates,
            'included' => array(
                'user' => array(
                    'attributes' => $user
                )
            )
        ), 200);
    }

    public function dates_apply_post($date_id) {

        if (empty($date_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to process apply',
                        'detail' => 'Date id is not provided.'
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
                        'title' => 'Failed to process apply',
                        'detail' => 'Num date tickets is not provided.'
                    )
                )
            ), 200);
        }
        if (!ctype_digit($num_date_tickets) || $num_date_tickets <= 0) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to process apply',
                        'detail' => 'Num date tickets is invalid.'
                    )
                )
            ), 200);
        }

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        // Insert date decision
        $insert_date_decision_array['date_id'] = $date_id;
        $insert_date_decision_array['user_id'] = $this -> rest -> user_id;
        $insert_date_decision_array['decision'] = 1;
        $insert_date_decision_array['decision_time'] = SQL_DATETIME;

        $this -> date_decision_api_model -> delete_date_decision_with_params($date_id, $this -> rest -> user_id);
        $date_decision_id = $this -> date_decision_api_model -> insert_date_decision($insert_date_decision_array);
        $date_decision = $this -> date_decision_api_model -> get_date_decision($date_decision_id);

        // Update user data for num_date_tickets
        $update_array['num_date_tix'] = $user -> num_date_tix - $num_date_tickets;
        $this -> user_api_model -> update_user($this -> rest -> user_id, $update_array);

        // Insert date applicant
        $insert_date_applicant_array['date_id'] = $date_id;
        $insert_date_applicant_array['applicant_user_id'] = $this -> rest -> user_id;
        $insert_date_applicant_array['num_date_tickets'] = $num_date_tickets;

        $date_applicant_id = $this -> date_applicant_api_model -> insert_date_applicant($insert_date_applicant_array);
        $date_applicant = $this -> date_applicant_api_model -> get_date_applicant($date_applicant_id);

        // Insert date ticket log
        $insert_log_date_ticket_array['user_id'] = $this -> rest -> user_id;
        $insert_log_date_ticket_array['num_date_tickets'] = $num_date_tickets;
        $insert_log_date_ticket_array['transaction_time'] = SQL_DATETIME;
        $insert_log_date_ticket_array['description'] = "Applied to Date {$date_id}";

        $this -> log_user_date_ticket_api_model -> insert_log_user_date_ticket($insert_log_date_ticket_array);

        // Send email
        $date = $this -> date_api_model -> get_date($date_id);
        $this -> send_date_apply_email($date -> requested_user_id, $date_id);

        // Send Push Notification
        $requested_user = $this -> user_api_model -> get_user($date -> requested_user_id);
        if ($requested_user -> want_pn_new_applicant == 1) {    // Check requested user's settings

            $message = "{$user -> first_name} has applied to be your date.";
            $meta = array(
                'notification_type' => 'date_apply',
                'date_id' => $date_id
            );
            $meta_response = $this -> user_api_model -> send_push_notification_to_user($date -> requested_user_id, $message, $meta);
        }

        // Get updated user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $date,
                'relationships' => array(
                    'date_decision' => $date_decision,
                    'date_applicant' => array(
                        'attributes' => $date_applicant
                    )
                )
            ),
            'included' => array(
                'user' => array(
                    'attributes' => $user
                )
            ),
            'meta' => !empty($meta_response) ? $meta_response : NULL
        ), 200);
    }

    public function dates_dislike_post($date_id) {

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

        // Insert date decision
        $insert_array['date_id'] = $date_id;
        $insert_array['user_id'] = $this -> rest -> user_id;
        $insert_array['decision'] = 0;
        $insert_array['decision_time'] = SQL_DATETIME;

        $this -> date_decision_api_model -> delete_date_decision_with_params($date_id, $this -> rest -> user_id);
        $date_decision_id = $this -> date_decision_api_model -> insert_date_decision($insert_array);

        // Get created date decision
        $date_decision = $this -> date_decision_api_model -> get_date_decision($date_decision_id);

        $this -> response(array(
            'data' => $date_decision
        ), 200);
    }

    public function dates_revert_last_dislike_post() {

        $last_disliked_date_decision = $this -> date_decision_api_model -> get_last_disliked_date_decision($this -> rest -> user_id);

        // Get date
        $date_id = $last_disliked_date_decision -> date_id;
        $date = $this -> date_api_model -> get_date($date_id);
        $requested_user = $this -> user_api_model -> get_user($date -> requested_user_id);

        // Delete date decision
        $this -> date_decision_api_model -> delete_date_decision($last_disliked_date_decision -> date_decision_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $date,
                'relationships' => array(
                    'requested_user' => array(
                        'attributes' => $requested_user
                    )
                )
            ),
            'meta' => array(
                'status' => TRUE,
                'detail' => 'Last disliked date has been reverted.'
            )
        ), 200);
    }

    private function send_date_apply_email($from_user_id, $date_id) {
        /* Temporarily commented [TODO]

        if ($from_user_id && $date_id) {

            $this -> general -> set_table('user');
            $user_data = $this -> general -> get("first_name,last_name,gender_id", array('user_id' => $this -> user_id));
            $applicant_user_data = $user_data['0'];

            $this -> general -> set_table('user');
            $request_user_data = $this -> general -> get("user_id,password,first_name,last_name,gender_id", array('user_id' => $from_user_id));
            $host_user_data = $request_user_data['0'];

            $this -> general -> set_table('date_invite');
            $date_invite_condition['invite_user_id'] = $this -> user_id;
            $date_invite_condition['date_id'] = $date_id;


            // Send msg to applicant from host
            $insertChat['from_user_id'] = $from_user_id;
            $insertChat['to_user_id'] = $this -> user_id;
            $insertChat['chat_message'] = 'Hi ' . $applicant_user_data['first_name'] . ', thanks for applying to my date';
            $insertChat['is_read'] = '0';
            $insertChat['chat_message_time'] = SQL_DATETIME;
            $this -> model_date -> save_chat($insertChat);

            // Send msg to host from applicant
            $insertChat['from_user_id'] = $this -> user_id;
            $insertChat['to_user_id'] = $from_user_id;
            $insertChat['chat_message'] = 'Hi ' . $host_user_data['first_name'] . ', would be great to meet you for this date!';
            $insertChat['is_read'] = '0';
            $insertChat['chat_message_time'] = SQL_DATETIME;
            $this -> model_date -> save_chat($insertChat);


            //send email to date host user
            $is_user_invited = $this -> general -> get("", $date_invite_condition);
            if($is_user_invited)
            {
                //if invited
                $subject = $applicant_user_data['first_name'] . translate_phrase(" has responded to your invitation");
                $email_content = $applicant_user_data['first_name'] . translate_phrase(" has responded to your invitation and applied to your date! Click the button below to chat with ") . $applicant_user_data['first_name'];
            }
            else {
                $dateDetail = $this -> model_date -> get_date_detail_by_id($date_id);
                $subject = $applicant_user_data['first_name'].translate_phrase(" has applied to be your date");

                if($applicant_user_data['gender_id'] == 1)
                {
                    $noun = 'his';
                    $pro_noun = 'him';
                }
                else {
                    $noun = 'her';
                    $pro_noun = 'her';
                }
                $email_content = $applicant_user_data['first_name'].translate_phrase(' has applied to ' . $dateDetail['date_type'] . ' @ ' . trim($dateDetail['name']).' ' . print_date_daytime($dateDetail['date_time']). '. Click the button below to view '.$noun.' profile and chat with '.$pro_noun.':');
            }

            $data['email_content'] = $email_content;
            $data['email_title'] = '';


            $host_user_id = $this -> utility -> encode($from_user_id);
            $chat_with_user_id = $this -> utility -> encode($this -> user_id);
            $data['btn_text'] = translate_phrase('Chat with ').$applicant_user_data['first_name'];

            $chat_link = base_url() . "dates/chat_history/" . $chat_with_user_id. "/" . $host_user_id;

            //Dynamic autologin link
            $user_link = $this -> utility -> encode($host_user_data['user_id']);
            if ($host_user_data['password']) {
                $user_link .= '/' . $host_user_data['password'];
            }
            $data['btn_link'] = base_url() . 'home/mail_action/' . $user_link . '?return_to='.$chat_link;

            $email_template = $this -> load -> view('email/common', $data, true);
            $user_email = $this -> model_user -> get_user_email($from_user_id);
            $this -> datetix -> mail_to_user($user_email['email_address'], $subject, $email_template);
        }
        */
    }

    /* ================================== End - Find Dates ==========================*/


    /* ================================= Start - New Date =========================*/
    public function available_dates_for_new_date_get() {

        $start_date_str = $this -> get('start_date');
        $limit = $this -> get('limit');
        $limit = $limit - 1;

        $start_date = date('Y-m-d', strtotime($start_date_str));
        $end_date = date('Y-m-d', strtotime("+{$limit} day", strtotime($start_date)));

        $available_dates = array();

        $current = strtotime($start_date);
        $last = strtotime($end_date);
        $i = 0;

        while ($current <= $last) {

            $available_dates[$i]['key'] = date('Y-m-d', $current);
            $available_dates[$i]['value'] = date('D, M jS ', strtotime(date('Y-m-d', $current)));
            $current = strtotime('+1 day', $current);
            $i++;
        }

        $this -> response(array(
            'data' => $available_dates
        ), 200);
    }

    public function validate_date_time_for_new_date_get() {

        $date_time_str = $this -> get('date_time');

        if (empty($date_time_str)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to validate times.',
                        'detail' => 'Date time is not provided.'
                    )
                )
            ), 200);
        }

        $dates_in_date_time_interval = $this -> date_api_model -> get_dates_in_date_time_interval($date_time_str, $this -> rest -> user_id);

        $errors = array();

        if (!empty($dates_in_date_time_interval)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to validate times.',
                'detail' => 'You cannot date more than one within 30 minutes.'
            );
        }

        $date_time = new DateTime($date_time_str);
        $time_interval = $date_time -> diff(new DateTime('now'));
        $diff_minutes = $time_interval -> days * 24 * 60 + $time_interval -> h * 60 + $time_interval -> i;

        if ($diff_minutes < 30) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to validate times.',
                'detail' => 'You cannot date in 30 minutes.'
            );
        }

        if (count($errors)) {

            $this -> response(array(
                'errors' => $errors
            ), 200);
        }

        $last_date = $this -> date_api_model -> get_last_date_by_user_id($this -> rest -> user_id);
        $merchant = $this -> merchant_api_model -> get_merchant($last_date -> merchant_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $last_date,
                'relationships' => array(
                    'merchant' => $merchant
                )
            ),
            'meta' => array(
                'status' => TRUE,
                'detail' => 'Your date time is validated. You are good to go through further steps.'            )
        ), 200);
    }

    public function new_date_step2_get() {

        $date_types = $this -> date_type_api_model -> get_date_types($this -> rest ->language_id);
        $relationship_types = $this -> relationship_type_api_model -> get_relationship_types($this -> rest ->language_id);
        $date_payers = $this -> date_payer_api_model -> get_date_payers($this -> rest ->language_id);

        $this -> response(array(
            'data' => array(
                'date_types' => $date_types,
                'relationship_types' => $relationship_types,
                'date_payers' => $date_payers
            )
        ), 200);
    }

    public function new_date_step3_get() {

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $country_id = NULL;
        if (!empty($user -> mobile_international_code)) {

            $country_code = str_replace('+', '', $user -> mobile_international_code);
            $country_id = $this -> country_api_model -> get_country_id_by_country_code($country_code);
        }

        $genders = $this -> gender_api_model -> get_genders($this -> rest ->language_id);
        $ethnicities = $this -> country_ethnicity_api_model -> get_ethnicities_by_country_id($country_id, $this -> rest ->language_id);

        $this -> response(array(
            'data' => array(
                'genders' => $genders,
                'ethnicities' => $ethnicities
            )
        ), 200);
    }

    public function new_date_step4_get() {

        $budgets = $this -> budget_api_model -> get_budgets($this -> rest ->language_id);
        $cuisines = $this -> cuisine_api_model -> get_cuisines_with_category($this -> rest ->language_id);
        $sort_by_list = array('Featured', 'Name', 'Distance');

        $this -> response(array(
            'data' => array(
                'budgets' => $budgets,
                'cuisines' => $cuisines,
                'sort_by_list' => $sort_by_list
            )
        ), 200);
    }

    public function new_date_step5_get() {

        $date_packages = $this -> get_date_packages();

        $this -> response(array(
            'data' => $date_packages
        ), 200);
    }

    private function get_date_packages() {

        return array(
            array(
                'date_package_id' => '1',
                'name'=> 'Bronze',
                'image'=>base_url('assets/images/award-bronze.png'),
                'use_tickets'=>'1',
                'profile_matches_count'=>'10',
            ),
            array(
                'date_package_id' => '2',
                'name'=> 'Silver',
                'image'=>base_url('assets/images/award-silver.png'),
                'use_tickets'=>'3',
                'profile_matches_count'=>'25',
            ),
            array(
                'date_package_id' => '3',
                'name'=> 'Gold',
                'image'=>base_url('assets/images/award-gold.png'),
                'use_tickets'=>'10',
                'profile_matches_count'=>'50',
            ),
            array(
                'date_package_id' => '4',
                'name'=>'Platinum',
                'image'=>base_url('assets/images/award-platinum.png'),
                'use_tickets'=>'100',
                'profile_matches_count'=>'100',
            )
        );
    }

    public function new_date_step6_get() {

        $date_type_id = $this -> get('date_type_id');
        $merchant_id = $this -> get('merchant_id');
        $date_payer_id = $this -> get('date_payer_id');
        $relationship_type_id = $this -> get('relationship_type_id');
        $date_package_id = $this -> get('date_package_id');

        // Result variables
        $date_type_str = '';
        $merchant_str = '';
        $budget_str = '';
        $date_payer_str = '';
        $relationship_type_str = '';
        $date_package_str = '';
        $relationship_type_date_tickets = 0;
        $budget_date_tickets = 0;
        $date_package_date_tickets = 0;

        $date_type = $this -> date_type_api_model -> get_date_type($date_type_id);
        if (!empty($date_type)) {
            $date_type_str = $date_type -> description;
        }

        $merchant = $this -> merchant_api_model -> get_merchant($merchant_id);
        if (!empty($merchant)) {
            $merchant_str = $merchant -> name;

            $merchant_budget = $this -> budget_api_model -> get_budget($merchant -> budget_id);
            if (!empty($merchant_budget)) {
                $budget_str = $merchant_budget -> description;
                $budget_date_tickets = $merchant_budget -> num_date_tix;
            }
        }

        $date_payer = $this -> date_payer_api_model -> get_date_payer($date_payer_id);
        if (!empty($date_payer)) {
            $date_payer_str = $date_payer -> description;
        }

        $relationship_type = $this -> relationship_type_api_model -> get_relationship_type($relationship_type_id);
        if (!empty($relationship_type)) {
            $relationship_type_str = $relationship_type -> description;
            $relationship_type_date_tickets = $relationship_type -> num_date_tix;
        }

        $date_packages = $this -> get_date_packages();
        foreach ($date_packages as $date_package) {

            if ($date_package['date_package_id'] == $date_package_id) {
                $date_package_str = $date_package['name'];
                $date_package_date_tickets = $date_package['use_tickets'];
            }
        }

        $this -> response(array(
            'data' => array(
                'date_type' => $date_type_str,
                'merchant' => $merchant_str,
                'budget' => $budget_str,
                'date_payer' => $date_payer_str,
                'relationship_type' => $relationship_type_str,
                'date_package' => $date_package_str,
                'relationship_type_date_tickets' => $relationship_type_date_tickets,
                'budget_date_tickets' => $budget_date_tickets,
                'date_package_date_tickets' => $date_package_date_tickets
            )
        ), 200);
    }
    /* ================================= End - New Date =========================*/


    /* ================================= Start - My Dates =========================*/
    public function my_dates_post() {

        // Get parameter values
        $date_time = $this -> post('date_time');
        $date_type_id = $this -> post('date_type_id');
        $relationship_type_id = $this -> post('relationship_type_id');
        $date_payer_id = $this -> post('date_payer_id');
        $gender_ids = $this -> post('gender_ids');
        $distance = $this -> post('distance');      // Not used for now
        $age_range_lower = $this -> post('age_range_lower');
        $age_range_upper = $this -> post('age_range_upper');
        $ethnicity_ids = $this -> post('ethnicity_ids');
        $merchant_id = $this -> post('merchant_id');
        $date_package_id = $this -> post('date_package_id');
        $num_date_tickets = $this -> post('num_date_tickets');

        $errors = array();

        if (empty($date_time)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to create date',
                'detail' => 'Available date is empty.'
            );
        }
        if (empty($date_type_id)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to create date',
                'detail' => 'Date type id is empty.'
            );
        }
        if (empty($relationship_type_id)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to create date',
                'detail' => 'Relationship type id is empty.'
            );
        }
        if (empty($date_payer_id)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to create date',
                'detail' => 'Date payer id is empty.'
            );
        }
        if (empty($gender_ids)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to create date',
                'detail' => 'Gender id is empty.'
            );
        }
        // Commented temporarily
        /*
        if (empty($distance)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to create date',
                'detail' => 'Distance is empty.'
            );
        }*/
        if (empty($age_range_lower)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to create date',
                'detail' => 'Age range lower is empty.'
            );
        }
        if (empty($age_range_upper)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to create date',
                'detail' => 'Age range upper is empty.'
            );
        }
        if (empty($ethnicity_ids)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to create date',
                'detail' => 'Ethnicity id is empty.'
            );
        }
        if (empty($merchant_id)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to create date',
                'detail' => 'Merchant id is empty.'
            );
        }
        if (empty($date_package_id)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to create date',
                'detail' => 'Date package id is empty.'
            );
        }
        if (empty($num_date_tickets)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to create date',
                'detail' => 'Num date tickets is empty.'
            );
        }
        if (count($errors) > 0) {

            $this -> response(array(
                'errors' => $errors
            ), 200);
        }

        // Build array for creating date
        $insert_date_array['date_time'] = $date_time;
        $insert_date_array['requested_user_id'] = $this -> rest -> user_id;
        $insert_date_array['completed_step'] = 10;  // This will help to distinguish dates posted through app and website
        $insert_date_array['post_time'] = SQL_DATETIME;
        $insert_date_array['date_type_id'] = $date_type_id;
        $insert_date_array['date_relationship_type_id'] = $relationship_type_id;
        $insert_date_array['date_payer_id'] = $date_payer_id;
        $insert_date_array['num_date_tickets'] = $num_date_tickets;
        $insert_date_array['date_gender_ids'] = implode(',', $gender_ids);
        $insert_date_array['age_range_lower'] = $age_range_lower;
        $insert_date_array['age_range_upper'] = $age_range_upper;
        $insert_date_array['date_ethnicity_ids'] = implode(',', $ethnicity_ids);
        $insert_date_array['merchant_id'] = $merchant_id;
        $insert_date_array['date_package_id'] = $date_package_id;
        //$insert_date_array['date_max_distance'] = $distance;

        $date_id = $this -> date_api_model -> insert_date($insert_date_array);

        if (empty($date_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to host a date',
                        'detail' => 'Failed to insert date info into db table.'
                    )
                )
            ), 200);
        }

        // Build array for creating date ticket log
        $insert_ticket_array['transaction_time'] = SQL_DATETIME;
        $insert_ticket_array['description'] = 'Hosted Date ' . $date_id;
        $insert_ticket_array['user_id'] = $this -> rest -> user_id;
        $insert_ticket_array['num_date_tickets'] = $num_date_tickets;

        $this -> log_user_date_ticket_api_model -> insert_log_user_date_ticket($insert_ticket_array);

        // Update user's num_date_tix
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $update_user_array['num_date_tix'] = $user -> num_date_tix - $num_date_tickets;
        $this -> user_api_model -> update_user($this -> rest -> user_id, $update_user_array);

        // Get updated user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        // Send push notifications to random matches
        $num_profile_matches_count = 0;
        $date_packages = $this -> get_date_packages();
        foreach ($date_packages as $date_package) {

            if ($date_package['date_package_id'] == $date_package_id) {
                $num_profile_matches_count = $date_package['profile_matches_count'];
            }
        }

        // Send 'date_invite' push notifications in the background
        $insert_delayed_job_1_array['function_name'] = 'send_invitation_push_notifications_to_profile_matches';
        $params = array(
            'user_id' => $this -> rest -> user_id,
            'date_id' => $date_id,
            'num_profile_matches_count' => $num_profile_matches_count
        );
        $insert_delayed_job_1_array['function_parameters'] = json_encode($params);
        $insert_delayed_job_1_array['queue_number'] = $this -> delayed_job_api_model -> get_last_queue_number() + 1;
        $insert_delayed_job_1_array['created_at'] = SQL_DATETIME;
        $insert_delayed_job_1_array['updated_at'] = SQL_DATETIME;
        $this -> delayed_job_api_model -> insert_delayed_job($insert_delayed_job_1_array);

        // Send 'new_date_near_you' push notifications in the background
        $insert_delayed_job_2_array['function_name'] = 'send_new_date_near_you_push_notifications';
        $params = array(
            'user_id' => $this -> rest -> user_id,
            'date_id' => $date_id
        );
        $insert_delayed_job_2_array['function_parameters'] = json_encode($params);
        $insert_delayed_job_2_array['queue_number'] = $this -> delayed_job_api_model -> get_last_queue_number() + 1;
        $insert_delayed_job_2_array['created_at'] = SQL_DATETIME;
        $insert_delayed_job_2_array['updated_at'] = SQL_DATETIME;
        $this -> delayed_job_api_model -> insert_delayed_job($insert_delayed_job_2_array);

        // Return date
        $date = $this -> date_api_model -> get_date($date_id);

        if (empty($date)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to retrieve date',
                        'detail' => 'Failed to retrieve date info from db table.'
                    )
                )
            ), 200);
        }

        $this -> response(array(
            'data' => array(
                'attributes' => $date,
                'relationships' => array(
                    'requested_user' => array(
                        'attributes' => $user
                    )
                )
            )
        ), 200);
    }

    public function my_dates_get($date_id) {

        if (empty($date_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to get date',
                        'detail' => 'Date id is not provided.'
                    )
                )
            ), 200);
        }

        $date = $this -> date_api_model -> get_date($date_id);

        $date_type = $this -> date_type_api_model -> get_date_type($date -> date_type_id);
        $relationship_type = $this -> relationship_type_api_model -> get_relationship_type($date -> date_relationship_type_id);
        $date_payer = $this -> date_payer_api_model -> get_date_payer($date -> date_payer_id);
        $merchant = $this -> merchant_api_model -> get_merchant_with_photo($date -> merchant_id);

        $requested_user = $this -> user_api_model -> get_user($date -> requested_user_id);
        $requested_user_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($date -> requested_user_id);

        $date_applicants = $this -> date_applicant_api_model -> get_date_applicants_by_date_id($date_id);
        $date_applicants_array = array();
        if (!empty($date_applicants)) {

            foreach ($date_applicants as $date_applicant) {

                $applicant_user = $this -> user_api_model -> get_user($date_applicant['applicant_user_id']);
                $applicant_user_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($date_applicant['applicant_user_id']);

                unset($date_applicant_array);
                $date_applicant_array['attributes'] = $date_applicant;
                $date_applicant_array['relationships']['applicant_user']['attributes'] = $applicant_user;
                if (!empty($applicant_user_photos)) {
                    $date_applicant_array['relationships']['applicant_user']['relationships']['user_photos'] = $applicant_user_photos;
                }

                $date_applicants_array[] = $date_applicant_array;
            }
        }

        $genders = $this -> gender_api_model -> get_genders($this -> rest -> language_id);
        $ethnicities = $this -> ethnicity_api_model -> get_ethnicities($this -> rest -> language_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $date,
                'relationships' => array(
                    'date_type' => $date_type,
                    'relationship_type' => $relationship_type,
                    'date_payer' => $date_payer,
                    'merchant' => $merchant,
                    'requested_user' => array(
                        'attributes' => $requested_user,
                        'relationships' => array(
                            'user_photos' => $requested_user_photos
                        )
                    ),
                    'date_applicants' => $date_applicants_array
                )
            ),
            'included' => array(
                'genders' => $genders,
                'ethnicities' => $ethnicities
            )
        ), 200);
    }

    public function my_dates_put($date_id) {

        if (empty($date_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to update date',
                        'detail' => 'Date id is not provided.'
                    )
                )
            ), 200);
        }

        $gender_ids = $this -> put('gender_ids');
        $age_range_lower = $this -> put('age_range_lower');
        $age_range_upper = $this -> put('age_range_upper');
        $distance = $this -> put('distance');       // Not used for now

        if (!empty($gender_ids))
            $update_date_array['date_gender_ids'] = implode(',', $gender_ids);

        if (!empty($age_range_lower))
            $update_date_array['age_range_lower'] = $age_range_lower;

        if (!empty($age_range_upper))
            $update_date_array['age_range_upper'] = $age_range_upper;

        // Commented temporarily
        /*
        if (!empty($distance))
            $update_date_array['date_max_distance'] = $distance;
        */

        if (empty($update_date_array)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to update date',
                        'detail' => 'No update information is provided.'
                    )
                )
            ), 200);
        }

        // Update date
        $this -> date_api_model -> update_date($date_id, $update_date_array);

        // Get updated date
        $date = $this -> date_api_model -> get_date($date_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $date
            )
        ), 200);
    }

    public function my_dates_upcoming_dates_get() {

        $offset = $this -> get('offset');
        $limit = $this -> get('limit');

        if (empty($offset)) $offset = 0;
        if (empty($limit)) $limit = 0;

        $this -> respond_with_my_dates($limit, $offset, TRUE, FALSE);
    }

    public function my_dates_upcoming_dates_my_hosts_get() {

        $offset = $this -> get('offset');
        $limit = $this -> get('limit');

        if (empty($offset)) $offset = 0;
        if (empty($limit)) $limit = 0;

        $this -> respond_with_my_dates($limit, $offset, TRUE, TRUE);
    }

    public function my_dates_past_dates_get() {

        $offset = $this -> get('offset');
        $limit = $this -> get('limit');

        if (empty($offset)) $offset = 0;
        if (empty($limit)) $limit = 0;

        $this -> respond_with_my_dates($limit, $offset, FALSE, FALSE);
    }

    public function my_dates_past_dates_my_hosts_get() {

        $offset = $this -> get('offset');
        $limit = $this -> get('limit');

        if (empty($offset)) $offset = 0;
        if (empty($limit)) $limit = 0;

        $this -> respond_with_my_dates($limit, $offset, FALSE, TRUE);
    }

    private function respond_with_my_dates($limit = 0, $offset = 0, $is_upcoming = TRUE, $my_hosts_only = TRUE) {

        $my_dates = $this -> date_api_model -> get_my_dates($this -> rest -> user_id, $limit, $offset, $is_upcoming, $my_hosts_only);

        $genders = $this -> gender_api_model -> get_genders($this -> rest -> language_id);
        $ethnicities = $this -> ethnicity_api_model -> get_ethnicities($this -> rest -> language_id);

        $this -> response(array(
            'data' => $my_dates,
            'included' => array(
                'genders' => $genders,
                'ethnicities' => $ethnicities
            )
        ), 200);
    }

    public function my_dates_feedbacks_get($date_id) {

        if (empty($date_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to get feedbacks',
                        'detail' => 'Date id is not provided.'
                    )
                )
            ), 200);
        }

        $date_reviews = $this -> date_review_api_model -> get_date_reviews_by_date_id($date_id);

        $this -> response(array(
            'data' => $date_reviews
        ), 200);
    }

    public function my_dates_feedbacks_post($date_id) {

        if (empty($date_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to upload feedback',
                        'detail' => 'Date id is not provided.'
                    )
                )
            ), 200);
        }

        $rating = $this -> post('rating');
        $comment = $this -> post('comment');

        $errors = array();

        if (empty($rating)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to upload feedback',
                'detail' => 'Rating is not provided.'
            );
        }
        if (empty($comment)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to upload feedback',
                'defail' => 'Comment is not provided.'
            );
        }

        if (count($errors) > 0) {

            $this -> response(array(
                'errors' => $errors
            ), 200);
        }

        $insert_date_review_array['date_id'] = $date_id;
        $insert_date_review_array['review_by_user_id'] = $this -> rest -> user_id;
        $insert_date_review_array['rating'] = $rating;
        $insert_date_review_array['review'] = $comment;
        $insert_date_review_array['review_time'] = SQL_DATETIME;

        $date_review_id = $this -> date_review_api_model -> insert_date_review($insert_date_review_array);

        $date_review = $this -> date_review_api_model -> get_date_review($date_review_id);

        $this -> response(array(
            'data' => $date_review
        ), 200);
    }

    public function my_dates_refund_post($date_id) {

        if (empty($date_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to upload feedback',
                        'detail' => 'Date id is not provided.'
                    )
                )
            ), 200);
        }

        $refund_reason = $this -> post('refund_reason');

        if (empty($refund_reason)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to refund date',
                        'detail' => 'Refund reason is not provided.'
                    )
                )
            ), 200);
        }

        // Update date
        $update_date_array['status'] = 2;
        $update_date_array['refund_reason'] = $refund_reason;

        $this -> date_api_model -> update_date($date_id, $update_date_array);

        // Get updated date
        $date = $this -> date_api_model -> get_date($date_id);

        // Send email
        $date_info = $this -> date_api_model -> get_date_detail_by_id($date_id);
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $subject = $user -> first_name . ' has requested a date refund';
        $email_content = $user -> first_name . ' has requested a refund for ' .
                        $date_info['date_type'] . ' @ ' . $date_info['name'] . ' on ' .
                        $date_info['date_time'] . ' with the reason: ' .
                        $refund_reason . '.';
        $email_content .= ' His mobile number is ' . $user -> mobile_international_code . ' ' . $user -> mobile_phone_number;

        $data['email_content'] = $email_content;
        $data['email_title'] = 'Date Refund';
        $email_template = $this -> load -> view('email/common', $data, true);
        $this -> datetix -> mail_to_user(REFUND_EMAIL, $subject, $email_template);

        $user_email = $this -> user_email_api_model -> get_user_email_by_user_id($this -> rest -> user_id);

        $subject1 = 'We have received your refund request';
        $data1['email_content'] = 'We have received your refund request and will get back to you if we have any questions.';
        $data1['email_title'] = '';
        $email_template1 = $this -> load -> view('email/common', $data1, true);
        $this -> datetix -> mail_to_user($user_email['email_address'], $subject1, $email_template1);

        $this -> response(array(
            'data' => array(
                'attributes' => $date
            )
        ), 200);
    }

    public function my_dates_applicants_get($date_id) {

        if (empty($date_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to upload feedback',
                        'detail' => 'Date id is not provided.'
                    )
                )
            ), 200);
        }

        $date_applicants = $this -> date_applicant_api_model -> get_date_applicants_by_date_id($date_id);

        $applicants = array();

        if (!empty($date_applicants)) {

            foreach ($date_applicants as $date_applicant) {

                $applicant_user = $this -> user_api_model -> get_user($date_applicant['applicant_user_id']);
                $is_applicant_user_premium_member = $this -> user_membership_option_api_model -> is_upgraded_user($date_applicant['applicant_user_id']);
                $applicant_user -> is_premium_member = $is_applicant_user_premium_member;

                $applicant_user_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($date_applicant['applicant_user_id']);
                $applicant_user_reviews = $this -> date_review_api_model -> get_date_reviews_by_user_id($date_applicant['applicant_user_id']);
                $applicant_user_common_interests = $this -> user_interest_api_model -> get_common_interests($this -> rest -> user_id, $date_applicant['applicant_user_id']);
                $applicant_user_mutual_friends = $this -> user_fb_friend_api_model -> get_mutual_friends($this -> rest -> user_id, $date_applicant['applicant_user_id']);

                unset($applicant);
                $applicant['attributes'] = $date_applicant;
                $applicant['relationships']['applicant_user']['attributes'] = $applicant_user;

                if (!empty($applicant_user_photos)) {
                    $applicant['relationships']['applicant_user']['relationships']['user_photos'] = $applicant_user_photos;
                }
                if (!empty($applicant_user_reviews)) {
                    $applicant['relationships']['applicant_user']['relationships']['date_reviews'] = $applicant_user_reviews;
                }
                if (!empty($applicant_user_common_interests)) {
                    $applicant['relationships']['applicant_user']['relationships']['common_interests'] = $applicant_user_common_interests;
                }
                if (!empty($applicant_user_mutual_friends)) {
                    $applicant['relationships']['applicant_user']['relationships']['mutual_friends'] = $applicant_user_mutual_friends;
                }

                $applicants[] = $applicant;
            }
        }

        $this -> response(array(
            'data' => $applicants
        ), 200);
    }

    public function my_dates_applicants_select_post($date_id, $date_applicant_id) {

        if (empty($date_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to choose applicant',
                        'detail' => 'Date id is not provided.'
                    )
                )
            ), 200);
        }
        if (empty($date_applicant_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to choose applicant',
                        'detail' => 'Date applicant id is not provided.'
                    )
                )
            ), 200);
        }

        $date = $this -> date_api_model -> get_date($date_id);

        if ($date -> requested_user_id != $this -> rest -> user_id) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to choose applicant',
                        'detail' => 'This is not a date you hosted.'
                    )
                )
            ), 200);
        }

        $date_applicant = $this -> date_applicant_api_model -> get_date_applicant($date_applicant_id);

        if ($date_applicant -> date_id != $date_id) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to choose applicant',
                        'detail' => 'Date applicant id is invalid.'
                    )
                )
            ), 200);
        }

        // Update date applicant
        $update_date_applicant_array['is_chosen'] = '1';

        $this -> date_applicant_api_model -> update_date_applicant($date_applicant_id, $update_date_applicant_array);

        // Get updated date applicant
        $date_applicant = $this -> date_applicant_api_model -> get_date_applicant($date_applicant_id);

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);
        $chosen_user = $this -> user_api_model -> get_user($date_applicant -> applicant_user_id);

        // Send Push Notification
        $message = "{$user -> first_name} has chosen you to be date.";
        $meta = array(
            'notification_type' => 'date_select',
            'date_id' => $date_id,
            'date_applicant_id' => $date_applicant_id
        );
        $meta_response = $this -> user_api_model -> send_push_notification_to_user($chosen_user -> user_id, $message, $meta);

        // [TODO]
        // Send email to host user
        $host_user_email = $this -> user_email_api_model -> get_user_email_by_user_id($this -> rest -> user_id);
        if (!empty($host_user_email)) {

        }

        // [TODO]
        // Send email to applicant user
        $applicant_user_email = $this -> user_email_api_model -> get_user_email_by_user_id($date_applicant -> applicant_user_id);
        if (!empty($applicant_user_email)) {

        }

        $this -> response(array(
            'data' => array(
                'attributes' => $date_applicant
            ),
            'meta' => !empty($meta_response) ? $meta_response : NULL
        ), 200);
    }

    public function my_dates_cancel_my_applicant_post($date_id) {

        if (empty($date_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to cancel applicant',
                        'detail' => 'Date id is not provided.'
                    )
                )
            ), 200);
        }

        $this -> date_applicant_api_model -> cancel_date_applicant_from_params($date_id, $this -> rest -> user_id);

        // Send Push Notification
        $date = $this -> date_api_model -> get_date($date_id);
        $requested_user = $this -> user_api_model -> get_user($date -> requested_user_id);
        if ($requested_user -> want_pn_date_cancellation == 1) {    // Check requested user's settings

            $user = $this -> user_api_model -> get_user($this -> rest -> user_id);
            $message = "{$user -> first_name} has cancelled your date.";
            $meta = array(
                'notification_type' => 'date_cancel',
                'date_id' => $date_id
            );
            $this -> user_api_model -> send_push_notification_to_user($date -> requested_user_id, $message, $meta);
        }

        $this -> response(array(
            'meta' => array(
                'status' => TRUE,
                'detail' => 'Your date applicant is cancelled.'
            )
        ), 200);
    }

    public function my_dates_cancel_my_date_post($date_id) {

        if (empty($date_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to cancel date',
                        'detail' => 'Date id is not provided.'
                    )
                )
            ), 200);
        }

        // Update date
        $update_date_array['status'] = -1;
        $update_date_array['cancel_date_time'] = SQL_DATETIME;

        $this -> date_api_model -> update_date($date_id, $update_date_array);

        // Get cancelled date
        $date = $this -> date_api_model -> get_date($date_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $date
            )
        ), 200);
    }

    public function my_dates_invite_matches_post($date_id) {

        if (empty($date_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to invite matches',
                        'detail' => 'Date id is not provided.'
                    )
                )
            ), 200);
        }

        $date = $this -> date_api_model -> get_date($date_id);

        if (empty($date)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to invite matches',
                        'detail' => 'Date id is invalid.'
                    )
                )
            ), 200);
        }

        if ($date -> requested_user_id != $this -> rest -> user_id) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to invite matches',
                        'detail' => 'The date specified by date_id was not posted by you.'
                    )
                )
            ), 200);
        }

        $matches_count = $this -> post('matches_count');
        if (empty($matches_count)) $matches_count = 5;

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        if ($user -> num_date_tix < $matches_count) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to invite matches',
                        'detail' => 'You don\'t have enough date tickets in your account.'
                    )
                )
            ), 200);
        }

        $invite_info_array = array();
        $invited_users_count = 0;

        // Find matches
        $profile_matches = $this -> user_api_model -> find_profile_matches($this -> rest -> user_id, $matches_count);
        if (!empty($profile_matches)) {

            $date_type = $this -> date_type_api_model -> get_date_type($date -> date_type_id);
            $merchant = $this -> merchant_api_model -> get_merchant($date -> merchant_id);

            $message = "{$user -> first_name} has invited you to date for {$date_type -> description} @ '{$merchant -> name}' at {$date -> date_time}";
            $meta = array(
                'notification_type' => 'date_invite',
                'date_id' => $date_id
            );

            foreach ($profile_matches as $profile_match) {

                unset($invite_info);
                $invite_info['user']['attributes'] = $profile_match;

                // Send push notification to the match
                $meta_response = $this -> user_api_model -> send_push_notification_to_user($profile_match['user_id'], $message, $meta);

                $invite_info['meta'] = $meta_response;

                if ($meta_response['status'] == TRUE) {

                    // Insert date invite
                    $insert_date_invite_array['date_id'] = $date_id;
                    $insert_date_invite_array['invite_user_id'] = $profile_match['user_id'];
                    $insert_date_invite_array['invite_time'] = SQL_DATETIME;
                    $insert_date_invite_array['status'] = 0;

                    $this -> date_invite_api_model -> delete_date_invite_with_params($date_id, $profile_match['user_id']);
                    $date_invite_id = $this -> date_invite_api_model -> insert_date_invite($insert_date_invite_array);
                    $date_invite = $this -> date_invite_api_model -> get_date_invite($date_invite_id);

                    $invite_info['date_invite'] = $date_invite;

                    // Insert user decision
                    $insert_user_decision_array['target_user_id'] = $profile_match['user_id'];
                    $insert_user_decision_array['user_id'] = $this -> rest -> user_id;
                    $insert_user_decision_array['decision'] = 1;
                    $insert_user_decision_array['decision_time'] = SQL_DATETIME;

                    $this -> user_decision_api_model -> delete_user_decision_with_params($profile_match['user_id'], $this -> rest -> user_id);
                    $user_decision_id = $this -> user_decision_api_model -> insert_user_decision($insert_user_decision_array);
                    $user_decision = $this -> user_decision_api_model -> get_user_decision($user_decision_id);

                    $invite_info['user_decision'] = $user_decision;

                    $invited_users_count ++;
                }

                $invite_info_array[] = $invite_info;
            }
        }

        if ($invited_users_count > 0) {

            // Decrease user's date tickets
            $update_user_array['num_date_tix'] = $user -> num_date_tix - $invited_users_count;
            $this -> user_api_model -> update_user($this -> rest -> user_id, $update_user_array);

            // Insert date ticket log
            $insert_log_date_ticket_array['user_id'] = $this -> rest -> user_id;
            $insert_log_date_ticket_array['num_date_tickets'] = $invited_users_count;
            $insert_log_date_ticket_array['transaction_time'] = SQL_DATETIME;
            $insert_log_date_ticket_array['description'] = "Invited matches to date {$date_id}";

            $this -> log_user_date_ticket_api_model -> insert_log_user_date_ticket($insert_log_date_ticket_array);
        }

        // Get updated user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $this -> response(array(
            'data' => $invite_info_array,
            'included' => array(
                'user' => array(
                    'attributes' => $user
                )
            ),
            'meta' => array(
                'status' => TRUE,
                'invited_users_count' => $invited_users_count
            )
        ), 200);
    }
}
