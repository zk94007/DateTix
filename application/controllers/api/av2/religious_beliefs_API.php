<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Religious_Beliefs_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/av2/religious_belief_api_model');
    }

    public function index_get() {

        $religious_beliefs = $this -> religious_belief_api_model -> get_religious_beliefs($this -> rest ->language_id);

        $this -> response(array(
            'data' => $religious_beliefs
        ), 200);
    }
}