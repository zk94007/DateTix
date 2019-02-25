<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Display_Language_api_model extends CI_Model {

    public function get_display_language($display_language_id) {

        $this -> db -> where('display_language_id', $display_language_id);
        $result = $this -> db -> get('display_language');

        return $result -> num_rows() > 0 ? $result -> row() : NULL;
    }

    public function get_display_languages() {

        $this -> db -> where_in('display_language_id', array(1, 2, 3));
        $result = $this -> db -> get('display_language');

        return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
    }
}
