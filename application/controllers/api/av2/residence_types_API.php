<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Residence_Types_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/av2/residence_type_api_model');
    }

    public function index_get() {

        $residence_types = $this -> residence_type_api_model -> get_residence_types($this -> rest ->language_id);

        $this -> response(array(
            'data' => $residence_types
        ), 200);
    }
}