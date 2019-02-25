<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Relationship_Statuses_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/v1/relationship_status_api_model');
    }

    public function index_get() {

        $relationship_statuses = $this -> relationship_status_api_model -> get_relationship_statuses($this -> rest ->language_id);

        $this -> response(array(
            'data' => $relationship_statuses
        ), 200);
    }
}