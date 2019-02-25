<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Test_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this -> load -> model('api/v1/v1/date_api_model');
        $this -> load -> model('api/v1/v1/user_api_model');
        $this -> load -> model('api/v1/v1/date_type_api_model');
        $this -> load -> model('api/v1/v1/merchant_api_model');
    }

    public function index_get() {

    }
}