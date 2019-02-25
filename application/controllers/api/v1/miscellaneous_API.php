<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Miscellaneous_API extends MY_REST_Controller {

    function __construct() {
        parent::__construct();

        $this -> load -> library('twilio');

        $this -> load -> model('api/v1/date_api_model');
        $this -> load -> model('api/v1/user_api_model');
        $this -> load -> model('api/v1/user_chat_api_model');
        $this -> load -> model('api/v1/user_membership_option_api_model');
        $this -> load -> model('api/v1/user_order_api_model');
    }

    public function validate_promotion_code_post() {

        $promotion_code = $this -> post('promotion_code');

        if (empty($promotion_code)) {

            $this -> response(array(
                'errors' => array(
                    'id' => 'Currently not supported',
                    'code' => 'Currently not supported',
                    'title' => 'Failed to process promotion code',
                    'detail' => 'Promotion code is not provided.'
                )
            ), 200);
        }

        // Get user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        if (empty($user -> promo_code)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to process promotion code',
                        'detail' => 'You already applied promotion code.'
                    )
                )
            ), 200);
        }
        if ($user -> promo_code != $promotion_code) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to process promotion code',
                        'detail' => 'Promotion code is invalid.'
                    )
                )
            ), 200);
        }

        // Update user data
        $update_user_array['num_date_tix'] = $user -> num_date_tix + 100;
        $update_user_array['promo_code'] = '';

        $this -> user_api_model -> update_user($this -> rest -> user_id, $update_user_array);

        // Get updated user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user
            )
        ), 200);
    }

    public function upgrade_account_post() {

        $order_membership_option_ids = $this -> post('order_membership_option_ids');
        $order_currency_id = $this -> post('order_currency_id');
        $order_amount = $this -> post('order_amount');
        $order_membership_duration_months = $this -> post('order_membership_duration_months');

        $errors = array();

        if (empty($order_membership_option_ids)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to upgrade account',
                'detail' => 'Membership option ids are not provided.'
            );
        }
        if (empty($order_currency_id)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to upgrade account',
                'detail' => 'Order currency id is not provided.'
            );
        }
        if (empty($order_amount)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to upgrade account',
                'detail' => 'Order amount is not provided.'
            );
        }
        if (empty($order_membership_duration_months)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to upgrade account',
                'detail' => 'Membership duration is not provided.'
            );
        }

        if (count($errors) > 0) {

            $this -> response(array(
                'errors' => $errors
            ), 200);
        }

        // Insert user order
        $insert_user_order_array['user_id'] = $this -> rest -> user_id;
        $insert_user_order_array['order_time'] = SQL_DATETIME;
        $insert_user_order_array['order_currency_id'] = $order_currency_id;
        $insert_user_order_array['order_amount'] = $order_amount;
        $insert_user_order_array['order_membership_options'] = implode(',', $order_membership_option_ids);
        $insert_user_order_array['order_membership_duration_months'] = $order_membership_duration_months;

        $user_order_id = $this -> user_order_api_model -> insert_user_order($insert_user_order_array);
        $user_order = $this -> user_order_api_model -> get_user_order($user_order_id);

        // Insert/update user membership options
        $this -> user_membership_option_api_model -> update_user_membership_options_with_params($this -> rest -> user_id, $order_membership_option_ids, $order_membership_duration_months);

        // [TODO]
        // Send email
        /*
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);
        $user_email = $this -> user_email_api_model -> get_user_email_by_user_id($this -> rest -> user_id);

        $name=$user -> first_name;
        $user_email_address=$user_email -> email_address;

        $upgrade_subject = translate_phrase("Your account has been upgraded");

        $email_template = $name.' , thanks for upgrading to a '.$order_membership_duration_months.' month premium membership';
        $this -> model_user -> send_email(INFO_EMAIL,$user_email_address, $upgrade_subject, $email_template,"html","DateTix");
        */

        $this -> response(array(
            'data' => $user_order
        ), 200);
    }

    public function super_date_request_post() {
    
    	$request_user_id = $this -> post('request_user_id');
    	$target_user_id = $this -> post('target_user_id');
    	$num_date_tickets = $this -> post('num_date_tickets');
    
    	$errors = array();
    	
    	if (empty($request_user_id)) {
    
    		$errors[] = array(
    				'id' => 'Currently not supported',
    				'code' => 'Currently not supported',
    				'title' => 'Failed to take a request',
    				'detail' => 'Requested User id is not provided.'
    		);
    	}
    	if (empty($target_user_id)) {
    
    		$errors[] = array(
    				'id' => 'Currently not supported',
    				'code' => 'Currently not supported',
    				'title' => 'Failed to take a request',
    				'detail' => 'Target User id is not provided.'
    		);
    	}
    	if (empty($num_date_tickets)) {
    
    		$errors[] = array(
    				'id' => 'Currently not supported',
    				'code' => 'Currently not supported',
    				'title' => 'Failed to take a request',
    				'detail' => 'Number  of ticket is not provided.'
    		);
    	}
    	
    	if (count($errors) > 0) {
    
    		$this -> response(array(
    				'errors' => $errors
    		), 200);
    	}
    
    	// Insert user order
    	$insert_super_date['request_user_id'] = $request_user_id;
    	$insert_super_date['request_time'] = SQL_DATETIME;
    	$insert_super_date['target_user_id'] = $target_user_id;
    	$insert_super_date['num_date_tickets'] = $num_date_tickets;
    	
    	$req_user = $this -> user_api_model -> get_user($request_user_id);
    	$tar_user = $this -> user_api_model -> get_user($target_user_id);
    	
    	$super_date = $this -> date_api_model -> insert_super_date($insert_super_date);
    	
    	$subject = translate_phrase("Your super date request has been sent.");
    	$user_email_address = "mikeye27@gmail.com";
    	$email_template = 'new super date request by '.$req_user->name.' at '.$req_user->mobile_phone_number.' for '.$req_user->name.' at '.$req_user->mobile_phone_number;
    	$this -> model_user -> send_email(INFO_EMAIL,$user_email_address, $subject , $email_template,"html","DateTix");
    	
    	$this -> response(array(
    			'data' => $insert_super_date
    	), 200);
    }

    public function invite_friends_post() {

        $my_name = $this -> post('my_name');
        $first_names = $this -> post('first_names');
        $last_names = $this -> post('last_names');
        $phone_numbers = $this -> post('phone_numbers');

        $errors = array();

        if (empty($my_name)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to invite friends',
                'detail' => 'My name is not provided.'
            );
        }
        if (empty($first_names)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to invite friends',
                'detail' => 'First names are not provided.'
            );
        }
        if (empty($last_names)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to invite friends',
                'detail' => 'Last names are not provided.'
            );
        }
        if (empty($phone_numbers)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to invite friends',
                'detail' => 'Phone numbers are not provided.'
            );
        }

        if (count($errors) > 0) {

            $this -> response(array(
                'errors' => $errors
            ), 200);
        }

        if (count($first_names) != count($last_names) || count($last_names) != count($phone_numbers)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to invite friends',
                        'detail' => 'Number of properties in provided arrays are not matched.'
                    )
                )
            ), 200);
        }

        $invited_friends = array();

        // Send invitation SMS
        for ($i = 0; $i < count($first_names); $i++) {

            if (!empty($phone_numbers[$i])) {

                $first_name = $first_names[$i];
                $last_name = $last_names[$i];
                $phone_number = $phone_numbers[$i];

                // Send SMS
                $from = '+61 428436828';
                $to = $phone_number;
                $message = "Hey this is {$my_name}.\n Just came across a cool new app. Try it and let me know what you think? http://datetix.hk/sms";

                $twilio_response = $this -> twilio -> sms($from, $to, $message);

                if ($twilio_response -> IsError)
                    continue;

                // Return invited friends
                $invited_friend['first_name'] = $first_name;
                $invited_friend['last_name'] = $last_name;
                $invited_friend['phone_number'] = $phone_number;

                $invited_friends[] = $invited_friend;
            }
        }

        $this -> response(array(
            'data' => $invited_friends
        ), 200);
    }

    public function get_my_info_get() {

        // Get user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        // Get meta information
        $found_date_ids = $this -> date_api_model -> find_date_ids($this -> rest -> user_id, 0);
        $find_dates_count = count($found_date_ids);

        $found_date_ids_by_distance = $this -> date_api_model -> find_date_ids($this -> rest -> user_id, 1);
        $find_dates_count_by_distance = count($found_date_ids_by_distance);

        $found_user_ids = $this -> user_api_model -> find_people_ids($this -> rest -> user_id, 0);
        $find_people_count = count($found_user_ids);

        $found_user_ids_by_distance = $this -> user_api_model -> find_people_ids($this -> rest -> user_id, 1);
        $find_people_count_by_distance = count($found_user_ids_by_distance);

        $unread_messages_count = $this -> user_chat_api_model -> get_unread_messages_count($this -> rest -> user_id);

        $my_upcoming_dates = $this -> date_api_model -> get_my_dates($this -> rest -> user_id, 0, 0, TRUE, FALSE);
        $my_upcoming_dates_count = count($my_upcoming_dates);


        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'meta' => array(
                    'find_dates_count' => $find_dates_count,
                    'find_dates_count_by_distance' => $find_dates_count_by_distance,
                    'find_people_count' => $find_people_count,
                    'find_people_count_by_distance' => $find_people_count_by_distance,
                    'unread_messages_count' => $unread_messages_count,
                    'my_upcoming_dates_count' => $my_upcoming_dates_count
                )
            )
        ), 200);
    }

    public function update_my_info_post() {

        $latitude = $this -> post('latitude');
        $longitude = $this -> post('longitude');
        $device_token = $this -> post('device_token');

        if (!empty($latitude) && floatval($latitude))
            $update_user_array['gps_lat'] = $latitude;

        if (!empty($longitude) && floatval($longitude))
            $update_user_array['gps_lng'] = $longitude;

        if (!empty($device_token))
            $update_user_array['device_token'] = $device_token;

        if (empty($update_user_array)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to update information',
                        'detail' => 'No parameter is provided.'
                    )
                )
            ), 200);
        }

        // Update user data
        $this -> user_api_model -> update_user($this -> rest -> user_id, $update_user_array);

        // Get updated user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user
            )
        ), 200);
    }

    public function send_push_notification_post() {

        $user_id = $this -> post('user_id');
        $message = $this -> post('message');
        $device_token = $this -> post('device_token');
        $debug = $this -> post('debug');

        if (empty($user_id) && empty($device_token)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to send push notification',
                        'detail' => 'User ID or Device Token should be provided.'
                    )
                )
            ), 200);
        }
        if (empty($message)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to send push notification',
                        'detail' => 'Message is not provided.'
                    )
                )
            ), 200);
        }

        $meta = array(
            'status' => FALSE,
            'detail' => 'No push notification is sent.'
        );

        if (!empty($user_id)) {
            $meta = $this->user_api_model->send_push_notification_to_user($user_id, $message, '', $debug == '1' ? TRUE : FALSE);
        } else if (!empty($device_token)) {
            $meta = $this->user_api_model->send_push_notification_to_device($device_token, $message, '', $debug == '1' ? TRUE : FALSE);
        }

        $this -> response(array(
            'meta' => $meta
        ), 200);
    }
}
