<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Chats_API extends MY_REST_Controller {

    function __construct() {
        parent::__construct();

        $this -> load -> model('api/v1/date_api_model');
        $this -> load -> model('api/v1/date_type_api_model');
        $this -> load -> model('api/v1/merchant_api_model');
        $this -> load -> model('api/v1/user_api_model');
        $this -> load -> model('api/v1/user_chat_api_model');
        $this -> load -> model('api/v1/user_membership_option_api_model');
        $this -> load -> model('api/v1/user_photo_api_model');
    }

    public function chats_get() {

        $last_chat_messages = $this -> user_chat_api_model -> get_last_chat_messages_by_user_id($this -> rest -> user_id);

        $chats = array();
        if (!empty($last_chat_messages)) {

            foreach ($last_chat_messages as $last_chat_message) {

                $friend = $this -> user_api_model -> get_user($last_chat_message['friend_id']);
                $friend_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($last_chat_message['friend_id']);
                $unread_count = $this -> user_chat_api_model -> get_unread_messages_count($this -> rest -> user_id, $last_chat_message['friend_id']);
                $last_date_with_friend = $this -> date_api_model -> get_last_date_between_users($this -> rest -> user_id, $last_chat_message['friend_id']);
                $is_friend_premium_user = $this -> user_membership_option_api_model -> is_upgraded_user($last_chat_message['friend_id']);

                unset($chat);
                $chat['unread_count'] = $unread_count;
                $chat['last_message'] = $last_chat_message['chat_message'];
                $chat['last_message_time'] = $last_chat_message['chat_message_time'];

                if (!empty($last_date_with_friend))
                    $chat['last_date_time'] = $last_date_with_friend -> date_time;

                $chat['friend']['attributes']['user_id'] = $friend -> user_id;
                $chat['friend']['attributes']['first_name'] = $friend -> first_name;
                $chat['friend']['attributes']['last_name'] = $friend -> last_name;
                $chat['friend']['relationships']['user_photos'] = $friend_photos;
                $chat['friend']['meta']['is_premium_member'] = $is_friend_premium_user;

                $chats[] = $chat;
            }
        }

        $this -> response(array(
            'data' => $chats
        ), 200);
    }

    public function chats_messages_get($user_id) {

        if (empty($user_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to get chat messages',
                        'detail' => 'User id is not provided.'
                    )
                )
            ), 200);
        }

        $user_chats = $this -> user_chat_api_model -> get_user_chats_by_user_id_friend_id($this -> rest -> user_id, $user_id);

        $friend = $this -> user_api_model -> get_user($user_id);

        $last_date_with_friend = $this -> date_api_model -> get_last_date_between_users($this -> rest -> user_id, $friend -> user_id);
        $last_date_date_type = $this -> date_type_api_model -> get_date_type($last_date_with_friend -> date_type_id);

        $first_date_with_friend = $this -> date_api_model -> get_first_date_between_users($this -> rest -> user_id, $friend -> user_id);
        $first_date_date_type = $this -> date_type_api_model -> get_date_type($first_date_with_friend -> date_type_id);
        $merchant = $this -> merchant_api_model -> get_merchant($first_date_with_friend -> merchant_id);

        $is_friend_premium_member = $this -> user_membership_option_api_model -> is_upgraded_user($friend -> user_id);

        // Mark messages read
        $this -> user_chat_api_model -> mark_user_chats_read_by_user_id_friend_id($this -> rest -> user_id, $friend -> user_id);

        $this -> response(array(
            'data' => $user_chats,
            'included' => array(
                'friend' => array(
                    'attributes' => $friend,
                    'meta' => array(
                        'is_premium_member' => $is_friend_premium_member
                    )
                ),
                'last_date' => array(
                    'attributes' => $last_date_with_friend,
                    'relationships' => array(
                        'date_type' => $last_date_date_type
                    )
                ),
                'first_date' => array(
                    'attributes' => $first_date_with_friend,
                    'relationships' => array(
                        'date_type' => $first_date_date_type,
                        'merchant' => $merchant,
                    )
                )
            )
        ), 200);
    }

    public function chats_messages_post($user_id) {

        if (empty($user_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to get send message',
                        'detail' => 'User id is not provided.'
                    )
                )
            ), 200);
        }

        $message = $this -> post('message');

        if (empty($message)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to send message',
                        'detail' => 'Message is not provided.'
                    )
                )
            ), 200);
        }

        // Get last message time
        $user_chats = $this -> user_chat_api_model -> get_user_chats_by_user_id_friend_id($this -> rest -> user_id, $user_id);
        $last_message_time = NULL;
        if (!empty($user_chats)) {

            $last_user_chat = end($user_chats);
            $last_message_time = $last_user_chat['chat_message_time'];
        }

        // Insert user chat
        $insert_user_chat_array['from_user_id'] = $this -> rest -> user_id;
        $insert_user_chat_array['to_user_id'] = $user_id;
        $insert_user_chat_array['chat_message'] = $message;
        $insert_user_chat_array['is_read'] = '0';
        $insert_user_chat_array['chat_message_time'] = SQL_DATETIME;

        $user_chat_id = $this -> user_chat_api_model -> insert_user_chat($insert_user_chat_array);
        $user_chat = $this -> user_chat_api_model -> get_user_chat($user_chat_id);

        // Send Push Notification
        $friend = $this -> user_api_model -> get_user($user_id);
        if ($friend -> want_pn_chat_message == 1) {     // Check friend's settings

            $user = $this -> user_api_model -> get_user($this -> rest -> user_id);
            $message = "You have received a new message from {$user -> first_name}";
            $meta = array(
                'notification_type' => 'new_message',
                'friend_id' => $user_id
            );
            $meta_response = $this -> user_api_model -> send_push_notification_to_user($user_id, $message, $meta);
        }

        // [TODO]
        // Send email
        /*
        // check past message time btwen  these two users
        $to_time = strtotime($last_message_time);
        $from_time = strtotime(date('Y-m-d H:i:s'));
        $diffMinutes = round(abs($to_time - $from_time) / 60, 2);
        // die();
        if ($diffMinutes > 30 || 1==1) {
            $this -> general -> set_table('user');
            $user_data = $this -> general -> get("user_id,first_name,last_name,num_date_tix,mobile_international_code,mobile_phone_number ,facebook_id,want_age_range_lower,want_age_range_upper,gender_id,ethnicity_id", array('user_id' => $currentid));
            $user = $user_data['0'];

            $this -> general -> set_table('user_email');
            $user_data_to = $this -> general -> get("", array('user_id' => $otherid));
            $user_to = $user_data_to['0'];

            //send mail to  receiver
            $data_applicant['email_content'] = 'You have received a new message from ' . $user['first_name'] . ' on ' .print_date_daytime(date('Y-m-d H:i:s'));
            $data_applicant['email_title'] = '';
            $return_url = base_url() . 'dates/chat_history/' . $this -> utility -> encode($currentid) . '/' . $this -> utility -> encode($otherid);

            //Dynamic autologin link
            $this -> general -> set_table('user');
            $user_data = $this -> general -> get("user_id,first_name,password", array('user_id' => $otherid));
            $user_to_profile = $user_data['0'];

            $user_link = $this -> utility -> encode($user_to_profile['user_id']);
            if ($user_to_profile['password']) {
                $user_link .= '/' . $user_to_profile['password'];
            }
            $data_applicant['btn_link'] = base_url() . 'home/mail_action/' . $user_link . '?return_to='.$return_url;

            $data_applicant['btn_text'] = 'View Message';
            $subject = 'You have received a new message from ' . $user['first_name'];
            $email_template = $this -> load -> view('email/common', $data_applicant, true);
            //echo $email_template;exit;
            $this -> datetix -> mail_to_user($user_to['email_address'], $subject, $email_template);
        }*/

        $this -> response(array(
            'data' => $user_chat,
            'meta' => !empty($meta_response) ? $meta_response : NULL
        ), 200);
    }
}
