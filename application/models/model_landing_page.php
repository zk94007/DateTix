<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_landing_page extends CI_Model {

    /**
     * Retrieve a langing page by id
     * @param  int $id landing_page_id
     * @return array|NULL landing_page detials
     */
    public function geLandingPage($id, $language_id)
    {
        $this->db->where('landing_page_id', $id);
        $this->db->where('display_language_id', $language_id);
        $result = $this->db->get('landing_page');
        return ($result->num_rows() > 0) ? $result->row() : NULL;
    }

    public function getLandingPageMessage($landing_page_id, $language_id)
    {
        $this->db->where('landing_page_id', $landing_page_id);
        $this->db->where('display_language_id', $language_id);
        $result = $this->db->get('landing_page_main_message');
        return ($result->num_rows() > 0) ? $result->row() : NULL;
    }
}

/* End of file model_landing_page.php */
/* Location: ./application/models/model_landing_page.php */
