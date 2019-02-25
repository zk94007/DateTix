<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Exercise_Frequencies_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this->load->model('api/v1/exercise_frequency_api_model');
    }

    public function index_get() {

        $exercise_frequencies = $this -> exercise_frequency_api_model -> get_exercise_frequencies($this -> rest ->language_id);

        $this -> response(array(
            'data' => $exercise_frequencies
        ), 200);
    }
}