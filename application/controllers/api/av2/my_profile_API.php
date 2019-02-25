<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class My_Profile_API extends  MY_REST_Controller {

    function __construct() {
        parent::__construct();

        $this -> load -> model('api/av2/annual_income_range_api_model');
        $this -> load -> model('api/av2/body_type_api_model');
        $this -> load -> model('api/av2/child_plan_api_model');
        $this -> load -> model('api/av2/child_status_api_model');
        $this -> load -> model('api/av2/city_api_model');
        $this -> load -> model('api/av2/country_api_model');
        $this -> load -> model('api/av2/descriptive_word_api_model');
        $this -> load -> model('api/av2/drinking_status_api_model');
        $this -> load -> model('api/av2/education_level_api_model');
        $this -> load -> model('api/av2/ethnicity_api_model');
        $this -> load -> model('api/av2/exercise_frequency_api_model');
        $this -> load -> model('api/av2/gender_api_model');
        $this -> load -> model('api/av2/interest_api_model');
        $this -> load -> model('api/av2/interest_category_api_model');
        $this -> load -> model('api/av2/relationship_status_api_model');
        $this -> load -> model('api/av2/religious_belief_api_model');
        $this -> load -> model('api/av2/residence_type_api_model');
        $this -> load -> model('api/av2/smoking_status_api_model');
        $this -> load -> model('api/av2/spoken_language_api_model');
        $this -> load -> model('api/av2/spoken_language_level_api_model');
        $this -> load -> model('api/av2/user_api_model');
        $this -> load -> model('api/av2/user_descriptive_word_api_model');
        $this -> load -> model('api/av2/user_education_level_api_model');
        $this -> load -> model('api/av2/user_interest_api_model');
        $this -> load -> model('api/av2/user_job_api_model');
        $this -> load -> model('api/av2/user_photo_api_model');
        $this -> load -> model('api/av2/user_school_api_model');
        $this -> load -> model('api/av2/user_spoken_language_api_model');

        $this -> load -> model('api/av2/user_want_body_type_api_model');
        $this -> load -> model('api/av2/user_want_descriptive_word_api_model');
        $this -> load -> model('api/av2/user_want_education_level_api_model');
        $this -> load -> model('api/av2/user_want_ethnicity_api_model');
        $this -> load -> model('api/av2/user_want_gender_api_model');
        $this -> load -> model('api/av2/user_want_relationship_status_api_model');
        $this -> load -> model('api/av2/user_want_religious_belief_api_model');
    }

    public function index_get() {

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        // Data for "Photos"
        $user_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($this -> rest -> user_id);

        // Data for "Basics"
        $current_city = $this -> city_api_model -> get($user -> current_city_id, $this -> rest ->language_id);
        $gender = $this -> gender_api_model -> get_gender($user -> gender_id);
        $ethnicity = $this -> ethnicity_api_model -> get_ethnicity($user -> ethnicity_id);
        $religious_belief = $this -> religious_belief_api_model -> get_religious_belief($user -> religious_belief_id);
        $relationship_status = $this -> relationship_status_api_model -> get_relationship_status($user -> relationship_status_id);
        $body_type = $this -> body_type_api_model -> get_body_type($user -> body_type_id);
        $spoken_languages = $this -> user_spoken_language_api_model -> get_user_spoken_languages_by_user_id($this -> rest -> user_id);
        $education_levels = $this -> user_education_level_api_model -> get_user_education_levels_by_user_id($this -> rest -> user_id);
        $user_schools = $this -> user_school_api_model -> get_user_schools_by_user_id($this -> rest -> user_id);
        $user_jobs = $this -> user_job_api_model -> get_user_jobs_by_user_id($this -> rest -> user_id);
        $annual_income_range = $this -> annual_income_range_api_model -> get_annual_income_range($user -> annual_income_range_id);
        $residence_type = $this -> residence_type_api_model -> get_residence_type($user -> residence_type);
        $smoking_status = $this -> smoking_status_api_model -> get_smoking_status($user -> smoking_status_id);
        $drinking_status = $this -> drinking_status_api_model -> get_drinking_status($user -> drinking_status_id);
        $exercise_frequency = $this -> exercise_frequency_api_model -> get_exercise_frequency($user -> exercise_frequency_id);
        $descriptive_words = $this -> user_descriptive_word_api_model -> get_user_descriptive_words_by_user_id($this -> rest -> user_id, $this -> rest ->language_id);

        // Data for "Preferences"
        $user_want_gender_ids = $this -> user_want_gender_api_model -> get_gender_ids_by_user_id($this -> rest -> user_id);
        $user_want_genders = array();
        if (!empty($user_want_gender_ids)) {
            foreach ($user_want_gender_ids as $user_want_gender_id) {
                $user_want_gender = $this->gender_api_model->get_gender($user_want_gender_id);
                $user_want_genders[] = $user_want_gender;
            }
        }

        $user_want_ethnicity_ids = $this -> user_want_ethnicity_api_model -> get_ethnicity_ids_by_user_id($this -> rest -> user_id);
        $user_want_ethnicities = array();
        if (!empty($user_want_ethnicity_ids)) {
            foreach ($user_want_ethnicity_ids as $user_want_ethnicity_id) {
                $user_want_ethnicity = $this->ethnicity_api_model->get_ethnicity($user_want_ethnicity_id);
                $user_want_ethnicities[] = $user_want_ethnicity;
            }
        }

        $user_want_religious_belief_ids = $this -> user_want_religious_belief_api_model -> get_religious_belief_ids_by_user_id($this -> rest ->user_id);
        $user_want_religious_beliefs = array();
        if (!empty($user_want_religious_belief_ids)) {
            foreach ($user_want_religious_belief_ids as $user_want_religious_belief_id) {
                $user_want_religious_belief = $this -> religious_belief_api_model -> get_religious_belief($user_want_religious_belief_id);
                $user_want_religious_beliefs[] = $user_want_religious_belief;
            }
        }

        $user_want_relationship_status_ids = $this -> user_want_relationship_status_api_model -> get_relationship_status_ids_by_user_id($this -> rest -> user_id);
        $user_want_relationship_statuses = array();
        if (!empty($user_want_relationship_status_ids)) {
            foreach ($user_want_relationship_status_ids as $user_want_relationship_status_id) {
                $user_want_relationship_status = $this -> relationship_status_api_model -> get_relationship_status($user_want_relationship_status_id);
                $user_want_relationship_statuses[] = $user_want_relationship_status;
            }
        }

        $user_want_body_type_ids = $this -> user_want_body_type_api_model -> get_body_type_ids_by_user_id($this -> rest -> user_id);
        $user_want_body_types = array();
        if (!empty($user_want_body_type_ids)) {
            foreach ($user_want_body_type_ids as $user_want_body_type_id) {
                $user_want_body_type = $this -> body_type_api_model -> get_body_type($user_want_body_type_id);
                $user_want_body_types[] = $user_want_body_type;
            }
        }

        $user_want_education_level_ids = $this -> user_want_education_level_api_model -> get_education_level_ids_by_user_id($this -> rest -> user_id);
        $user_want_education_levels = array();
        if (!empty($user_want_education_level_ids)) {
            foreach ($user_want_education_level_ids as $user_want_education_level_id) {
                $user_want_education_level = $this -> education_level_api_model -> get_education_level($user_want_education_level_id);
                $user_want_education_levels[] = $user_want_education_level;
            }
        }

        $user_want_descriptive_word_ids = $this -> user_want_descriptive_word_api_model -> get_descriptive_word_ids_by_user_id($this -> rest -> user_id);
        $user_want_descriptive_words = array();
        if (!empty($user_want_descriptive_word_ids)) {
            foreach ($user_want_descriptive_word_ids as $user_want_descriptive_word_id) {
                $user_want_descriptive_word = $this -> descriptive_word_api_model -> get_descriptive_word($user_want_descriptive_word_id);
                $user_want_descriptive_words[] = $user_want_descriptive_word;
            }
        }

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(

                    // Data for "Photos"
                    'user_photos' => $user_photos,

                    // Data for "Basics"
                    'current_city' => $current_city,
                    'gender' => $gender,
                    'ethnicity' => $ethnicity,
                    'religious_belief' => $religious_belief,
                    'relationship_status' => $relationship_status,
                    'body_type' => $body_type,
                    'spoken_languages' => $spoken_languages,
                    'education_levels' => $education_levels,
                    'user_schools' => $user_schools,
                    'user_jobs' => $user_jobs,
                    'annual_income_range' => $annual_income_range,
                    'residence_type' => $residence_type,
                    'smoking_status' => $smoking_status,
                    'drinking_status' => $drinking_status,
                    'exercise_frequency' => $exercise_frequency,
                    'descriptive_words' => $descriptive_words,

                    // Data for "Preferences"
                    'user_want_genders' => $user_want_genders,
                    'user_want_ethnicities' => $user_want_ethnicities,
                    'user_want_religious_beliefs' => $user_want_religious_beliefs,
                    'user_want_relationship_statuses' => $user_want_relationship_statuses,
                    'user_want_body_types' => $user_want_body_types,
                    'user_want_education_levels' => $user_want_education_levels,
                    'user_want_descriptive_words' => $user_want_descriptive_words
                )
            )
        ), 200);
    }

    public function basics_get() {

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $current_city = $this -> city_api_model -> get($user -> current_city_id, $this -> rest ->language_id);
        $gender = $this -> gender_api_model -> get_gender($user -> gender_id);
        $ethnicity = $this -> ethnicity_api_model -> get_ethnicity($user -> ethnicity_id);
        $religious_belief = $this -> religious_belief_api_model -> get_religious_belief($user -> religious_belief_id);
        $relationship_status = $this -> relationship_status_api_model -> get_relationship_status($user -> relationship_status_id);
        $body_type = $this -> body_type_api_model -> get_body_type($user -> body_type_id);
        $spoken_languages = $this -> user_spoken_language_api_model -> get_user_spoken_languages_by_user_id($this -> rest -> user_id);
        $education_levels = $this -> user_education_level_api_model -> get_user_education_levels_by_user_id($this -> rest -> user_id);
        $user_schools = $this -> user_school_api_model -> get_user_schools_by_user_id($this -> rest -> user_id);
        $user_jobs = $this -> user_job_api_model -> get_user_jobs_by_user_id($this -> rest -> user_id);
        $annual_income_range = $this -> annual_income_range_api_model -> get_annual_income_range($user -> annual_income_range_id);
        $residence_type = $this -> residence_type_api_model -> get_residence_type($user -> residence_type);
        $smoking_status = $this -> smoking_status_api_model -> get_smoking_status($user -> smoking_status_id);
        $drinking_status = $this -> drinking_status_api_model -> get_drinking_status($user -> drinking_status_id);
        $exercise_frequency = $this -> exercise_frequency_api_model -> get_exercise_frequency($user -> exercise_frequency_id);
        $descriptive_words = $this -> user_descriptive_word_api_model -> get_user_descriptive_words_by_user_id($this -> rest -> user_id, $this -> rest ->language_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(
                    'current_city' => $current_city,
                    'gender' => $gender,
                    'ethnicity' => $ethnicity,
                    'religious_belief' => $religious_belief,
                    'relationship_status' => $relationship_status,
                    'body_type' => $body_type,
                    'spoken_languages' => $spoken_languages,
                    'education_levels' => $education_levels,
                    'user_schools' => $user_schools,
                    'user_jobs' => $user_jobs,
                    'annual_income_range' => $annual_income_range,
                    'residence_type' => $residence_type,
                    'smoking_status' => $smoking_status,
                    'drinking_status' => $drinking_status,
                    'exercise_frequency' => $exercise_frequency,
                    'descriptive_words' => $descriptive_words
                )
            )
        ), 200);
    }

    public function basics_post() {

        // Populate parameters
        $first_name = $this -> post('first_name');
        $last_name = $this -> post('last_name');
        $current_city_id = $this -> post('current_city_id');
        $birth_date_str = $this -> post('birth_date');
        $gender_id = $this -> post('gender_id');
        $ethnicity_id = $this -> post('ethnicity_id');
        $height = $this -> post('height');
        $religious_belief_id = $this -> post('religious_belief_id');
        $relationship_status_id = $this -> post('relationship_status_id');
        $body_type_id = $this -> post('body_type_id');
        $spoken_language_ids = $this -> post('spoken_language_ids');
        $education_level_ids = $this -> post('education_level_ids');
        $user_schools_json = $this -> post('user_schools_json');
        $user_jobs_json = $this -> post('user_jobs_json');
        $annual_income_range_id = $this -> post('annual_income_range_id');
        $residence_type_id = $this -> post('residence_type_id');
        $smoking_status_id = $this -> post('smoking_status_id');
        $drinking_status_id = $this -> post('drinking_status_id');
        $exercise_frequency_id = $this -> post('exercise_frequency_id');
        $descriptive_word_ids = $this -> post('descriptive_word_ids');

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        // Build update array
        if (!empty($first_name))
            $update_array['first_name'] = $first_name;

        if (!empty($last_name))
            $update_array['last_name'] = $last_name;

        if (!empty($current_city_id))
            $update_array['current_city_id'] = $current_city_id;

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
            if (empty($user -> want_age_range_lower) && empty($user -> want_age_range_upper)) {

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
        }

        if (!empty($gender_id))
            $update_array['gender_id'] = $gender_id;

        if (!empty($ethnicity_id))
            $update_array['ethnicity_id'] = $ethnicity_id;

        if (!empty($height))
            $update_array['height'] = $height;

        if (!empty($religious_belief_id))
            $update_array['religious_belief_id'] = $religious_belief_id;

        if (!empty($relationship_status_id))
            $update_array['relationship_status_id'] = $relationship_status_id;

        if (!empty($body_type_id))
            $update_array['body_type_id'] = $body_type_id;

        if (!empty($annual_income_range_id))
            $update_array['annual_income_range_id'] = $annual_income_range_id;

        if (!empty($residence_type_id))
            $update_array['residence_type'] = $residence_type_id;

        if (!empty($smoking_status_id))
            $update_array['smoking_status_id'] = $smoking_status_id;

        if (!empty($drinking_status_id))
            $update_array['drinking_status_id'] = $drinking_status_id;

        if (!empty($exercise_frequency_id))
            $update_array['exercise_frequency_id'] = $exercise_frequency_id;

        if (!empty($update_array)) {

            // Update user data
            $this -> user_api_model -> update_user($this -> rest -> user_id, $update_array);
        }

        if (empty($spoken_language_ids)) {
            // User selects no spoken language
            $this -> user_spoken_language_api_model -> delete_user_spoken_languages_by_user_id($this -> rest -> user_id);
        } else {
            $this -> user_spoken_language_api_model -> update_user_records_with_spoken_language_ids($this -> rest -> user_id, $spoken_language_ids);
        }

        if (empty($education_level_ids)) {
            // User selects no education level
            $this -> user_education_level_api_model -> delete_user_education_levels_by_user_id($this -> rest -> user_id);
        } else {
            $this -> user_education_level_api_model -> update_user_records_with_education_level_ids($this -> rest -> user_id, $education_level_ids);
        }

        if (empty($descriptive_word_ids)) {
            // User selects no descriptive word
            $this -> user_descriptive_word_api_model -> delete_user_descriptive_words_by_user_id($this -> rest -> user_id);
        } else {
            $this -> user_descriptive_word_api_model -> update_user_records_with_descriptive_word_ids($this -> rest -> user_id, $descriptive_word_ids);
        }

        if (empty($user_schools_json)) {
            // User selects no user school
            $this -> user_school_api_model -> delete_user_schools_by_user_id($this -> rest -> user_id);
        } else {

            $user_schools = json_decode($user_schools_json);
            if (!empty($user_schools)) {

                $user_school_ids = array();

                foreach ($user_schools as $user_school) {

                    if (!empty($user_school -> user_school_id) &&
                        $user_school -> user_school_id > 0) {   // Update existing user school

                        unset($update_array);
                        $update_array['school_id'] = $user_school -> school_id;
                        $update_array['school_name'] = $user_school -> school_name;
                        $update_array['degree_name'] = $user_school -> degree_name;
                        $update_array['years_attended_start'] = $user_school -> years_attended_start;
                        $update_array['years_attended_end'] = $user_school -> years_attended_end;

                        $this -> user_school_api_model -> update_user_school($user_school -> user_school_id, $update_array);

                        $user_school_ids[] = $user_school -> user_school_id;

                    } else {    // Insert new user school

                        if (!empty($user_school -> school_id)) {

                            unset($insert_array);
                            $insert_array['user_id'] = $this -> rest -> user_id;
                            $insert_array['school_id'] = $user_school -> school_id;
                            $insert_array['school_name'] = $user_school -> school_name;
                            $insert_array['degree_name'] = $user_school -> degree_name;
                            $insert_array['years_attended_start'] = $user_school -> years_attended_start;
                            $insert_array['years_attended_end'] = $user_school -> years_attended_end;

                            $user_school_ids[] = $this -> user_school_api_model -> insert_user_school($insert_array);
                        }
                    }
                }

                // Delete excluded user schools
                $this -> user_school_api_model -> delete_excluded_user_schools_by_ids($this -> rest -> user_id, $user_school_ids);
            }
        }

        if (empty($user_jobs_json)) {
            // User selects no user job
            $this -> user_job_api_model -> delete_user_jobs_by_user_id($this -> rest -> user_id);
        } else {

            $user_jobs = json_decode($user_jobs_json);
            if (!empty($user_jobs)) {

                $user_job_ids = array();

                foreach ($user_jobs as $user_job) {

                    if (!empty($user_job -> user_company_id) &&
                        $user_job -> user_company_id > 0) {     // Update existing user job

                        unset($update_array);
                        $update_array['company_id'] = $user_job -> company_id;
                        $update_array['company_name'] = !empty($user_job -> company_name) ? $user_job -> company_name : '';
                        $update_array['job_title'] = $user_job -> job_title;
                        $update_array['job_city_name'] = $user_job -> job_city_name;
                        $update_array['years_worked_start'] = $user_job -> years_worked_start;
                        $update_array['years_worked_end'] = $user_job -> years_worked_end;

                        $this -> user_job_api_model -> update_user_job($user_job -> user_company_id, $user_job);

                        $user_job_ids[] = $user_job -> user_company_id;

                    } else {    // Insert new user job

                        if (!empty($user_job -> company_id)) {

                            unset($insert_array);
                            $insert_array['user_id'] = $this -> rest -> user_id;
                            $insert_array['company_id'] = $user_job -> company_id;
                            $insert_array['company_name'] = $user_job -> company_name;
                            $insert_array['job_title'] = $user_job -> job_title;
                            $insert_array['job_city_name'] = $user_job -> job_city_name;
                            $insert_array['years_worked_start'] = $user_job -> years_worked_start;
                            $insert_array['years_worked_end'] = $user_job -> years_worked_end;

                            $user_job_ids[] = $this -> user_job_api_model -> insert_user_job($insert_array);
                        }
                    }
                }

                // Delete excluded user jobs
                $this -> user_job_api_model -> delete_excluded_user_jobs_by_ids($this -> rest -> user_id, $user_job_ids);

            }
        }

        // Get updated user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $user_public_profile_count = $this -> user_api_model -> get_user_public_profile_count($this -> rest ->user_id);
        $user_experience_count = $this -> user_api_model -> get_user_experience_count($this -> rest ->user_id);
        $user_personality_count = $this -> user_api_model -> get_user_personality_count($this -> rest ->user_id);
        $profile_completeness_percent = intval(($user_public_profile_count + $user_experience_count + $user_personality_count) / (9 + 4 + 4) * 100);
        if ($profile_completeness_percent > 100) {
            $profile_completeness_percent = 100;
        }

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'meta' => array(
                    'profile_completeness_percent' => $profile_completeness_percent
                )
            )
        ), 200);
    }

    public function preferences_get() {

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $user_want_gender_ids = $this -> user_want_gender_api_model -> get_gender_ids_by_user_id($this -> rest -> user_id);
        $user_want_genders = array();
        if (!empty($user_want_gender_ids)) {
            foreach ($user_want_gender_ids as $user_want_gender_id) {
                $gender = $this->gender_api_model->get_gender($user_want_gender_id);
                $user_want_genders[] = $gender;
            }
        }

        $user_want_ethnicity_ids = $this -> user_want_ethnicity_api_model -> get_ethnicity_ids_by_user_id($this -> rest -> user_id);
        $user_want_ethnicities = array();
        if (!empty($user_want_ethnicity_ids)) {
            foreach ($user_want_ethnicity_ids as $user_want_ethnicity_id) {
                $ethnicity = $this->ethnicity_api_model->get_ethnicity($user_want_ethnicity_id);
                $user_want_ethnicities[] = $ethnicity;
            }
        }

        $user_want_religious_belief_ids = $this -> user_want_religious_belief_api_model -> get_religious_belief_ids_by_user_id($this -> rest ->user_id);
        $user_want_religious_beliefs = array();
        if (!empty($user_want_religious_belief_ids)) {
            foreach ($user_want_religious_belief_ids as $user_want_religious_belief_id) {
                $religious_belief = $this -> religious_belief_api_model -> get_religious_belief($user_want_religious_belief_id);
                $user_want_religious_beliefs[] = $religious_belief;
            }
        }

        $user_want_relationship_status_ids = $this -> user_want_relationship_status_api_model -> get_relationship_status_ids_by_user_id($this -> rest -> user_id);
        $user_want_relationship_statuses = array();
        if (!empty($user_want_relationship_status_ids)) {
            foreach ($user_want_relationship_status_ids as $user_want_relationship_status_id) {
                $relationship_status = $this -> relationship_status_api_model -> get_relationship_status($user_want_relationship_status_id);
                $user_want_relationship_statuses[] = $relationship_status;
            }
        }

        $user_want_body_type_ids = $this -> user_want_body_type_api_model -> get_body_type_ids_by_user_id($this -> rest -> user_id);
        $user_want_body_types = array();
        if (!empty($user_want_body_type_ids)) {
            foreach ($user_want_body_type_ids as $user_want_body_type_id) {
                $body_type = $this -> body_type_api_model -> get_body_type($user_want_body_type_id);
                $user_want_body_types[] = $body_type;
            }
        }

        $user_want_education_level_ids = $this -> user_want_education_level_api_model -> get_education_level_ids_by_user_id($this -> rest -> user_id);
        $user_want_education_levels = array();
        if (!empty($user_want_education_level_ids)) {
            foreach ($user_want_education_level_ids as $user_want_education_level_id) {
                $education_level = $this -> education_level_api_model -> get_education_level($user_want_education_level_id);
                $user_want_education_levels[] = $education_level;
            }
        }

        $user_want_descriptive_word_ids = $this -> user_want_descriptive_word_api_model -> get_descriptive_word_ids_by_user_id($this -> rest -> user_id);
        $user_want_descriptive_words = array();
        if (!empty($user_want_descriptive_word_ids)) {
            foreach ($user_want_descriptive_word_ids as $user_want_descriptive_word_id) {
                $descriptive_word = $this -> descriptive_word_api_model -> get_descriptive_word($user_want_descriptive_word_id);
                $user_want_descriptive_words[] = $descriptive_word;
            }
        }

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(
                    'user_want_genders' => $user_want_genders,
                    'user_want_ethnicities' => $user_want_ethnicities,
                    'user_want_religious_beliefs' => $user_want_religious_beliefs,
                    'user_want_relationship_statuses' => $user_want_relationship_statuses,
                    'user_want_body_types' => $user_want_body_types,
                    'user_want_education_levels' => $user_want_education_levels,
                    'user_want_descriptive_words' => $user_want_descriptive_words
                )
            )
        ), 200);
    }

    public function preferences_post() {

        $want_gender_ids = $this -> post('want_gender_ids');
        $want_age_range_lower = $this -> post('want_age_range_lower');
        $want_age_range_upper = $this -> post('want_age_range_upper');
        $want_ethnicity_ids = $this -> post('want_ethnicity_ids');
        $want_height_range_lower = $this -> post('want_height_range_lower');
        $want_height_range_upper = $this -> post('want_height_range_upper');
        $want_religious_belief_ids = $this -> post('want_religious_belief_ids');
        $want_relationship_status_ids = $this -> post('want_relationship_status_ids');
        $want_body_type_ids = $this -> post('want_body_type_ids');
        $want_education_level_ids = $this -> post('want_education_level_ids');
        $want_descriptive_word_ids = $this -> post('want_descriptive_word_ids');

        // Build update array
        if (!empty($want_age_range_lower))
            $update_array['want_age_range_lower'] = $want_age_range_lower;

        if (!empty($want_age_range_upper))
            $update_array['want_age_range_upper'] = $want_age_range_upper;

        if (!empty($want_height_range_lower))
            $update_array['want_height_range_lower'] = $want_height_range_lower;

        if (!empty($want_height_range_upper))
            $update_array['want_height_range_upper'] = $want_height_range_upper;

        if (!empty($update_array)) {

            // Update user data
            $this -> user_api_model -> update_user($this -> rest -> user_id, $update_array);
        }

        if (empty($want_gender_ids)) {
            // User selects no gender
            $this -> user_want_gender_api_model -> delete_user_want_genders_by_user_id($this -> rest -> user_id);
        } else {
            $this -> user_want_gender_api_model -> update_user_want_records_with_gender_ids($this -> rest -> user_id, $want_gender_ids);
        }

        if (empty($want_ethnicity_ids)) {
            // User selects no ethnicity
            $this -> user_want_ethnicity_api_model -> delete_user_want_ethnicities_by_user_id($this -> rest -> user_id);
        } else {
            $this -> user_want_ethnicity_api_model -> update_user_want_records_with_ethnicity_ids($this -> rest -> user_id, $want_ethnicity_ids);
        }

        if (empty($want_religious_belief_ids)) {
            // User selects no religious belief
            $this -> user_want_religious_belief_api_model -> delete_user_want_religious_beliefs_by_user_id($this -> rest -> user_id);
        } else {
            $this -> user_want_religious_belief_api_model -> update_user_want_records_with_religious_belief_ids($this -> rest -> user_id, $want_religious_belief_ids);
        }

        if (empty($want_relationship_status_ids)) {
            // User selects no relationship status
            $this -> user_want_relationship_status_api_model -> delete_user_want_relationship_statuses_by_user_id($this -> rest -> user_id);
        } else {
            $this -> user_want_relationship_status_api_model -> update_user_want_records_with_relationship_status_ids($this -> rest -> user_id, $want_relationship_status_ids);
        }

        if (empty($want_body_type_ids)) {
            // User selects no body type
            $this -> user_want_body_type_api_model -> delete_user_want_body_types_by_user_id($this -> rest -> user_id);
        } else {
            $this -> user_want_body_type_api_model -> update_user_want_records_with_body_type_ids($this -> rest -> user_id, $want_body_type_ids);
        }

        if (empty($want_education_level_ids)) {
            // User selects no education level
            $this -> user_want_education_level_api_model -> delete_user_want_education_levels_by_user_id($this -> rest -> user_id);
        } else {
            $this -> user_want_education_level_api_model -> update_user_want_records_with_education_level_ids($this -> rest -> user_id, $want_education_level_ids);
        }

        if (empty($want_descriptive_word_ids)) {
            // User selects no descriptive word
            $this -> user_want_descriptive_word_api_model -> delete_user_want_descriptive_words_by_user_id($this -> rest -> user_id);
        } else {
            $this -> user_want_descriptive_word_api_model -> update_user_want_records_with_descriptive_word_ids($this -> rest -> user_id, $want_descriptive_word_ids);
        }

        // Get updated user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user
            )
        ), 200);
    }

    public function photos_get() {

        $user_photos = $this -> user_photo_api_model -> get_user_photos_by_user_id($this -> rest -> user_id);

        $this -> response(array(
            'data' => $user_photos
        ), 200);
    }

    public function photos_post() {

        $user_photo_ids_json = $this -> post('user_photo_ids_json');
        $user_photo_ids = json_decode($user_photo_ids_json);
        $primary_user_photo_id = $this -> post('primary_user_photo_id');

        // Create folder
        $folder_path = $this -> user_photo_api_model -> create_user_photos_folder($this -> rest -> user_id);

        if (empty($user_photo_ids)) {   // Delete all previous user photos
            $this -> user_photo_api_model -> delete_user_photos_by_user_id($this -> rest -> user_id);
        } else {        // Delete excluded user photos
            $this -> user_photo_api_model -> delete_excluded_user_photos_by_ids($this -> rest -> user_id, $user_photo_ids);
            if (!empty($primary_user_photo_id) && $primary_user_photo_id > 0) {
                $this -> user_photo_api_model -> set_primary_photo_with_params($this -> rest -> user_id, $primary_user_photo_id);
            } else {
                $this -> user_photo_api_model -> clear_primary_photo_by_user_id($this -> rest -> user_id);
            }
        }

        // Re-array files
        if (!empty($_FILES['user_photos'])) {

            $file_array = $this -> rearray_files($_FILES['user_photos']);
            $_FILES = $file_array;

            if (count($_FILES) > 0) {

                $this -> load -> library('upload');

                $index = 0;

                foreach ($_FILES as $key => $file) {

                    // File extension
                    $file_extension = substr($file['name'], strrpos($file['name'], '.') + 1);

                    $config['upload_path'] = $folder_path;
                    $config['file_name'] = strtotime(SQL_DATETIME) . "_{$key}_profile_pic.$file_extension";
                    $config['allowed_types'] = '*';
                    $config['overwrite'] = TRUE;

                    $this -> upload -> initialize($config);

                    if ($this -> upload -> do_upload($key)) {

                        $set_primary = 0;
                        if (empty($primary_user_photo_id) && $index == 0) {
                            $set_primary = 1;
                        }

                        // Insert user photo
                        $this -> user_photo_api_model -> insert_user_photo_with_upload_data($this -> rest -> user_id, $this -> upload -> data(), $set_primary);

                        $index++;
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
}
