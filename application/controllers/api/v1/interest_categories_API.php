<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Interest_Categories_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/v1/interest_category_api_model');
    }

    public function index_get() {

        $interest_categories = $this -> interest_category_api_model -> get_interest_categories($this -> rest ->language_id);

        $this -> response(array(
            'data' => $interest_categories
        ), 200);
    }
}