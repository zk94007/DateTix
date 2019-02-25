<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Drinking_Statuses_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/av2/drinking_status_api_model');
    }

    public function index_get() {

        $drinking_statuses = $this -> drinking_status_api_model -> get_drinking_statuses($this -> rest ->language_id);

        $this -> response(array(
            'data' => $drinking_statuses
        ), 200);
    }
}