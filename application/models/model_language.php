<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_language extends CI_Model {

    public function languages($limit = '') {
        if ($limit) {
            $this->db->limit($limit);
        }

        $query = $this->db->get('display_language');
        foreach ($query->result() as $row) {
            $data[] = $row;
        }
        if (!empty($data)) {
            return $data;
        }
    }

    public function insert() {
        $all_languages = get_all_languages();
        $language_code = $this->input->post('language_code');
        $language = $all_languages[$language_code];
        $insert_array = array("language_code" => $language_code,
            "description" => $language);
        $this->db->insert('display_language', $insert_array);
        $language_id = $this->db->insert_id();
        $this->load->model('model_translation');
        $this->model_translation->google_translate_all($language_id, $language_code);
        return $language_id;
    }

    public function update($language_id) {
        return;
    }

    public function get_language_code($language_id) {
        $this->db->where(array("display_language_id" => $language_id));
        $query = $this->db->get('display_language');
        foreach ($query->result() as $row) {
            $data[] = $row;
        }
        if (!empty($data)) {
            return $data[0]->language_code;
        }
    }

    public function get_all_languages() {
        $query = $this->db->get('display_language');
        foreach ($query->result() as $row) {
            $data[] = $row;
        }
        if (!empty($data)) {
            return $data;
        }
    }

    public function get_language($language_id) {
        $this->db->where(array("display_language_id" => $language_id));
        $query = $this->db->get('display_language');
        foreach ($query->result() as $row) {
            $data[] = $row;
        }
        if (!empty($data)) {
            return $data[0]->display_language_id;
        }
    }

    public function get_language_details($language_id) {
        $this->db->where(array("display_language_id" => $language_id));
        $query = $this->db->get('display_language');
        foreach ($query->result() as $row) {
            $data[] = $row;
        }
        if (!empty($data)) {
            return $data;
        }
    }

    public function make_default($language_id) {
        $update_array = array("default" => 0);
        $this->db->update("display_language", $update_array);

        $update_array = array("default" => 1);
        $update_cond = array("display_language_id" => $language_id);
        $this->db->update("display_language", $update_array, $update_cond);
        return;
    }

    public function update_flag($image_data, $language_id) {
        $image_data = $image_data["upload_data"];
        $update_array = array("flag" => $image_data["file_name"]);
        $update_cond = array("display_language_id" => $language_id);
        $this->db->update("display_language", $update_array, $update_cond);
    }

    public function get_existing_languages($code = '') {
        $language_array = array();
        $language_array = $this->get_all_languages();
        if (!$code)
            return $language_array;
        else {
            foreach ($language_array as $row) {
                $language_array[] = $row->language_code;
            }
            return $language_array;
        }
    }

    /**
     * Retrive languages set for country
     * @param  int $country_id
     * @return mixed
     */
    public function get_lanuages_by_country($country_id)
    {
        $this->db->order_by('l.view_order', 'asc');
	//$this->db->order_by('l.view_order', 'asc');
        $this->db->where('cl.country_id', $country_id);
        $this->db->join('country_display_language cl', 'cl.display_language_id = l.display_language_id', 'left');
        $this->db->select('l.*');
        $q = $this->db->get('display_language l', 3);
        return ($q->num_rows() > 0) ? $q->result() : array() ;
    }
}
