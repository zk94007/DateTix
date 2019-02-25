<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Settings_API extends  MY_REST_Controller {

    function __construct() {
        parent::__construct();

        $this -> load -> model('api/av2/account_status_api_model');
        $this -> load -> model('api/av2/display_language_api_model');
        $this -> load -> model('api/av2/user_api_model');
        $this -> load -> model('api/av2/user_email_api_model');
        $this -> load -> model('api/av2/user_membership_option_api_model');
        $this -> load -> model('api/av2/user_order_api_model');

        $this -> load -> model('api/av2/user_preferred_date_type_api_model');
        $this -> load -> model('api/av2/date_type_api_model');

        $this -> load -> model('api/av2/user_want_gender_api_model');
        $this -> load -> model('api/av2/gender_api_model');

        $this -> load -> model('api/av2/user_want_ethnicity_api_model');
        $this -> load -> model('api/av2/ethnicity_api_model');

        $this -> load -> model('api/av2/user_want_body_type_api_model');
        $this -> load -> model('api/av2/body_type_api_model');

        $this -> load -> model('api/av2/user_want_child_plan_api_model');
        $this -> load -> model('api/av2/child_plan_api_model');

        $this -> load -> model('api/av2/user_want_child_status_api_model');
        $this -> load -> model('api/av2/child_status_api_model');

        $this -> load -> model('api/av2/user_want_descriptive_word_api_model');
        $this -> load -> model('api/av2/descriptive_word_api_model');

        $this -> load -> model('api/av2/user_want_education_level_api_model');
        $this -> load -> model('api/av2/education_level_api_model');

        $this -> load -> model('api/av2/user_want_relationship_status_api_model');
        $this -> load -> model('api/av2/relationship_status_api_model');

        $this -> load -> model('api/av2/user_want_relationship_type_api_model');
        $this -> load -> model('api/av2/relationship_type_api_model');

        $this -> load -> model('api/av2/user_want_religious_belief_api_model');
        $this -> load -> model('api/av2/religious_belief_api_model');

        $this -> load -> model('api/av2/user_want_residence_type_api_model');
        $this -> load -> model('api/av2/residence_type_api_model');

        $this -> load -> model('api/av2/user_want_smoking_status_api_model');
        $this -> load -> model('api/av2/smoking_status_api_model');

        $this -> load -> model('api/av2/user_want_drinking_status_api_model');
        $this -> load -> model('api/av2/drinking_status_api_model');

        $this -> load -> model('api/av2/user_want_exercise_frequency_api_model');
        $this -> load -> model('api/av2/exercise_frequency_api_model');

        $this -> load -> model('api/av2/user_want_school_api_model');
        $this -> load -> model('api/av2/school_api_model');

        $this -> load -> model('api/av2/user_want_company_api_model');
        $this -> load -> model('api/av2/company_api_model');

        $this -> load -> model('api/av2/user_want_school_subject_api_model');
    }

    public function index_get() {

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);
        $is_premium_member = $this -> user_membership_option_api_model -> is_upgraded_user($this -> rest -> user_id);
        $last_upgrade_date = $this -> user_order_api_model -> get_last_upgrade_date($this -> rest -> user_id);

        $user_email = $this -> user_email_api_model -> get_user_email_by_user_id($this -> rest -> user_id);
        $display_language = $this -> display_language_api_model -> get_display_language($user -> last_display_language_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(
                    'user_email' => $user_email,
                    'display_language' => $display_language
                ),
                'meta' => array(
                    'is_premium_member' => $is_premium_member,
                    'last_upgrade_date' => $last_upgrade_date
                )
            )
        ), 200);
    }

    public function index_post() {

        $account_status_id = $this -> post('account_status_id');
        $mobile_international_code = $this -> post('mobile_international_code');
        $mobile_phone_number = $this -> post('mobile_phone_number');
        $email_address = $this -> post('email_address');
        $want_pn_date_invite = $this -> post('want_pn_date_invite');
        $want_pn_new_applicant = $this -> post('want_pn_new_applicant');
        $want_pn_chat_message = $this -> post('want_pn_chat_message');
        $want_pn_date_cancellation = $this -> post('want_pn_date_cancellation');
        $display_language_id = $this -> post('display_language_id');

        // Build update array
        if (!empty($account_status_id))
            $update_array['account_status_id'] = $account_status_id;

        if (!empty($display_language_id))
            $update_array['last_display_language_id'] = $display_language_id;

        if (!empty($mobile_international_code))
            $update_array['mobile_international_code'] = $mobile_international_code;

        if (!empty($mobile_phone_number))
            $update_array['mobile_phone_number'] = $mobile_phone_number;

        if ($want_pn_date_invite === '0' || !empty($want_pn_date_invite))
            $update_array['want_pn_date_invite'] = $want_pn_date_invite;

        if ($want_pn_new_applicant === '0' || !empty($want_pn_new_applicant))
            $update_array['want_pn_new_applicant'] = $want_pn_new_applicant;

        if ($want_pn_chat_message === '0' || !empty($want_pn_chat_message))
            $update_array['want_pn_chat_message'] = $want_pn_chat_message;

        if ($want_pn_date_cancellation === '0' || !empty($want_pn_date_cancellation))
            $update_array['want_pn_date_cancellation'] = $want_pn_date_cancellation;

        // Update user data
        if (!empty($update_array)) {

            $this -> user_api_model -> update_user($this -> rest -> user_id, $update_array);
        }

        // Update user email
        if (!empty($email_address)) {
            $this -> user_email_api_model -> update_user_email_with_params($this -> rest -> user_id, $email_address);
        }

        // Get updated user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $user_email = $this -> user_email_api_model -> get_user_email_by_user_id($this -> rest -> user_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(
                    'user_email' => $user_email
                )
            )
        ), 200);
    }
}