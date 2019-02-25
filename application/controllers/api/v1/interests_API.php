<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Interests_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/v1/interest_api_model');
    }

    public function index_get() {

        $interest_category_id = $this -> get('interest_category_id');

        $interests = $this -> interest_api_model -> get_interests($this -> rest ->language_id, $interest_category_id);

        $this -> response(array(
            'data' => $interests
        ), 200);
    }
}