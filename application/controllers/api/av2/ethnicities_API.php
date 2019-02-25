<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Ethnicities_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/av2/ethnicity_api_model');
    }

    public function index_get() {

        $ethnicities = $this -> ethnicity_api_model -> get_ethnicities($this -> rest ->language_id);

        $this -> response(array(
            'data' => $ethnicities
        ), 200);
    }

}