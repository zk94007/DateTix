<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Smoking_Statuses_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/v1/smoking_status_api_model');
    }

    public function index_get() {

        $smoking_statuses = $this -> smoking_status_api_model -> get_smoking_statuses($this -> rest ->language_id);

        $this -> response(array(
            'data' => $smoking_statuses
        ), 200);
    }
}