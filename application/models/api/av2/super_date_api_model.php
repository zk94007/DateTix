<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Super_Date_api_model extends CI_Model {

    public function insert_super_date($insert_array) {

        $this -> db -> insert('super_date', $insert_array);
        return $this -> db -> insert_id();
    }

    public function get_super_date($super_date_id) {

        $this -> db -> where('super_date_id', $super_date_id);
        $result = $this -> db -> get('super_date');

        return $result -> num_rows() > 0 ? $result -> row() : NULL;
    }
}
