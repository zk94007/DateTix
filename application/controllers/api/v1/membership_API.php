<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Membership_API extends REST_Controller {

    function __construct() {
        parent::__construct();

        $this -> load -> model('api/v1/api_key_model');
        $this -> load -> model('api/v1/date_api_model');
        $this -> load -> model('api/v1/log_user_date_ticket_api_model');
        $this -> load -> model('api/v1/phone_verification_code_api_model');
        $this -> load -> model('api/v1/user_membership_option_api_model');
        $this -> load -> model('api/v1/user_api_model');
        $this -> load -> model('api/v1/user_chat_api_model');
        $this -> load -> model('api/v1/user_email_api_model');
        $this -> load -> model('api/v1/user_photo_api_model');
        $this -> load -> model('api/v1/user_want_gender_api_model');
    }

    public function request_verification_code_post() {

        $mobile_international_code = $this -> post('mobile_international_code');
        $mobile_phone_number = $this -> post('mobile_phone_number');

        $errors = array();

        if (empty($mobile_international_code)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to generate verification code',
                'detail' => 'Mobile international code is not provided.'
            );
        }
        if (empty($mobile_phone_number)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to generate verification code',
                'detail' => 'Mobile phone number is not provided.'
            );
        }

        if (count($errors) > 0) {

            $this -> response(array(
                'errors' => $errors
            ), 200);
        }

        $verification_code = $this -> generate_random_digits(4);

        $full_mobile_number = $mobile_international_code . ' ' . $mobile_phone_number;

        $twilio_response = $this -> send_verification_code($full_mobile_number, $verification_code);

        if ($twilio_response -> IsError) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to generate verification code',
                        'detail' => 'Failed to send verification code to your phone number with error: ' . $twilio_response -> ErrorMessage
                    )
                )
            ), 200);
        }

        $this -> phone_verification_code_api_model -> insert_or_update_phone_verification_code($full_mobile_number, $verification_code);

        $exists = $this -> user_api_model -> is_mobile_number_exists($mobile_international_code, $mobile_phone_number);

        $this -> response(array(
            'meta' => array(
                'status' => TRUE,
                'verification_code' => $verification_code,
                'phone_number_exists' => $exists
            )
        ), 200);
    }

    public function validate_verification_code_post() {

        $mobile_international_code = $this -> post('mobile_international_code');
        $mobile_phone_number = $this -> post('mobile_phone_number');
        $verification_code = $this -> post('verification_code');

        $errors = array();

        if (empty($mobile_international_code)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to validate verification code',
                'detail' => 'Mobile international code is not provided.'
            );
        }
        if (empty($mobile_phone_number)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to validate verification code',
                'detail' => 'Mobile phone number is not provided.'
            );
        }
        if (empty($verification_code)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to validate verification code',
                'detail' => 'Verification code is not provided.'
            );
        }

        if (count($errors) > 0) {

            $this -> response(array(
                'errors' => $errors
            ), 200);
        }

        $full_mobile_number = $mobile_international_code . ' ' . $mobile_phone_number;

        // Get verification code
        $stored_verification_code = $this -> phone_verification_code_api_model -> get_verification_code($full_mobile_number);

        if ($stored_verification_code != $verification_code) {

            $this -> response(array(
                'meta' => array(
                    'status' => FALSE,
                    'detail' => 'Verification code is invalid.'
                )
            ), 200);
        }

        $this -> phone_verification_code_api_model -> delete_phone_verification_code($full_mobile_number);

        $this -> response(array(
            'meta' => array(
                'status' => TRUE,
                'detail' => "The phone number '{$full_mobile_number}' is verified."
            )
        ), 200);
    }

    private function send_verification_code($full_mobile_number, $verification_code) {

        // Send verification code to mobile phone number
        $from = '+61 428436828';
        $to = $full_mobile_number;
        $sms_message = 'Here is a verification code from DateTix. Please Enter \'' . $verification_code . '\' on the sign up page to verify your phone number.';

        $this -> load -> library('twilio');

        $twilio_response = $this -> twilio -> sms($from, $to, $sms_message);

        return $twilio_response;
    }

    private function send_sms($to, $message) {

        $from = '+61 428436828';
        $this -> load -> library('twilio');

        $twilio_response = $this -> twilio -> sms($from, $to, $message);

        return $twilio_response;
    }

    private function generate_random_digits($length = 4) {

        return str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    public function sign_up_post() {

        $mobile_international_code = $this -> post('mobile_international_code');
        $mobile_phone_number = $this -> post('mobile_phone_number');
        $password = $this -> post('password');
        $email_address = $this -> post('email_address');
        $first_name = $this -> post('first_name');
        $last_name = $this -> post('last_name');
        $gender_id = $this -> post('gender_id');
        $media_source = $this -> post('media_source');
        

        $errors = array();
        if (empty($mobile_international_code)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to Sign Up',
                'detail' => 'Mobile international code is empty.'
            );
        }
        if (empty($mobile_phone_number)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to Sign Up',
                'detail' => 'Mobile phone number is empty.'
            );
        }
        if (empty($password)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to Sign Up',
                'detail' => 'Password is empty.'
            );
        }
        if (empty($email_address)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to Sign Up',
                'detail' => 'Email address is empty.'
            );
        }
        if (empty($first_name)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to Sign Up',
                'detail' => 'First name is empty.'
            );
        }
        if (empty($last_name)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to Sign Up',
                'detail' => 'Last name is empty.'
            );
        }
        if (empty($gender_id)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to Sign Up',
                'detail' => 'Gender id is empty.'
            );
        }
        if (empty($_FILES)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to Sign Up',
                'detail' => 'User photo file is empty.'
            );
        }
        if (count($errors) > 0) {

            $this -> response(array(
                'errors' => $errors
            ), 200);
        }

        // Check if email address already exists
        if ($this -> user_api_model -> is_email_address_exists($email_address)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to Sign Up',
                        'detail' => 'Your email address is already being used by our system.'
                    )
                )
            ), 200);
        }

        // Check if mobile number is already exists
        if ($this -> user_api_model -> is_mobile_number_exists($mobile_international_code, $mobile_phone_number)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to Sign Up',
                        'detail' => 'Your mobile phone number is already being used by our system.'
                    )
                )
            ), 200);
        }

        // Insert user data
        $insert_array['first_name'] = $first_name;
        $insert_array['last_name'] = $last_name;
        $insert_array['gender_id'] = $gender_id;
        $insert_array['password'] = sha1($password);

        $insert_array['mobile_international_code'] = $mobile_international_code;
        $insert_array['mobile_phone_number'] = $mobile_phone_number;
        $insert_array['mobile_phone_is_verified'] = '1';

        $insert_array['completed_application_step'] = '10';
        $insert_array['media_source'] = $media_source;
        if($mobile_international_code == "+852" || $mobile_international_code == "852" )
        {
        	$insert_array['current_city_id'] = "260";
        }
        
        $insert_array['num_date_tix'] = 100;
        $insert_array['applied_date'] = date('Y-m-d H:i:s');

        $user_id = $this -> user_api_model -> insert_user($insert_array);

        // Insert user email
        $this -> user_email_api_model -> insert_user_email_with_params($user_id, $email_address);

        // Upload user photo file
        $folder_path = $this -> user_photo_api_model -> create_user_photos_folder($user_id);
        $file_extension = substr(current($_FILES)['name'], strrpos(current($_FILES)['name'], '.') + 1);

        $config['upload_path'] = $folder_path;
        $config['file_name'] = strtotime(SQL_DATETIME) . "_profile_pic.$file_extension";
        $config['allowed_types'] = '*';
        $config['overwrite'] = TRUE;

        $this -> load -> library('upload', $config);

        $this -> upload -> do_upload(key($_FILES));

        // Insert user photo
        $user_photo_id = $this -> user_photo_api_model -> insert_user_photo_with_upload_data($user_id, $this -> upload -> data());

        // Get created user photo
        $user_photo = $this -> user_photo_api_model -> get_user_photo($user_photo_id);

        // Insert user want gender
        $insert_user_want_gender_array['user_id'] = $user_id;
        if ($gender_id == 1) {
            $insert_user_want_gender_array['gender_id'] = 2;
        } else {
            $insert_user_want_gender_array['gender_id'] = 1;
        }
        $this -> user_want_gender_api_model -> insert_user_want_gender($insert_user_want_gender_array);

        // Get created user data
        $user = $this -> user_api_model -> get_user($user_id);

        // Generate api_key for the user
        $user_api_key = $this -> api_key_model -> generate_api_key();

        // Insert or update api_key for the user
        if ($this -> api_key_model -> api_key_exists_for_user_id($user_id)) {

            if (!$this -> api_key_model -> update_api_key_for_user_id($user_id, $user_api_key)) {

                $this -> response(array(
                    'errors' => array(
                        array(
                            'id' => 'Currently not supported',
                            'code' => 'Currently not supported',
                            'title' => 'Failed to Sign In',
                            'detail' => 'Your account is created successfully, but failed to prepare access for your account. Please try to sign in again.'
                        )
                    )
                ), 200);
            }
        } else {

            if (!$this -> api_key_model -> insert_api_key($user_api_key, array('user_id' => $user_id))) {

                $this -> response(array(
                    'errors' => array(
                        array(
                            'id' => 'Currently not supported',
                            'code' => 'Currently not supported',
                            'title' => 'Failed to Sign In',
                            'detail' => 'Your account is created successfully, but failed to prepare access for your account. Please try to sign in again.'
                        )
                    )
                ), 200);
            }
        }

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(
                    'user_photos' => array(
                        array(
                            'set_primary' => $user_photo -> set_primary,
                            'url' => base_url() . "user_photos/user_{$user_id}/" . $user_photo -> photo
                        )
                    )
                ),
                'meta' => array(
                    'api_key' => $user_api_key
                )
            )
        ), 200);
    }

    public function sign_in_post() {

        $mobile_international_code = $this -> post('mobile_international_code');
        $mobile_phone_number = $this -> post('mobile_phone_number');
        $password = $this -> post('password');

        $errors = array();

        if (empty($mobile_international_code)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to sign in',
                'detail' => 'Mobile international code is not provided.'
            );
        }
        if (empty($mobile_phone_number)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to sign in',
                'detail' => 'Mobile phone number is not provided.'
            );
        }
        if (empty($password)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to sign in',
                'detail' => 'Password is not provided.'
            );
        }

        if (count($errors) > 0) {

            $this -> response(array(
                'errors' => $errors
            ), 200);
        }

        // Get user data by mobile phone number
        if (!($user = $this -> user_api_model -> get_user_by_mobile_number($mobile_international_code, $mobile_phone_number))) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to sign in',
                        'detail' => 'Your phone number does not exist.'
                    )
                )
            ), 200);
        }

        // Compare passwords
        if (!($user -> password === sha1($password))) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to sign in',
                        'detail' => 'Your password is incorrect.'
                    )
                )
            ), 200);
        }

        // Success
        $user_id = $user -> user_id;

        // Get user photos
        $user_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($user_id);

        // Get meta information
        $found_date_ids = $this -> date_api_model -> find_date_ids($user_id, 0);
        $find_dates_count = count($found_date_ids);

        $found_date_ids_by_distance = $this -> date_api_model -> find_date_ids($user_id, 1);
        $find_dates_count_by_distance = count($found_date_ids_by_distance);

        $found_user_ids = $this -> user_api_model -> find_people_ids($user_id, 0);
        $find_people_count = count($found_user_ids);

        $found_user_ids_by_distance = $this -> user_api_model -> find_people_ids($user_id, 1);
        $find_people_count_by_distance = count($found_user_ids_by_distance);

        $unread_messages_count = $this -> user_chat_api_model -> get_unread_messages_count($user_id);

        $my_upcoming_dates = $this -> date_api_model -> get_my_dates($user_id, 0, 0, TRUE, FALSE);
        $my_upcoming_dates_count = count($my_upcoming_dates);

        // Generate api_key for the user
        $user_api_key = $this -> api_key_model -> generate_api_key();

        // Insert or update api_key for the user
        if ($this -> api_key_model -> api_key_exists_for_user_id($user_id)) {

            if (!$this -> api_key_model -> update_api_key_for_user_id($user_id, $user_api_key)) {

                $this -> response(array(
                    'errors' => array(
                        array(
                            'id' => 'Currently not supported',
                            'code' => 'Currently not supported',
                            'title' => 'Failed to Sign In',
                            'detail' => 'Your credential information is correct, but failed to prepare access for your account. Please try to sign in again.'
                        )
                    )
                ), 200);
            }
        } else {

            if (!$this -> api_key_model -> insert_api_key($user_api_key, array('user_id' => $user_id))) {

                $this -> response(array(
                    'errors' => array(
                        array(
                            'id' => 'Currently not supported',
                            'code' => 'Currently not supported',
                            'title' => 'Failed to Sign In',
                            'detail' => 'Your credential information is correct. but failed to prepare access for your account. Please try to sign in again.'
                        )
                    )
                ), 200);
            }
        }

        // Get updated user data
        $user = $this -> user_api_model -> get_user($user_id);
        $is_premium_member = $this -> user_membership_option_api_model -> is_upgraded_user($user_id);
        $user -> is_premium_member = $is_premium_member;

        // Check daily sign in
        $rewarded_today = $this -> log_user_date_ticket_api_model -> check_daily_sign_in_reward($user_id, SQL_DATETIME);
        if ($rewarded_today == FALSE) {
            $log_user_date_ticket_id = $this -> log_user_date_ticket_api_model -> insert_daily_sign_in_reward_log($user_id, 5, SQL_DATETIME);
        }

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(
                    'user_photos' => $user_photos
                ),
                'meta' => array(
                    'api_key' => $user_api_key,
                    'find_dates_count' => $find_dates_count,
                    'find_dates_count_by_distance' => $find_dates_count_by_distance,
                    'find_people_count' => $find_people_count,
                    'find_people_count_by_distance' => $find_people_count_by_distance,
                    'unread_messages_count' => $unread_messages_count,
                    'my_upcoming_dates_count' => $my_upcoming_dates_count
                )
            ),
            'meta' => array(
                'status' => TRUE,
                'daily_sign_in_reward' => !empty($log_user_date_ticket_id) ? TRUE : FALSE
            )
        ), 200);
    }

    public function reset_password_post() {

        $mobile_international_code = $this -> post('mobile_international_code');
        $mobile_phone_number = $this -> post('mobile_phone_number');

        $errors = array();

        if (empty($mobile_international_code)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to reset password',
                'detail' => 'Mobile international code is not provided.'
            );
        }
        if (empty($mobile_phone_number)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to reset password',
                'detail' => 'Mobile phone number is not provided.'
            );
        }

        if (count($errors) > 0) {

            $this -> response(array(
                'errors' => $errors
            ), 200);
        }

        if ($this -> user_api_model -> is_mobile_number_exists($mobile_international_code, $mobile_phone_number) == 0) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to reset password',
                        'detail' => 'The phone number does not exist in the system.'
                    )
                )
            ), 200);
        }

        $user = $this -> user_api_model -> get_user_by_mobile_number($mobile_international_code, $mobile_phone_number);

        $reset_password_code = $this -> generate_random_digits(6);

        // Save reset code
        $update_user_array['reset_password_code'] = $reset_password_code;
        $this -> user_api_model -> update_user($user -> user_id, $update_user_array);

        // Send reset code via SMS
        $this -> load -> library('utility');
        $encoded_user_id = $this -> utility -> encode($user -> user_id);
        $encoded_reset_code = $this -> utility -> encode($reset_password_code);

        // Message should not exceed 160 characters.
        $message = base_url() . "/api/v1/reset_password?p1={$encoded_user_id}&p2={$encoded_reset_code}";

        $full_mobile_number = $mobile_international_code . ' ' . $mobile_phone_number;

        $twilio_response = $this -> send_sms($full_mobile_number, $message);

        if ($twilio_response -> IsError) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to reset password',
                        'detail' => 'Failed to send reset information to your phone number with error: ' . $twilio_response -> ErrorMessage
                    )
                )
            ), 200);
        }

        $this -> response(array(
            'meta' => array(
                'status' => TRUE,
                'detail' => 'We\'ve sent you a SMS to your phone number for resetting your password. Please check the link on it.'
            )
        ), 200);
    }

    public function reset_password_get() {

        $encoded_user_id = $this -> get('p1');
        $encoded_reset_password_code = $this -> get('p2');

        if (empty($encoded_user_id) || empty($encoded_reset_password_code)) {

            $this -> response('Given URL is invalid.', 200);
        }

        $this -> load -> library('utility');

        $decoded_user_id = $this -> utility -> decode($encoded_user_id);
        $decoded_reset_password_code = $this -> utility -> decode($encoded_reset_password_code);

        // Get user data
        $user = $this -> user_api_model -> get_user($decoded_user_id);

        if (empty($user)) {

            $this -> response('Given URL is invalid.', 200);
        }

        if ($decoded_reset_password_code != $user -> reset_password_code) {

            $this -> response('Given URL is invalid.', 200);
        }

        $new_password = $this -> generate_random_digits(6);

        // Save new password
        $update_user_array['reset_password_code'] = '';
        $update_user_array['password'] = sha1($new_password);
        $this -> user_api_model -> update_user($user -> user_id, $update_user_array);

        // Send new password via SMS
        $message = translate_phrase("Hi ") . $user -> first_name . ' ' . $user -> last_name . "\n\r";
        $message .= translate_phrase("We have reset your password according to your request. Your new password is '") . $new_password . "'.";

        $full_mobile_number = $user -> mobile_international_code . ' ' . $user -> mobile_phone_number;

        $twilio_response = $this -> send_sms($full_mobile_number, $message);

        if ($twilio_response -> IsError) {

            $this -> response('Failed to send new password to your phone number with error: ' . $twilio_response -> ErrorMessage, 200);
        }

        $this -> response('We\'ve sent you a SMS to your phone number with new password.', 200);
    }

    public function facebook_sign_in_post() {

        $facebook_id = $this -> post('facebook_id');

        // Check if facebook id already exists
        if ($this -> user_api_model -> is_facebook_id_exists($facebook_id)) {

            $user = $this -> user_api_model -> get_user_by_facebook_id($facebook_id);
            $is_premium_member = $this -> user_membership_option_api_model -> is_upgraded_user($user -> user_id);
            $user -> is_premium_member = $is_premium_member;

            // Get user photos
            $user_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($user -> user_id);

            // Get meta information
            $found_date_ids = $this -> date_api_model -> find_date_ids($user -> user_id, 0);
            $find_dates_count = count($found_date_ids);

            $found_date_ids_by_distance = $this -> date_api_model -> find_date_ids($user -> user_id, 1);
            $find_dates_count_by_distance = count($found_date_ids_by_distance);

            $found_user_ids = $this -> user_api_model -> find_people_ids($user -> user_id, 0);
            $find_people_count = count($found_user_ids);

            $found_user_ids_by_distance = $this -> user_api_model -> find_people_ids($user -> user_id, 1);
            $find_people_count_by_distance = count($found_user_ids_by_distance);

            $unread_messages_count = $this -> user_chat_api_model -> get_unread_messages_count($user -> user_id);

            $my_upcoming_dates = $this -> date_api_model -> get_my_dates($user -> user_id, 0, 0, TRUE, FALSE);
            $my_upcoming_dates_count = count($my_upcoming_dates);

            // Generate api_key for the user
            $user_api_key = $this -> api_key_model -> generate_api_key();

            // Insert or update api_key for the user
            if ($this -> api_key_model -> api_key_exists_for_user_id($user -> user_id)) {

                if (!$this -> api_key_model -> update_api_key_for_user_id($user -> user_id, $user_api_key)) {

                    $this -> response(array(
                        'errors' => array(
                            array(
                                'id' => 'Currently not supported',
                                'code' => 'Currently not supported',
                                'title' => 'Failed to Sign In',
                                'detail' => 'Your facebook account already exists, but failed to prepare access for your account. Please try to sign in again.'
                            )
                        )
                    ), 200);
                }
            } else {

                if (!$this -> api_key_model -> insert_api_key($user_api_key, array('user_id' => $user -> user_id))) {

                    $this -> response(array(
                        'errors' => array(
                            array(
                                'id' => 'Currently not supported',
                                'code' => 'Currently not supported',
                                'title' => 'Failed to Sign In',
                                'detail' => 'Your facebook account already exists, but failed to prepare access for your account. Please try to sign in again.'
                            )
                        )
                    ), 200);
                }
            }

            // Check daily sign in
            $rewarded_today = $this -> log_user_date_ticket_api_model -> check_daily_sign_in_reward($user -> user_id, SQL_DATETIME);
            if ($rewarded_today == FALSE) {
                $log_user_date_ticket_id = $this -> log_user_date_ticket_api_model -> insert_daily_sign_in_reward_log($user -> user_id, 5, SQL_DATETIME);
            }

            $this -> response(array(
                'data' => array(
                    'attributes' => $user,
                    'relationships' => array(
                        'user_photos' => $user_photos
                    ),
                    'meta' => array(
                        'status' => TRUE,
                        'detail' => 'Your facebook account already exists. You are ready to sign in using provided API Key.',
                        'api_key' => $user_api_key,
                        'find_dates_count' => $find_dates_count,
                        'find_dates_count_by_distance' => $find_dates_count_by_distance,
                        'find_people_count' => $find_people_count,
                        'find_people_count_by_distance' => $find_people_count_by_distance,
                        'unread_messages_count' => $unread_messages_count,
                        'my_upcoming_dates_count' => $my_upcoming_dates_count
                    )
                ),
                'meta' => array(
                    'status' => TRUE,
                    'daily_sign_in_reward' => !empty($log_user_date_ticket_id) ? TRUE : FALSE
                )
            ), 200);
        }

        // Get parameters
        $first_name = $this -> post('first_name');
        $last_name = $this -> post('last_name');
        $gender_id = $this -> post('gender_id');
        $mobile_international_code = $this -> post('mobile_international_code');
        $mobile_phone_number = $this -> post('mobile_phone_number');
        $user_photo_url = $this -> post('user_photo_url');
        $birth_date_str = $this -> post('birth_date');
        $media_source = $this -> post('media_source');
        $email_address = $this -> post('email_address');

        $errors = array();
        if (empty($facebook_id)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to Sign Up',
                'detail' => 'Facebook id is empty.'
            );
        }
        if (empty($first_name)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to Sign Up',
                'detail' => 'First name is empty.'
            );
        }
        if (empty($last_name)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to Sign Up',
                'detail' => 'Last name is empty.'
            );
        }
        /*if (empty($email_address)) {
        
        	$errors[] = array(
        			'id' => 'Currently not supported',
        			'code' => 'Currently not supported',
        			'title' => 'Failed to Sign Up',
        			'detail' => 'Email address is empty.'
        	);
        }*/
        if (empty($gender_id)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to Sign Up',
                'detail' => 'Gender id is empty.'
            );
        }
        if (!empty($birth_date_str)) {

            // Check date validation
            $birth_date = DateTime::createFromFormat('Y-m-d', $birth_date_str);
            $date_errors = DateTime::getLastErrors();
            if ($date_errors['warning_count'] + $date_errors['error_count'] > 0) {

                $errors[] = array(
                    'id' => 'Currently not supported',
                    'code' => 'Currently not supported',
                    'title' => 'Failed to Sign Up',
                    'detail' => 'Birth date is invalid.'
                );
            }
        }
        if (count($errors) > 0) {

            $this -> response(array(
                'errors' => $errors
            ), 200);
        }

        // Insert user data
        $insert_array['first_name'] = $first_name;
        $insert_array['last_name'] = $last_name;
        $insert_array['gender_id'] = $gender_id;
        $insert_array['facebook_id'] = $facebook_id;

        if (!empty($mobile_international_code) && !empty($mobile_phone_number)) {
            $insert_array['mobile_international_code'] = $mobile_international_code;
            $insert_array['mobile_phone_number'] = $mobile_phone_number;
			
            $insert_array['media_source'] = $media_source;
            if($mobile_international_code == "+852" || $mobile_international_code == "852" )
            {
            	$insert_array['current_city_id'] = "260";
            }
        }
        if (!empty($birth_date_str)) {

            $insert_array['birth_date'] = $birth_date_str;

            // Age related attributes
            $interval = $birth_date -> diff(new DateTime('now'));
            $age = $interval -> y;

            if ($gender_id == '1') {   // Male

                $insert_array['want_age_range_lower'] = $age / 2 + 3;
                $insert_array['want_age_range_upper'] = $age + 2;

            } else if ($gender_id == '2') {    // Female

                $insert_array['want_age_range_lower'] = $age - 2;
                $insert_array['want_age_range_upper'] = ($age - 3) * 2;
            }
        }

        $insert_array['num_date_tix'] = 100;
        $insert_array['applied_date'] = date('Y-m-d H:i:s');
        $insert_array['completed_application_step'] = '10';

        $user_id = $this -> user_api_model -> insert_user($insert_array);

        if (!empty($email_address)) {
        	// Insert user email
        	$this -> user_email_api_model -> insert_user_email_with_params($user_id, $email_address);
        }
        
        if (!empty($user_photo_url)) {

            // Download user photo from url
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $user_photo_url);
            curl_setopt($ch, CURLOPT_NOBODY, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            if (curl_exec($ch) !== FALSE) {
                $folder_path = $this->user_photo_api_model->create_user_photos_folder($user_id);
                $file_extension = 'png'; //substr($user_photo_url, strrpos($user_photo_url, '.') + 1);
                $file_name = strtotime(SQL_DATETIME) . "_profile_pic.$file_extension";

                $success = copy($user_photo_url, "$folder_path/$file_name");

                if ($success == TRUE) {

                    // Insert user photo
                    $insert_user_photo_array['user_id'] = $user_id;
                    $insert_user_photo_array['set_primary'] = 1;
                    $insert_user_photo_array['photo'] = $file_name;
                    $insert_user_photo_array['uploaded_time'] = SQL_DATETIME;

                    $user_photo_id = $this->user_photo_api_model->insert_user_photo($insert_user_photo_array);

                    // Get created user photo
                    $user_photo = $this->user_photo_api_model->get_user_photo($user_photo_id);
                }
            }
            curl_close($ch);
        }

        // Insert user want gender
        $insert_user_want_gender_array['user_id'] = $user_id;
        if ($gender_id == 1) {
            $insert_user_want_gender_array['gender_id'] = 2;
        } else {
            $insert_user_want_gender_array['gender_id'] = 1;
        }
        $this -> user_want_gender_api_model -> insert_user_want_gender($insert_user_want_gender_array);

        // Get created user data
        $user = $this -> user_api_model -> get_user($user_id);

        // Generate api_key for the user
        $user_api_key = $this -> api_key_model -> generate_api_key();

        // Insert or update api_key for the user
        if ($this -> api_key_model -> api_key_exists_for_user_id($user_id)) {

            if (!$this -> api_key_model -> update_api_key_for_user_id($user_id, $user_api_key)) {

                $this -> response(array(
                    'errors' => array(
                        array(
                            'id' => 'Currently not supported',
                            'code' => 'Currently not supported',
                            'title' => 'Failed to Sign In',
                            'detail' => 'Your account is created successfully, but failed to prepare access for your account. Please try to sign in again.'
                        )
                    )
                ), 200);
            }
        } else {

            if (!$this -> api_key_model -> insert_api_key($user_api_key, array('user_id' => $user_id))) {

                $this -> response(array(
                    'errors' => array(
                        array(
                            'id' => 'Currently not supported',
                            'code' => 'Currently not supported',
                            'title' => 'Failed to Sign In',
                            'detail' => 'Your account is created successfully, but failed to prepare access for your account. Please try to sign in again.'
                        )
                    )
                ), 200);
            }
        }

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(
                    'user_photos' => !empty($user_photo) ? array(
                        array(
                            'set_primary' => $user_photo -> set_primary,
                            'url' => base_url() . "user_photos/user_{$user_id}/" . $user_photo -> photo
                        )
                    ) : ''
                ),
                'meta' => array(
                    'api_key' => $user_api_key
                )
            )
        ), 200);
    }

    public function check_facebook_id_get() {

        $facebook_id = $this -> get('facebook_id');

        if (empty($facebook_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to Sign Up',
                        'detail' => 'Facebook id is empty.'
                    )
                )
            ), 200);
        }

        $exists = $this -> user_api_model -> is_facebook_id_exists($facebook_id);

        $this -> response(array(
            'meta' => array(
                'facebook_id_exists' => $exists
            )
        ), 200);
    }
}