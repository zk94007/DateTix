<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Genders_API extends  MY_REST_Controller {

    function __construct() {
        parent::__construct();

        $this -> load -> model('api/av2/gender_api_model');
    }

    public function index_get() {

        $genders = $this -> gender_api_model -> get_genders($this -> rest ->language_id);

        $this -> response(array(
            'data' => $genders
        ), 200);
    }

}