<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Neighborhoods_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/av2/neighborhood_api_model');
    }

    public function index_get() {

        $city_id = $this -> get('city_id');

        if (!empty($city_id) && $city_id) {
            $neighborhoods = $this -> neighborhood_api_model -> filter_neighborhoods($city_id);
        } else {
            $neighborhoods = $this -> neighborhood_api_model -> get_neighborhoods();
        }

        $this -> response(array(
            'data' => $neighborhoods
        ), 200);
    }
}