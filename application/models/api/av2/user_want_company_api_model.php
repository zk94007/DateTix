<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Want_Company_api_model extends CI_Model {

    public function get_user_want_company($user_want_company_id) {

        $this -> db -> where('user_want_company_id', $user_want_company_id);
        $result = $this -> db -> get('user_want_company');
        return $result -> num_rows() > 0 ? $result -> row() : NULL;
    }

    public function insert_user_want_company($insert_array) {

        $this -> db -> insert('user_want_company', $insert_array);
        return $this -> db -> insert_id();
    }

    public function delete_user_want_company($user_want_company_id) {

        $this -> db -> where('user_want_company_id', $user_want_company_id);
        $this -> db -> delete('user_want_company');
    }

    public function get_user_want_companies_by_user_id($user_id) {
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db ->get('user_want_company');

        $results = array();
        if ($result -> num_rows() > 0) {
            $results = $result -> result_array();
        }
        return $results;
    }

    public function get_company_ids_by_user_id($user_id) {

        $this -> db -> select('company_id');
        $this -> db -> where('user_id', $user_id);
        $result = $this -> db -> get('user_want_company');

        if ($result -> num_rows() > 0) {

            $result_array = $result -> result_array();

            $company_ids = array();
            foreach ($result_array as $row) {
                $company_ids[] = $row['company_id'];
            }

            return $company_ids;
        }

        return NULL;
    }

}
