<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Relationship_Types_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/v1/relationship_type_api_model');
    }

    public function index_get() {

        $relationship_types = $this -> relationship_type_api_model -> get_relationship_types($this -> rest ->language_id);

        $this -> response(array(
            'data' => $relationship_types
        ), 200);
    }

}