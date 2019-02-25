<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Cuisines_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this -> load -> model('api/av2/cuisine_api_model');
        $this -> load -> model('api/av2/cuisine_category_api_model');
    }

    public function index_get() {

        $cuisines = $this -> cuisine_api_model -> get_cuisines($this -> rest ->language_id);
        $cuisine_categories = $this -> cuisine_category_api_model -> get_cuisine_categories($this -> rest -> language_id);

        $this -> response(array(
            'data' => $cuisines,
            'included' => array(
                'cuisine_categories' => $cuisine_categories
            )
        ), 200);
    }
}