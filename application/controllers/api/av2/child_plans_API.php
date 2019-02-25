<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Child_Plans_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/av2/child_plan_api_model');
    }

    public function index_get() {

        $child_plans = $this -> child_plan_api_model -> get_child_plans($this -> rest ->language_id);

        $this -> response(array(
            'data' => $child_plans
        ), 200);
    }
}