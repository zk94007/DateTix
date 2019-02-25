<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class My_Profile_API extends  MY_REST_Controller {

    function __construct() {
        parent::__construct();

        $this -> load -> model('api/v1/annual_income_range_api_model');
        $this -> load -> model('api/v1/body_type_api_model');
        $this -> load -> model('api/v1/child_plan_api_model');
        $this -> load -> model('api/v1/child_status_api_model');
        $this -> load -> model('api/v1/city_api_model');
        $this -> load -> model('api/v1/country_api_model');
        $this -> load -> model('api/v1/descriptive_word_api_model');
        $this -> load -> model('api/v1/drinking_status_api_model');
        $this -> load -> model('api/v1/education_level_api_model');
        $this -> load -> model('api/v1/ethnicity_api_model');
        $this -> load -> model('api/v1/exercise_frequency_api_model');
        $this -> load -> model('api/v1/gender_api_model');
        $this -> load -> model('api/v1/interest_api_model');
        $this -> load -> model('api/v1/interest_category_api_model');
        $this -> load -> model('api/v1/relationship_status_api_model');
        $this -> load -> model('api/v1/religious_belief_api_model');
        $this -> load -> model('api/v1/residence_type_api_model');
        $this -> load -> model('api/v1/smoking_status_api_model');
        $this -> load -> model('api/v1/spoken_language_api_model');
        $this -> load -> model('api/v1/spoken_language_level_api_model');
        $this -> load -> model('api/v1/user_api_model');
        $this -> load -> model('api/v1/user_descriptive_word_api_model');
        $this -> load -> model('api/v1/user_education_level_api_model');
        $this -> load -> model('api/v1/user_interest_api_model');
        $this -> load -> model('api/v1/user_job_api_model');
        $this -> load -> model('api/v1/user_photo_api_model');
        $this -> load -> model('api/v1/user_school_api_model');
        $this -> load -> model('api/v1/user_spoken_language_api_model');
    }
	
    public function basics_get() {

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $genders = $this -> gender_api_model -> get_genders($this -> rest ->language_id);
        $ethnicities = $this -> ethnicity_api_model -> get_ethnicities($this -> rest ->language_id);
        $cities = $this -> city_api_model -> get_cities(array('city_id', 'province_id', 'description'), $this -> rest ->language_id);
        $body_types = $this -> body_type_api_model -> get_body_types($this -> rest ->language_id);
        $relationship_statuses = $this -> relationship_status_api_model -> get_relationship_statuses($this -> rest ->language_id);
        $religious_beliefs = $this -> religious_belief_api_model -> get_religious_beliefs($this -> rest ->language_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user
            ),
            'included' => array(
                'genders' => $genders,
                'ethnicities' => $ethnicities,
                'cities' => $cities,
                'body_types' => $body_types,
                'relationship_statuses' => $relationship_statuses,
                'religious_beliefs' => $religious_beliefs
            )
        ), 200);
    }

    public function basics_post() {

        // Populate parameters
        $gender_id = $this -> post('gender_id');
        $ethnicity_id = $this -> post('ethnicity_id');
        $current_city_id = $this -> post('current_city_id');
        $mobile_phone_number = $this -> post('mobile_phone_number');
        $height_in_cm = $this -> post('height_in_cm');
        $body_type_id = $this -> post('body_type_id');
        $relationship_status_id = $this -> post('relationship_status_id');
        $religious_belief_id = $this -> post('religious_belief_id');
        $self_summary = $this -> post('self_summary');
        $birth_date_str = $this -> post('birth_date');

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        // Build update array
        if (!empty($gender_id))
            $update_array['gender_id'] = $gender_id;

        if (!empty($ethnicity_id))
            $update_array['ethnicity_id'] = $ethnicity_id;

        if (!empty($current_city_id))
            $update_array['current_city_id'] = $current_city_id;

        if (!empty($mobile_phone_number))
            $update_array['mobile_phone_number'] = $mobile_phone_number;

        if (!empty($height_in_cm))
            $update_array['height'] = $height_in_cm;

        if (!empty($body_type_id))
            $update_array['body_type_id'] = $body_type_id;

        if (!empty($relationship_status_id))
            $update_array['relationship_status_id'] = $relationship_status_id;

        if (!empty($religious_belief_id))
            $update_array['religious_belief_id'] = $religious_belief_id;

        if (!empty($self_summary))
            $update_array['self_summary'] = $self_summary;

        if (!empty($birth_date_str)) {

            // Check date validation
            $birth_date = DateTime::createFromFormat('Y-m-d', $birth_date_str);
            $date_errors = DateTime::getLastErrors();
            if ($date_errors['warning_count'] + $date_errors['error_count'] > 0) {

                $this -> response(array(
                    'errors' => array(
                        array(
                            'id' => 'Currently not supported',
                            'code' => 'Currently not supported',
                            'title' => 'Failed to update basics information.',
                            'detail' => 'Birth date is invalid.'
                        )
                    )
                ), 200);
            }

            $update_array['birth_date'] = $birth_date_str;

            // Age related attributes
            $interval = $birth_date -> diff(new DateTime('now'));
            $age = $interval -> y;

            $user_gender_id = $user -> gender_id;
            if (!empty($gender_id)) $user_gender_id = $gender_id;

            if ($user_gender_id == '1') {   // Male

                $update_array['want_age_range_lower'] = $age / 2 + 3;
                $update_array['want_age_range_upper'] = $age + 2;

            } else if ($user_gender_id == '2') {    // Female

                $update_array['want_age_range_lower'] = $age - 2;
                $update_array['want_age_range_upper'] = ($age - 3) * 2;
            }
        }

        if (empty($update_array)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to update basics information.',
                        'detail' => 'No update information is provided.'
                    )
                )
            ), 200);
        }

        // Update user data
        $this -> user_api_model -> update_user($this -> rest -> user_id, $update_array);

        // Get updated user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user
            )
        ), 200);
    }

    public function photos_get() {

        $photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($this -> rest -> user_id);

        $this -> response(array(
            'data' => $photos
        ), 200);
    }

    public function photos_post() {

        // Create folder
        $folder_path = $this -> user_photo_api_model -> create_user_photos_folder($this -> rest -> user_id);

        // Re-array files
        if ($_FILES['user_photos']) {

            $file_array = $this -> rearray_files($_FILES['user_photos']);
            $_FILES = $file_array;

            if (count($_FILES) > 0) {

                $this -> load -> library('upload');

                foreach ($_FILES as $key => $file) {

                    // File extension
                    $file_extension = substr($file['name'], strrpos($file['name'], '.') + 1);

                    $config['upload_path'] = $folder_path;
                    $config['file_name'] = strtotime(SQL_DATETIME) . "_{$key}_profile_pic.$file_extension";
                    $config['allowed_types'] = '*';
                    $config['overwrite'] = TRUE;

                    $this -> upload -> initialize($config);

                    if ($this -> upload -> do_upload($key)) {

                        // Insert user photo
                        $this -> user_photo_api_model -> insert_user_photo_with_upload_data($this -> rest -> user_id, $this -> upload -> data());
                    }
                }
            }
        }

        // Get all user photos
        $user_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($this -> rest -> user_id);

        $this -> response(array(
            'data' => $user_photos
        ), 200);
    }

    public function photos_json_post() {

        $json = $this -> post('json');

        if (empty($json)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to insert user photos',
                        'detail' => 'JSON parameter is empty.'
                    )
                )
            ), 200);
        }

        $user_photo_urls = json_decode($json);

        if (empty($user_photo_urls)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to insert user photos',
                        'detail' => 'JSON parameter is invalid.'
                    )
                )
            ), 200);
        }

        $user_photos = array();

        // Create folder
        $folder_path = $this->user_photo_api_model->create_user_photos_folder($this -> rest -> user_id);

        foreach ($user_photo_urls as $key => $user_photo_url) {

            if (!empty($user_photo_url)) {

                // Download user photo from url
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $user_photo_url);
                curl_setopt($ch, CURLOPT_NOBODY, 1);
                curl_setopt($ch, CURLOPT_FAILONERROR, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                if (curl_exec($ch) !== FALSE) {
                    $file_extension = 'png'; //substr($user_photo_url, strrpos($user_photo_url, '.') + 1);
                    $file_name = strtotime(SQL_DATETIME) . "_{$key}_profile_pic.$file_extension";

                    $success = copy($user_photo_url, "$folder_path/$file_name");

                    if ($success == TRUE) {

                        // Insert user photo
                        $insert_user_photo_array['user_id'] = $this -> rest -> user_id;
                        if (count($user_photos) == 0) { // If first insert
                            $insert_user_photo_array['set_primary'] = 1;
                        } else {
                            $insert_user_photo_array['set_primary'] = 0;
                        }
                        $insert_user_photo_array['photo'] = $file_name;
                        $insert_user_photo_array['uploaded_time'] = SQL_DATETIME;

                        $user_photo_id = $this->user_photo_api_model->insert_user_photo($insert_user_photo_array);

                        // Get created user photo
                        $user_photo = $this->user_photo_api_model->get_user_photo($user_photo_id);

                        $user_photos[] = $user_photo;
                    }
                }
                curl_close($ch);
            }
        }

        $this -> response(array(
            'data' => $user_photos
        ), 200);
    }

    private function rearray_files(&$file_post) {

        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for ($i=0; $i<$file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }

        return $file_ary;
    }

    public function photos_delete($user_photo_id) {

        if (!$user_photo_id) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to delete photo',
                        'detail' => 'user_photo_id parameter is not given.'
                    )
                )
            ), 200);
        }

        $result = $this -> user_photo_api_model -> delete_user_photo($user_photo_id, $this -> rest -> user_id);

        if ($result == '0') {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to delete photo',
                        'detail' => 'User photo id does not exist.'
                    )
                )
            ), 200);
        }

        $this -> response(array(
            'meta' => array(
                'status' => TRUE,
                'detail' => 'Photo is removed successfully.'
            )
        ), 200);
    }

    public function set_primary_photo_post($user_photo_id) {

        if (!$user_photo_id) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to set primary photo',
                        'detail' => 'user_photo_id parameter is not given.'
                    )
                )
            ), 200);
        }

        $has_user_photo_id = $this -> user_photo_api_model -> has_user_photo_id($this -> rest -> user_id, $user_photo_id);
        if ($has_user_photo_id == FALSE) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to set primary photo',
                        'detail' => 'user_photo_id parameter is invalid.'
                    )
                )
            ), 200);
        }

        $this -> user_photo_api_model -> set_primary_photo_with_params($this -> rest -> user_id, $user_photo_id);

        // Get user photos
        $user_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($this -> rest -> user_id);

        $this -> response(array(
            'data' => $user_photos
        ), 200);
    }

    public function education_get() {

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $education_levels = $this -> education_level_api_model -> get_education_levels($this -> rest ->language_id);
        $user_education_levels = $this -> user_education_level_api_model -> get_user_education_levels_by_user_id($this -> rest ->user_id, $this -> rest ->language_id);
        $user_schools = $this -> user_school_api_model -> get_user_schools_by_user_id($this -> rest -> user_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(
                    'education_levels' => $user_education_levels,
                    'schools' => $user_schools
                )
            ),
            'included' => array(
                'education_levels' => $education_levels
            )
        ), 200);
    }

    public function education_post() {

        $education_level_ids = $this -> post('education_level_ids');

        if (empty($education_level_ids)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to update education information',
                        'detail' => 'No update information is provided.'
                    )
                )
            ), 200);
        }

        // Remove existing user education levels
        $this -> user_education_level_api_model -> delete_user_education_levels_by_user_id($this -> rest -> user_id);

        // Insert new education levels
        foreach ($education_level_ids as $education_level_id) {

            $this -> user_education_level_api_model -> insert_user_education_level($this -> rest -> user_id, $education_level_id);
        }

        // Get updated user education levels
        $user_education_levels = $this -> user_education_level_api_model -> get_user_education_levels_by_user_id($this -> rest ->user_id, $this -> rest ->language_id);

        $this -> response(array(
            'data' => $user_education_levels
        ), 200);
    }

    public function education_schools_post() {

        $school_name = $this -> post('school_name');
        $degree_name = $this -> post('degree_name');
        $completed = $this -> post('completed');
        if (!$completed) $completed = '0';
        $years_attended_start = $this -> post('years_attended_start');
        $years_attended_end = $this -> post('years_attended_end');

        $errors = array();
        if (empty($school_name)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to add school',
                'detail' => 'School name is empty.'
            );
        }
        if ($completed != '0' && $completed != '1') {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to add school',
                'detail' => 'Completed parameter value is invalid.'
            );
        }
        if (count($errors) > 0) {

            $this -> response(array(
                'errors' => $errors
            ), 200);
        }

        $insert_array['user_id'] = $this -> rest -> user_id;
        $insert_array['school_name'] = $school_name;
        $insert_array['degree_name'] = $degree_name;
        $insert_array['is_degree_completed'] = $completed;
        $insert_array['years_attended_start'] = $years_attended_start;
        $insert_array['years_attended_end'] = $years_attended_end;

        $user_school_id = $this -> user_school_api_model -> insert_user_school($insert_array);

        $user_school = $this -> user_school_api_model -> get_user_school_details($user_school_id, $this -> rest ->language_id);

        $this -> response(array(
            'data' => $user_school
        ), 200);
    }

    public function education_schools_json_post() {

        $json = $this -> post('json');

        if (empty($json)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to insert user schools',
                        'detail' => 'JSON parameter is empty.'
                    )
                )
            ), 200);
        }

        $objects = json_decode($json);

        if (empty($objects)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to insert user schools',
                        'detail' => 'JSON parameter is invalid.'
                    )
                )
            ), 200);
        }

        $user_schools = array();

        foreach ($objects as $object) {

            $school_name = $object -> school_name;
            $degree_name = $object -> degree_name;
            $completed = $object -> completed;
            if (!$completed) $completed = '0';
            $years_attended_start = $object -> years_attended_start;
            $years_attended_end = $object -> years_attended_end;

            if (!empty($school_name) && ($completed == '0' || $completed == '1')) {

                $insert_array['user_id'] = $this -> rest -> user_id;
                $insert_array['school_name'] = $school_name;
                $insert_array['degree_name'] = $degree_name;
                $insert_array['is_degree_completed'] = $completed;
                $insert_array['years_attended_start'] = $years_attended_start;
                $insert_array['years_attended_end'] = $years_attended_end;

                $user_school_id = $this -> user_school_api_model -> insert_user_school($insert_array);

                $user_school = $this -> user_school_api_model -> get_user_school($user_school_id);

                $user_schools[] = $user_school;
            }
        }

        $this -> response(array(
            'data' => $user_schools
        ), 200);
    }

    public function education_schools_put($user_school_id) {

        $school_name = $this -> put('school_name');
        $degree_name = $this -> put('degree_name');
        $completed = $this -> put('completed');
        if (empty($completed)) $completed = '0';
        $years_attended_start = $this -> put('years_attended_start');
        $years_attended_end = $this -> put('years_attended_end');

        $errors = array();
        if (empty($user_school_id)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to add school',
                'detail' => 'User school id is not given.'
            );
        }
        if (empty($school_name)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to add school',
                'detail' => 'School name is empty.'
            );
        }
        if ($completed != '0' && $completed != '1') {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to add school',
                'detail' => 'The value of \'completed\' parameter is invalid.'
            );
        }

        if (count($errors) > 0) {

            $this -> response(array(
                'errors' => $errors
            ), 200);
        }

        $update_array['school_name'] = $school_name;
        $update_array['degree_name'] = $degree_name;
        $update_array['is_degree_completed'] = $completed;
        $update_array['years_attended_start'] = $years_attended_start;
        $update_array['years_attended_end'] = $years_attended_end;

        $this -> user_school_api_model -> update_user_school($user_school_id, $update_array);

        $user_school = $this -> user_school_api_model -> get_user_school_details($user_school_id, $this -> rest ->language_id);

        $this -> response(array(
            'data' => $user_school
        ), 200);
    }

    public function education_schools_delete($user_school_id) {

        if (empty($user_school_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to delete school',
                        'detail' => 'User school id is not given.'
                    )
                )
            ), 200);
        }

        $this -> user_school_api_model -> delete_user_school_and_links($user_school_id);

        $this -> response(array(
            'meta' => array(
                'status' => TRUE,
                'detail' => 'User school is removed successfully.'
            )
        ), 200);
    }

    public function career_get() {

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        // Get country id
        $current_country = $this -> country_api_model -> get_country_by_city_id($user -> current_city_id);
        $country_id = $current_country ? $current_country -> country_id : '0';

        $annual_income_ranges = $this -> annual_income_range_api_model -> get_annual_income_ranges_by_country_id($country_id);
        $user_jobs = $this -> user_job_api_model -> get_user_jobs_by_user_id($this -> rest -> user_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(
                    'user_jobs' => $user_jobs
                )
            ),
            'included' => array(
                'annual_income_ranges' => $annual_income_ranges
            )
        ), 200);
    }

    public function career_post() {

        $annual_income_range_id = $this -> post('annual_income_range_id');

        if (empty($annual_income_range_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to update career information',
                        'detail' => 'No update information is provided.'
                    )
                )
            ), 200);
        }

        $update_array['annual_income_range_id'] = $annual_income_range_id;

        // Update user data
        $this -> user_api_model -> update_user($this -> rest -> user_id, $update_array);

        // Get updated user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user
            )
        ), 200);
    }

    public function career_jobs_post() {

        $company_name = $this -> post('company_name');
        $show_company_name = $this -> post('show_company_name');
        if (empty($show_company_name)) $show_company_name = '0';
        $job_title = $this -> post('job_title');
        $years_worked_start = $this -> post('years_worked_start');
        $years_worked_end = $this -> post('years_worked_end');

        $errors = array();
        if (empty($company_name)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to add job',
                'detail' => 'Company name is not given.'
            );
        }
        if ($show_company_name != '0' && $show_company_name != '1') {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to add job',
                'detail' => 'The value of \'show_company_name\' parameter is invalid.'
            );
        }
        if (empty($job_title)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to add job',
                'detail' => 'Job title is not given.'
            );
        }
        if (empty($years_worked_start) || empty($years_worked_end)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to add job',
                'detail' => 'Years worked start/end is not given.'
            );
        }

        if (count($errors) > 0) {

            $this -> response(array(
                'errors' => $errors
            ), 200);
        }

        $insert_array['user_id'] = $this -> rest -> user_id;
        $insert_array['company_name'] = $company_name;
        $insert_array['show_company_name'] = $show_company_name;
        $insert_array['job_title'] = $job_title;
        $insert_array['years_worked_start'] = $years_worked_start;
        $insert_array['years_worked_end'] = $years_worked_end;

        $user_job_id = $this -> user_job_api_model -> insert_user_job($insert_array);

        $user_company = $this -> user_job_api_model -> get_user_job($user_job_id);

        $this -> response(array(
            'data' => $user_company
        ), 200);
    }

    public function career_jobs_json_post() {

        $json = $this -> post('json');

        if (empty($json)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to insert user jobs',
                        'detail' => 'JSON parameter is empty.'
                    )
                )
            ), 200);
        }

        $objects = json_decode($json);

        if (empty($objects)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to insert user jobs',
                        'detail' => 'JSON parameter is invalid.'
                    )
                )
            ), 200);
        }

        $user_jobs = array();

        foreach ($objects as $object) {

            $company_name = $object -> company_name;
            $show_company_name = $object -> show_company_name;
            if (empty($show_company_name)) $show_company_name = '0';
            $job_title = $object -> job_title;
            $years_worked_start = $object -> years_worked_start;
            $years_worked_end = $object -> years_worked_end;

            if (!empty($company_name)) {

                $insert_array['user_id'] = $this -> rest -> user_id;
                $insert_array['company_name'] = $company_name;
                $insert_array['show_company_name'] = $show_company_name;
                $insert_array['job_title'] = $job_title;
                $insert_array['years_worked_start'] = $years_worked_start;
                $insert_array['years_worked_end'] = $years_worked_end;

                $user_job_id = $this -> user_job_api_model -> insert_user_job($insert_array);

                $user_job = $this -> user_job_api_model -> get_user_job($user_job_id);

                $user_jobs[] = $user_job;
            }
        }

        $this -> response(array(
            'data' => $user_jobs
        ), 200);
    }

    public function career_jobs_put($user_job_id) {

        $company_name = $this -> put('company_name');
        $show_company_name = $this -> put('show_company_name');
        if (empty($show_company_name)) $show_company_name = '0';
        $job_title = $this -> put('job_title');
        $years_worked_start = $this -> put('years_worked_start');
        $years_worked_end = $this -> put('years_worked_end');

        $errors = array();
        if (empty($user_job_id)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to add job',
                'detail' => 'User company id is not given.'
            );
        }
        if (empty($company_name)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to add job',
                'detail' => 'Company name is not given.'
            );
        }
        if ($show_company_name != '0' && $show_company_name != '1') {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to add job',
                'detail' => 'The value of \'show_company_name\' parameter is invalid.'
            );
        }
        if (empty($job_title)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to add job',
                'detail' => 'Job title is not given.'
            );
        }
        if (empty($years_worked_start) || empty($years_worked_end)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to add job',
                'detail' => 'Years worked start/end is not given.'
            );
        }

        if (count($errors) > 0) {

            $this -> response(array(
                'errors' => $errors
            ), 200);
        }

        $update_array['company_name'] = $company_name;
        $update_array['show_company_name'] = $show_company_name;
        $update_array['job_title'] = $job_title;
        $update_array['years_worked_start'] = $years_worked_start;
        $update_array['years_worked_end'] = $years_worked_end;

        $this -> user_job_api_model -> update_user_job($user_job_id, $update_array);

        $user_company = $this -> user_job_api_model -> get_user_job($user_job_id);

        $this -> response(array(
            'data' => $user_company
        ), 200);
    }

    public function career_jobs_delete($user_job_id) {

        if (empty($user_job_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to delete user job',
                        'detail' => 'User company id is not given.'
                    )
                )
            ), 200);
        }

        $this -> user_job_api_model -> delete_user_job($user_job_id);

        $this -> response(array(
            'meta' => array(
                'status' => TRUE,
                'detail' => 'User job is removed successfully.'
            )
        ), 200);
    }

    public function other_get() {

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $descriptive_words = $this -> descriptive_word_api_model -> get_descriptive_words($this -> rest ->language_id);
        $user_descriptive_words = $this -> user_descriptive_word_api_model -> get_user_descriptive_words_by_user_id($this -> rest -> user_id, $this -> rest ->language_id);

        $interests = $this -> interest_api_model -> get_interests($this -> rest ->language_id);
        $interest_categories = $this -> interest_category_api_model -> get_interest_categories($this -> rest ->language_id);
        $user_interests = $this -> user_interest_api_model -> get_user_interests_by_user_id($this -> rest -> user_id, $this -> rest ->language_id);

        $smoking_statuses = $this -> smoking_status_api_model -> get_smoking_statuses($this -> rest ->language_id);
        $drinking_statuses = $this -> drinking_status_api_model -> get_drinking_statuses($this -> rest ->language_id);
        $exercise_frequencies = $this -> exercise_frequency_api_model -> get_exercise_frequencies($this -> rest ->language_id);
        $residence_types = $this -> residence_type_api_model -> get_residence_types($this -> rest ->language_id);
        $child_statuses = $this -> child_status_api_model -> get_child_statuses($this -> rest ->language_id);
        $child_plans = $this -> child_plan_api_model -> get_child_plans($this -> rest ->language_id);

        $spoken_language_levels = $this -> spoken_language_level_api_model -> get_spoken_language_levels($this -> rest ->language_id);
        $spoken_languages = $this -> spoken_language_api_model -> get_spoken_languages($this -> rest ->language_id);
        $user_spoken_languages = $this -> user_spoken_language_api_model -> get_user_spoken_languages_by_user_id($this -> rest -> user_id, $this -> rest ->language_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(
                    'descriptive_words' => $user_descriptive_words,
                    'interests' => $user_interests,
                    'spoken_languages' => $user_spoken_languages
                )
            ),
            'included' => array(
                'descriptive_words' => $descriptive_words,
                'interest_categories' => $interest_categories,
                'interests' => $interests,
                'smoking_statuses' => $smoking_statuses,
                'drinking_statuses' => $drinking_statuses,
                'exercise_frequencies' => $exercise_frequencies,
                'residence_types' => $residence_types,
                'child_statuses' => $child_statuses,
                'child_plans' => $child_plans,
                'spoken_language_levels' => $spoken_language_levels,
                'spoken_languages' => $spoken_languages
            )
        ), 200);
    }

    public function other_post() {

        $descriptive_word_ids = $this -> post('descriptive_word_ids');
        $interest_ids = $this -> post('interest_ids');
        $smoking_status_id = $this -> post('smoking_status_id');
        $drinking_status_id = $this -> post('drinking_status_id');
        $exercise_frequency_id = $this -> post('exercise_frequency_id');
        $residence_type_id = $this -> post('residence_type_id');
        $child_status_id = $this -> post('child_status_id');
        $child_plan_id = $this -> post('child_plan_id');

        if (!empty($smoking_status_id))
            $update_array['smoking_status_id'] = $smoking_status_id;

        if (!empty($drinking_status_id))
            $update_array['drinking_status_id'] = $drinking_status_id;

        if (!empty($exercise_frequency_id))
            $update_array['exercise_frequency_id'] = $exercise_frequency_id;

        if (!empty($residence_type_id))
            $update_array['residence_type'] = $residence_type_id;

        if (!empty($child_status_id))
            $update_array['child_status_id'] = $child_status_id;

        if (!empty($child_plan_id))
            $update_array['child_plan_id'] = $child_plan_id;

        // Update user data
        if (!empty($update_array))
            $this -> user_api_model -> update_user($this -> rest -> user_id, $update_array);

        if (!empty($descriptive_word_ids)) {

            // Delete excluded records and insert new records
            $this -> db -> user_descriptive_word_api_model -> update_user_records_with_descriptive_word_ids($this -> rest -> user_id, $descriptive_word_ids);
        }

        if (!empty($interest_ids)) {

            // Delete excluded records and insert new records
            $this -> db -> user_interest_api_model -> update_user_records_with_interest_ids($this -> rest -> user_id, $interest_ids);
        }

        if (empty($update_array) && empty($descriptive_word_ids) && empty($interest_category_id_interest_ids)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to update other information',
                        'detail' => 'No update information is provided.'
                    )
                )
            ), 200);
        }

        // Get updated user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);
        $user_descriptive_words = $this -> user_descriptive_word_api_model -> get_user_descriptive_words_by_user_id($this -> rest -> user_id, $this -> rest ->language_id);
        $user_interests = $this -> user_interest_api_model -> get_user_interests_by_user_id($this -> rest -> user_id, $this -> rest ->language_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(
                    'descriptive_words' => $user_descriptive_words,
                    'interests' => $user_interests
                )
            )
        ), 200);
    }

    public function other_spoken_languages_post() {

        $spoken_language_id = $this -> post('spoken_language_id');
        $spoken_language_level_id = $this -> post('spoken_language_level_id');

        $errors = array();
        if (empty($spoken_language_id)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to add user spoken language',
                'detail' => 'Spoken language id is not given.'
            );
        }
        if (empty($spoken_language_level_id)) {

            $errors[] = array(
                'id' => 'Currently not supported',
                'code' => 'Currently not supported',
                'title' => 'Failed to add user spoken language',
                'detail' => 'Spoken language level id is not given.'
            );
        }

        if (count($errors) > 0) {

            $this -> response(array(
                'errors' => $errors
            ), 200);
        }

        $insert_array['user_id'] = $this -> rest -> user_id;
        $insert_array['spoken_language_id'] = $spoken_language_id;
        $insert_array['spoken_language_level_id'] = $spoken_language_level_id;

        // Insert data
        $user_spoken_language_id = $this -> user_spoken_language_api_model -> insert_user_spoken_language($insert_array);

        if (empty($user_spoken_language_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to insert user spoken language',
                        'detail' => 'Failed to insert record into db.'
                    )
                )
            ), 200);
        }

        // Get updated data
        $user_spoken_language = $this -> user_spoken_language_api_model -> get_user_spoken_language($user_spoken_language_id);

        $this -> response(array(
            'data' => $user_spoken_language
        ), 200);
    }

    public function other_spoken_languages_delete($user_spoken_language_id) {

        if (empty($user_spoken_language_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to delete user spoken language',
                        'detail' => 'User spoken language id is not given.'
                    )
                )
            ), 200);
        }

        $this -> user_spoken_language_api_model -> delete_user_spoken_language($user_spoken_language_id);

        $this -> response(array(
            'meta' => array(
                'status' => TRUE,
                'detail' => 'User spoken language is removed successfully.'
            )
        ), 200);
    }

}
