<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Descriptive_Words_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/v1/descriptive_word_api_model');
    }

    public function index_get() {

        $descriptive_words = $this -> descriptive_word_api_model -> get_descriptive_words($this -> rest ->language_id);

        $this -> response(array(
            'data' => $descriptive_words
        ), 200);
    }
}