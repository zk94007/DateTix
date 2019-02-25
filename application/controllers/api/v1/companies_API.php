<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Companies_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this -> load -> model('api/v1/company_api_model');
    }

    public function index_get() {

        $companies = $this -> company_api_model -> get_companies($this -> rest ->language_id);

        $this -> response(array(
            'data' => $companies
        ), 200);
    }
}