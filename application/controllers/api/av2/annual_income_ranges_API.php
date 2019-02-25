<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Annual_Income_Ranges_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/av2/annual_income_range_api_model');
    }

    public function index_get() {

        $country_id = $this -> get('country_id');
        if (!$country_id) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to retrieve data',
                        'detail' => 'GET parameter "country_id" is not given.'
                    )
                )
            ), 200);
        }

        $annual_income_ranges = $this -> annual_income_range_api_model -> get_annual_income_ranges_by_country_id($country_id);

        $this -> response(array(
            'data' => $annual_income_ranges
        ), 200);
    }
}