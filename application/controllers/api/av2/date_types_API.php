<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Date_Types_API extends  MY_REST_Controller {

    function __construct() {
        parent::__construct();

        $this -> load -> model('api/av2/date_type_api_model');
    }

    public function index_get() {

        $date_types = $this -> date_type_api_model -> get_date_types($this -> rest ->language_id);

        $this -> response(array(
            'data' => $date_types
        ), 200);
    }

}