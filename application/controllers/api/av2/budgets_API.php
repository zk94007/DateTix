<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Budgets_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/av2/budget_api_model');
    }

    public function index_get() {

        $budgets = $this -> budget_api_model -> get_budgets($this -> rest ->language_id);

        $this -> response(array(
            'data' => $budgets
        ), 200);
    }
}