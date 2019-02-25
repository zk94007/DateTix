<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Date_Payers_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/v1/date_payer_api_model');
    }

    public function index_get() {

        $date_payers = $this -> date_payer_api_model -> get_date_payers($this -> rest ->language_id);

        $this -> response(array(
            'data' => $date_payers
        ), 200);
    }

}