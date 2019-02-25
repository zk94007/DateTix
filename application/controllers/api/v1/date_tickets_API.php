<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Date_Tickets_API extends MY_REST_Controller {

    function __construct() {
        parent::__construct();

        $this -> load -> model('api/v1/user_api_model');
        $this -> load -> model('api/v1/user_order_api_model');
    }

    public function purchase_post() {

        $order_currency_id = $this -> post('order_currency_id');
        $order_amount = $this -> post('order_amount');
        $order_num_date_tix = $this -> post('order_num_date_tix');

        $errors = array();

        if (empty($order_currency_id)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to process purchase',
                'detail' => 'Order currency id is not provided.'
            );
        }
        if (empty($order_amount)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to process purchase',
                'detail' => 'Order amount is not provided.'
            );
        }
        if (empty($order_num_date_tix)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to process purchase',
                'detail' => 'Number of date tickets is not provided.'
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
        $insert_user_order_array['order_num_date_tix'] = $order_num_date_tix;

        $user_order_id = $this -> user_order_api_model -> insert_user_order($insert_user_order_array);
        $user_order = $this -> user_order_api_model -> get_user_order($user_order_id);

        // Update user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);
        $update_user_array['num_date_tix'] = $user -> num_date_tix + $order_num_date_tix;

        $this -> user_api_model -> update_user($this -> rest -> user_id, $update_user_array);

        // Get updated user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        // [TODO]
        // Send email
        /*
        $upgrade_subject = translate_phrase("Date package purchase");
        $email_template = $name.' ,thanks for purchasing a '.$post_data['no_of_unit'].' date package';
        $this -> model_user -> send_email(INFO_EMAIL,$user_mail, $upgrade_subject, $email_template,"html","DateTix");
        */

        $this -> response(array(
            'data' => $user_order,
            'included' => array(
                'user' => array(
                    'attributes' => $user
                )
            )
        ), 200);
    }

    public function add_post() {

        $num_date_tix = $this -> post('num_date_tix');

        if (empty($num_date_tix)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to add date tockets',
                        'detail' => 'num_date_tix is not provided.'
                    )
                )
            ), 200);
        }

        // Update user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);
        $update_user_array['num_date_tix'] = $user -> num_date_tix + $num_date_tix;

        $this -> user_api_model -> update_user($this -> rest -> user_id, $update_user_array);

        // Get updated user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user
            )
        ), 200);
    }

    public function use_post() {

        $num_date_tickets = $this -> post('num_date_tickets');

        if (empty($num_date_tickets)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to use date tickets.',
                        'detail' => 'Number of date tickets is not provided.'
                    )
                )
            ), 200);
        }

        // Get user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        if ($num_date_tickets > $user -> num_date_tix) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to use date tickets.',
                        'detail' => 'You don\'t have enough date tickets.'
                    )
                )
            ), 200);
        }

        // Update user data
        $update_user_array['num_date_tix'] = $user -> num_date_tix - $num_date_tickets;

        $this -> user_api_model -> update_user($this -> rest -> user_id, $update_user_array);

        // Get updated user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user
            )
        ), 200);
    }
}
