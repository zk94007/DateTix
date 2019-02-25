<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Cities_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/v1/city_api_model');
    }

    public function index_get() {

        $cities = $this -> city_api_model -> get_cities('*', $this -> rest ->language_id);

        $this -> response(array(
            'data' => $cities
        ), 200);
    }

}