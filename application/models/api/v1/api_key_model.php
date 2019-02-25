<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class API_Key_model extends CI_Model {

    public function insert_api_key($api_key, $data) {

        $data['api_key'] = $api_key;
        $data['date_created'] = function_exists('now') ? now() : time();

        return $this -> db -> set($data) -> insert(config_item('rest_keys_table'));
    }

    public function update_api_key_for_user_id($user_id, $api_key) {

        return $this -> db -> where('user_id', $user_id) -> update(config_item('rest_keys_table'), array('api_key' => $api_key));
    }

    public function delete_api_key($api_key) {

        return $this -> db -> where('api_key', $api_key) -> delete(config_item('rest_keys_table'));
    }

    public function delete_api_key_for_user_id($user_id) {

        return $this -> db -> where ('user_id', $user_id) -> delete(config_item('rest_keys_table'));
    }

    public function api_key_exists_for_user_id($user_id) {

        return $this -> db -> where('user_id', $user_id) -> count_all_results(config_item('rest_keys_table')) > 0;
    }

    public function api_key_exists($api_key) {

        return $this -> db -> where('api_key', $api_key) -> count_all_results(config_item('rest_keys_table')) > 0;
    }

    public function generate_api_key() {

        do {
            $salt = hash('md5', time().mt_rand());
            $new_api_key = substr($salt, 0, config_item('rest_key_length'));
        }
            // Already in the DB? Fail. Try again
        while ($this -> api_key_exists($new_api_key));

        return $new_api_key;
    }

}
