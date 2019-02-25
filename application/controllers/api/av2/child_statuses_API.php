<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Child_Statuses_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/av2/child_status_api_model');
    }

    public function index_get() {

        $child_statuses = $this -> child_status_api_model -> get_child_statuses($this -> rest ->language_id);

        $this -> response(array(
            'data' => $child_statuses
        ), 200);
    }
}