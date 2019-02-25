<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Schools_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this -> load -> model('api/v1/school_api_model');
    }

    public function index_get() {

        $schools = $this -> school_api_model -> get_schools($this -> rest ->language_id);

        $this -> response(array(
            'data' => $schools
        ), 200);
    }
}