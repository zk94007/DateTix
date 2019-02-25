<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class School_Subjects_API extends  MY_REST_Controller {

    function __construct()
    {
        parent::__construct();

        $this -> load -> model('api/v1/school_subject_api_model');
    }

    public function index_get() {

        $school_subjects = $this -> school_subject_api_model -> get_school_subjects($this -> rest ->language_id);

        $this -> response(array(
            'data' => $school_subjects
        ), 200);
    }
}