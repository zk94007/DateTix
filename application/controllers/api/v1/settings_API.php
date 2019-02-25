<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/MY_REST_Controller.php';

class Settings_API extends  MY_REST_Controller {

    function __construct() {
        parent::__construct();

        $this -> load -> model('api/v1/account_status_api_model');
        $this -> load -> model('api/v1/user_api_model');
        $this -> load -> model('api/v1/user_membership_option_api_model');

        $this -> load -> model('api/v1/user_preferred_date_type_api_model');
        $this -> load -> model('api/v1/date_type_api_model');

        $this -> load -> model('api/v1/user_want_gender_api_model');
        $this -> load -> model('api/v1/gender_api_model');

        $this -> load -> model('api/v1/user_want_ethnicity_api_model');
        $this -> load -> model('api/v1/ethnicity_api_model');

        $this -> load -> model('api/v1/user_want_body_type_api_model');
        $this -> load -> model('api/v1/body_type_api_model');

        $this -> load -> model('api/v1/user_want_child_plan_api_model');
        $this -> load -> model('api/v1/child_plan_api_model');

        $this -> load -> model('api/v1/user_want_child_status_api_model');
        $this -> load -> model('api/v1/child_status_api_model');

        $this -> load -> model('api/v1/user_want_descriptive_word_api_model');
        $this -> load -> model('api/v1/descriptive_word_api_model');

        $this -> load -> model('api/v1/user_want_education_level_api_model');
        $this -> load -> model('api/v1/education_level_api_model');

        $this -> load -> model('api/v1/user_want_relationship_status_api_model');
        $this -> load -> model('api/v1/relationship_status_api_model');

        $this -> load -> model('api/v1/user_want_relationship_type_api_model');
        $this -> load -> model('api/v1/relationship_type_api_model');

        $this -> load -> model('api/v1/user_want_religious_belief_api_model');
        $this -> load -> model('api/v1/religious_belief_api_model');

        $this -> load -> model('api/v1/user_want_residence_type_api_model');
        $this -> load -> model('api/v1/residence_type_api_model');

        $this -> load -> model('api/v1/user_want_smoking_status_api_model');
        $this -> load -> model('api/v1/smoking_status_api_model');

        $this -> load -> model('api/v1/user_want_drinking_status_api_model');
        $this -> load -> model('api/v1/drinking_status_api_model');

        $this -> load -> model('api/v1/user_want_exercise_frequency_api_model');
        $this -> load -> model('api/v1/exercise_frequency_api_model');

        $this -> load -> model('api/v1/user_want_school_api_model');
        $this -> load -> model('api/v1/school_api_model');

        $this -> load -> model('api/v1/user_want_company_api_model');
        $this -> load -> model('api/v1/company_api_model');

        $this -> load -> model('api/v1/user_want_school_subject_api_model');
    }

    public function dates_get() {

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $user_preferred_date_types = $this -> user_preferred_date_type_api_model -> get_user_preferred_date_types_by_user_id($this -> rest -> user_id);
        $date_types = $this -> date_type_api_model -> get_date_types($this -> rest -> language_id);

        $user_want_genders = $this -> user_want_gender_api_model -> get_user_want_genders_by_user_id($this -> rest -> user_id);
        $genders = $this -> gender_api_model -> get_genders($this -> rest -> language_id);

        $user_want_ethnicities = $this -> user_want_ethnicity_api_model -> get_user_want_ethnicities_by_user_id($this -> rest -> user_id);
        $ethnicities = $this -> ethnicity_api_model -> get_ethnicities($this -> rest -> language_id);

        $user_want_body_types = $this -> user_want_body_type_api_model -> get_user_want_body_types_by_user_id($this -> rest -> user_id);
        $body_types = $this -> body_type_api_model -> get_body_types($this -> rest -> language_id);

        $user_want_child_plans = $this -> user_want_child_plan_api_model -> get_user_want_child_plans_by_user_id($this -> rest -> user_id);
        $child_plans = $this -> child_plan_api_model -> get_child_plans($this -> rest -> language_id);

        $user_want_child_statuses = $this -> user_want_child_status_api_model -> get_user_want_child_statuses_by_user_id($this -> rest -> user_id);
        $child_statuses = $this -> child_status_api_model -> get_child_statuses($this -> rest -> language_id);

        $user_want_descriptive_words = $this -> user_want_descriptive_word_api_model -> get_user_want_descriptive_words_by_user_id($this -> rest -> user_id);
        $descriptive_words = $this -> descriptive_word_api_model -> get_descriptive_words($this -> rest ->language_id);

        $user_want_education_levels = $this -> user_want_education_level_api_model -> get_user_want_education_levels_by_user_id($this -> rest -> user_id);
        $education_levels = $this -> education_level_api_model -> get_education_levels($this -> rest -> language_id);

        $user_want_relationship_statuses = $this -> user_want_relationship_status_api_model -> get_user_want_relationship_statuses_by_user_id($this -> rest -> user_id);
        $relationship_statuses = $this -> relationship_status_api_model -> get_relationship_statuses($this -> rest -> language_id);

        $user_want_relationship_types = $this -> user_want_relationship_type_api_model -> get_user_want_relationship_types_by_user_id($this -> rest -> user_id);
        $relationship_types = $this -> relationship_type_api_model -> get_relationship_types($this -> rest -> language_id);

        $user_want_religious_beliefs = $this -> user_want_religious_belief_api_model -> get_user_want_religious_beliefs_by_user_id($this -> rest -> user_id);
        $religious_beliefs = $this -> religious_belief_api_model -> get_religious_beliefs($this -> rest -> language_id);

        $user_want_residence_types = $this -> user_want_residence_type_api_model -> get_user_want_residence_types_by_user_id($this -> rest -> user_id);
        $residence_types = $this -> residence_type_api_model -> get_residence_types($this -> rest -> language_id);

        $user_want_smoking_statuses = $this -> user_want_smoking_status_api_model -> get_user_want_smoking_statuses_by_user_id($this -> rest -> user_id);
        $smoking_statuses = $this -> smoking_status_api_model -> get_smoking_statuses($this -> rest -> language_id);

        $user_want_drinking_statuses = $this -> user_want_drinking_status_api_model -> get_user_want_drinking_statuses_by_user_id($this -> rest -> user_id);
        $drinking_statuses = $this -> drinking_status_api_model -> get_drinking_statuses($this -> rest -> language_id);

        $user_want_exercise_frequencies = $this -> user_want_exercise_frequency_api_model -> get_user_want_exercise_frequencies_by_user_id($this -> rest -> user_id);
        $exercise_frequencies = $this -> exercise_frequency_api_model -> get_exercise_frequencies($this -> rest -> language_id);

        $user_want_schools = $this -> user_want_school_api_model -> get_user_want_schools_by_user_id($this -> rest -> user_id);
        $schools = $this -> school_api_model -> get_schools($this -> rest -> language_id);

        $user_want_companies = $this -> user_want_company_api_model -> get_user_want_companies_by_user_id($this -> rest -> user_id);
        $companies = $this -> company_api_model -> get_companies($this -> rest -> language_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(
                    'user_preferred_date_types' => $user_preferred_date_types,
                    'user_want_genders' => $user_want_genders,
                    'user_want_ethnicities' => $user_want_ethnicities,
                    'user_want_body_types' => $user_want_body_types,
                    'user_want_child_plans' => $user_want_child_plans,
                    'user_want_child_statuses' => $user_want_child_statuses,
                    'user_want_descriptive_words' => $user_want_descriptive_words,
                    'user_want_education_levels' => $user_want_education_levels,
                    'user_want_relationship_statuses' => $user_want_relationship_statuses,
                    'user_want_relationship_types' => $user_want_relationship_types,
                    'user_want_religious_beliefs' => $user_want_religious_beliefs,
                    'user_want_residence_types' => $user_want_residence_types,
                    'user_want_smoking_statuses' => $user_want_smoking_statuses,
                    'user_want_drinking_statuses' => $user_want_drinking_statuses,
                    'user_want_exercise_frequencies' => $user_want_exercise_frequencies,
                    'user_want_schools' => $user_want_schools,
                    'user_want_companies' => $user_want_companies
                )
            ),
            'included' => array(
                'date_types' => $date_types,
                'genders' => $genders,
                'ethnicities' => $ethnicities,
                'body_types' => $body_types,
                'child_plans' => $child_plans,
                'child_statuses' => $child_statuses,
                'descriptive_words' => $descriptive_words,
                'education_levels' => $education_levels,
                'relationship_statuses' => $relationship_statuses,
                'relationship_types' => $relationship_types,
                'religious_beliefs' => $religious_beliefs,
                'residence_types' => $residence_types,
                'smoking_statuses' => $smoking_statuses,
                'drinking_statuses' => $drinking_statuses,
                'exercise_frequencies' => $exercise_frequencies,
                'schools' => $schools,
                'companies' => $companies
            )
        ), 200);
    }

    public function dates_post() {

        $want_age_range_lower = $this -> post('want_age_range_lower');
        $want_age_range_upper = $this -> post('want_age_range_upper');

        $want_height_range_lower = $this -> post('want_height_range_lower');
        $want_height_range_upper = $this -> post('want_height_range_upper');

        $want_max_date_distance = $this -> post('want_max_date_distance');
        $want_max_people_distance = $this -> post('want_max_people_distance');

        $want_annual_income_range_id = $this -> post('want_annual_income_range_id');

        $preferred_date_type_ids = $this -> post('preferred_date_type_ids');
        $want_gender_ids = $this -> post('want_gender_ids');
        $want_ethnicity_ids = $this -> post('want_ethnicity_ids');
        $want_body_type_ids = $this -> post('want_body_type_ids');
        $want_child_plan_ids = $this -> post('want_child_plan_ids');
        $want_child_status_ids = $this -> post('want_child_status_ids');
        $want_descriptive_word_ids = $this -> post('want_descriptive_word_ids');
        $want_education_level_ids = $this -> post('want_education_level_ids');
        $want_relationship_status_ids = $this -> post('want_relationship_status_ids');
        $want_relationship_type_ids = $this -> post('want_relationship_type_ids');
        $want_religious_belief_ids = $this -> post('want_religious_belief_ids');
        $want_residence_type_ids = $this -> post('want_residence_type_ids');
        $want_smoking_status_ids = $this -> post('want_smoking_status_ids');
        $want_drinking_status_ids = $this -> post('want_drinking_status_ids');
        $want_exercise_frequency_ids = $this -> post('want_exercise_frequency_ids');

        // Build update array
        if (!empty($want_age_range_lower))
            $update_array['want_age_range_lower'] = $want_age_range_lower;

        if (!empty($want_age_range_upper))
            $update_array['want_age_range_upper'] = $want_age_range_upper;

        if (!empty($want_height_range_lower))
            $update_array['want_height_range_lower'] = $want_height_range_lower;

        if (!empty($want_height_range_upper))
            $update_array['want_height_range_upper'] = $want_height_range_upper;

        if (!empty($want_annual_income_range_id))
            $update_array['want_annual_income'] = $want_annual_income_range_id;

        if (!empty($want_max_date_distance))
            $update_array['want_max_date_distance'] = $want_max_date_distance;

        if (!empty($want_max_people_distance))
            $update_array['want_max_people_distance'] = $want_max_people_distance;

        // Update user data
        if (!empty($update_array)) {

            $this -> user_api_model -> update_user($this -> rest -> user_id, $update_array);
        }

        // Update relationships by deleting excluded records and inserting new records
        if (!empty($preferred_date_type_ids))
            $this -> user_preferred_date_type_api_model -> update_user_want_records_with_date_type_ids($this -> rest -> user_id, $preferred_date_type_ids);

        if (!empty($want_gender_ids))
            $this -> user_want_gender_api_model -> update_user_want_records_with_gender_ids($this -> rest -> user_id, $want_gender_ids);

        if (!empty($want_ethnicity_ids))
            $this -> user_want_ethnicity_api_model -> update_user_want_records_with_ethnicity_ids($this -> rest -> user_id, $want_ethnicity_ids);

        if (!empty($want_body_type_ids))
            $this -> user_want_body_type_api_model -> update_user_want_records_with_body_type_ids($this -> rest -> user_id, $want_body_type_ids);

        if (!empty($want_child_plan_ids))
            $this -> user_want_child_plan_api_model -> update_user_want_records_with_child_plan_ids($this -> rest -> user_id, $want_child_plan_ids);

        if (!empty($want_child_status_ids))
            $this -> user_want_child_status_api_model -> update_user_want_records_with_child_status_ids($this -> rest -> user_id, $want_child_status_ids);

        if (!empty($want_descriptive_word_ids))
            $this -> user_want_descriptive_word_api_model -> update_user_want_records_with_descriptive_word_ids($this -> rest -> user_id, $want_descriptive_word_ids);

        if (!empty($want_education_level_ids))
            $this -> user_want_education_level_api_model -> update_user_want_records_with_education_level_ids($this -> rest -> user_id, $want_education_level_ids);

        if (!empty($want_relationship_status_ids))
            $this -> user_want_relationship_status_api_model -> update_user_want_records_with_relationship_status_ids($this -> rest -> user_id, $want_relationship_status_ids);

        if (!empty($want_relationship_type_ids))
            $this -> user_want_relationship_type_api_model -> update_user_want_records_with_relationship_type_ids($this -> rest -> user_id, $want_relationship_type_ids);

        if (!empty($want_religious_belief_ids))
            $this -> user_want_religious_belief_api_model -> update_user_want_records_with_religious_belief_ids($this -> rest -> user_id, $want_religious_belief_ids);

        if (!empty($want_residence_type_ids))
            $this -> user_want_residence_type_api_model -> update_user_want_records_with_residence_type_ids($this -> rest -> user_id, $want_residence_type_ids);

        if (!empty($want_smoking_status_ids))
            $this -> user_want_smoking_status_api_model -> update_user_want_records_with_smoking_status_ids($this -> rest -> user_id, $want_smoking_status_ids);

        if (!empty($want_drinking_status_ids))
            $this -> user_want_drinking_status_api_model -> update_user_want_records_with_drinking_status_ids($this -> rest -> user_id, $want_drinking_status_ids);

        if (!empty($want_exercise_frequency_ids))
            $this -> user_want_exercise_frequency_api_model -> update_user_want_records_with_exercise_frequency_ids($this -> rest -> user_id, $want_exercise_frequency_ids);

        if (empty($update_array) &&
            empty($preferred_date_type_ids) &&
            empty($want_gender_ids) &&
            empty($want_ethnicity_ids) &&
            empty($want_body_type_ids) &&
            empty($want_child_plan_ids) &&
            empty($want_child_status_ids) &&
            empty($want_descriptive_word_ids) &&
            empty($want_education_level_ids) &&
            empty($want_relationship_status_ids) &&
            empty($want_relationship_type_ids) &&
            empty($want_religious_belief_ids) &&
            empty($want_residence_type_ids) &&
            empty($want_smoking_status_ids) &&
            empty($want_drinking_status_ids) &&
            empty($want_exercise_frequency_ids)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to update \'dates\' settings',
                        'detail' => 'No update information is provided.'
                    )
                )
            ), 200);
        }

        // Get updated user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        // Get update user relationships
        $user_preferred_date_types = $this -> user_preferred_date_type_api_model -> get_user_preferred_date_types_by_user_id($this -> rest -> user_id);
        $user_want_genders = $this -> user_want_gender_api_model -> get_user_want_genders_by_user_id($this -> rest -> user_id);
        $user_want_ethnicities = $this -> user_want_ethnicity_api_model -> get_user_want_ethnicities_by_user_id($this -> rest -> user_id);
        $user_want_body_types = $this -> user_want_body_type_api_model -> get_user_want_body_types_by_user_id($this -> rest -> user_id);
        $user_want_child_plans = $this -> user_want_child_plan_api_model -> get_user_want_child_plans_by_user_id($this -> rest -> user_id);
        $user_want_child_statuses = $this -> user_want_child_status_api_model -> get_user_want_child_statuses_by_user_id($this -> rest -> user_id);
        $user_want_descriptive_words = $this -> user_want_descriptive_word_api_model -> get_user_want_descriptive_words_by_user_id($this -> rest -> user_id);
        $user_want_education_levels = $this -> user_want_education_level_api_model -> get_user_want_education_levels_by_user_id($this -> rest -> user_id);
        $user_want_relationship_statuses = $this -> user_want_relationship_status_api_model -> get_user_want_relationship_statuses_by_user_id($this -> rest -> user_id);
        $user_want_relationship_types = $this -> user_want_relationship_type_api_model -> get_user_want_relationship_types_by_user_id($this -> rest -> user_id);
        $user_want_religious_beliefs = $this -> user_want_religious_belief_api_model -> get_user_want_religious_beliefs_by_user_id($this -> rest -> user_id);
        $user_want_residence_types = $this -> user_want_residence_type_api_model -> get_user_want_residence_types_by_user_id($this -> rest -> user_id);
        $user_want_smoking_statuses = $this -> user_want_smoking_status_api_model -> get_user_want_smoking_statuses_by_user_id($this -> rest -> user_id);
        $user_want_drinking_statuses = $this -> user_want_drinking_status_api_model -> get_user_want_drinking_statuses_by_user_id($this -> rest -> user_id);
        $user_want_exercise_frequencies = $this -> user_want_exercise_frequency_api_model -> get_user_want_exercise_frequencies_by_user_id($this -> rest -> user_id);
        $user_want_schools = $this -> user_want_school_api_model -> get_user_want_schools_by_user_id($this -> rest -> user_id);
        $user_want_companies = $this -> user_want_company_api_model -> get_user_want_companies_by_user_id($this -> rest -> user_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(
                    'user_preferred_date_types' => $user_preferred_date_types,
                    'user_want_genders' => $user_want_genders,
                    'user_want_ethnicities' => $user_want_ethnicities,
                    'user_want_body_types' => $user_want_body_types,
                    'user_want_child_plans' => $user_want_child_plans,
                    'user_want_child_statuses' => $user_want_child_statuses,
                    'user_want_descriptive_words' => $user_want_descriptive_words,
                    'user_want_education_levels' => $user_want_education_levels,
                    'user_want_relationship_statuses' => $user_want_relationship_statuses,
                    'user_want_relationship_types' => $user_want_relationship_types,
                    'user_want_religious_beliefs' => $user_want_religious_beliefs,
                    'user_want_residence_types' => $user_want_residence_types,
                    'user_want_smoking_statuses' => $user_want_smoking_statuses,
                    'user_want_drinking_statuses' => $user_want_drinking_statuses,
                    'user_want_exercise_frequencies' => $user_want_exercise_frequencies,
                    'user_want_schools' => $user_want_schools,
                    'user_want_companies' => $user_want_companies
                )
            )
        ), 200);
    }

    public function dates_user_want_schools_post() {

        $school_id = $this -> post('school_id');
        $school_name = $this -> post('school_name');

        if (empty($school_id)) {

            if (empty($school_name)) {

                $this -> response(array(
                    'errors' => array(
                        array(
                            'id' => 'Currently not supported',
                            'code' => 'Currently not supported',
                            'title' => 'Failed to add user want school',
                            'detail' => 'No information is provided.'
                        )
                    )
                ), 200);
            }

            // Create a new school with school_name
            $insert_school_array['display_language_id'] = $this -> rest -> language_id;
            $insert_school_array['school_name'] = $school_name;

            $school_id = $this -> school_api_model -> insert_school($insert_school_array);
        }

        // Create new record with school_id
        $insert_user_want_school_array['user_id'] = $this -> rest -> user_id;
        $insert_user_want_school_array['school_id'] = $school_id;

        $user_want_school_id = $this -> user_want_school_api_model -> insert_user_want_school($insert_user_want_school_array);

        // Get created record
        $user_want_school = $this -> user_want_school_api_model -> get_user_want_school($user_want_school_id);

        if (empty($user_want_school)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to add user want school',
                        'detail' => 'Failed to insert new record into db.'
                    )
                )
            ), 200);
        }

        $this -> response(array(
            'data' => $user_want_school
        ), 200);
    }

    public function dates_user_want_schools_delete($user_want_school_id) {

        if (empty($user_want_school_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to delete user want school',
                        'detail' => 'User want school id is not provided.'
                    )
                )
            ), 200);
        }

        $this -> user_want_school_api_model -> delete_user_want_school($user_want_school_id);

        $this -> response(array(
            'meta' => array(
                'status' => TRUE,
                'detail' => 'User want school is removed successfully.'
            )
        ), 200);
    }

    public function dates_user_want_school_subjects_post() {

        $want_school_subject_id = $this -> post('want_school_subject_id');

        if (empty($want_school_subject_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to add user want school subject',
                        'detail' => 'No information is provided.'
                    )
                )
            ), 200);
        }

        $insert_array['user_id'] = $this -> rest -> user_id;
        $insert_array['school_subject_id'] = $want_school_subject_id;

        // Create new record
        $user_want_school_subject_id = $this -> user_want_school_subject_api_model -> insert_user_want_school_subject($insert_array);

        // Get created record
        $user_want_school_subject = $this -> user_want_school_subject_api_model -> get_user_want_school_subject($user_want_school_subject_id);

        $this -> response(array(
            'data' => $user_want_school_subject
        ), 200);
    }

    public function dates_user_want_school_subjects_delete($user_want_school_subject_id) {

        if (empty($user_want_school_subject_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to delete user want school subject',
                        'detail' => 'User want school subject id is not provided.'
                    )
                )
            ), 200);
        }

        $this -> user_want_school_subject_api_model -> delete_user_want_school_subject($user_want_school_subject_id);

        $this -> response(array(
            'meta' => array(
                'status' => TRUE,
                'detail' => 'User want school subject is removed successfully.'
            )
        ), 200);
    }

    public function dates_user_want_companies_post() {

        $company_id = $this -> post('company_id');
        $company_name = $this -> post('company_name');

        if (empty($company_id)) {

            if (empty($company_name)) {

                $this -> response(array(
                    'errors' => array(
                        array(
                            'id' => 'Currently not supported',
                            'code' => 'Currently not supported',
                            'title' => 'Failed to add user want company',
                            'detail' => 'No information is provided.'
                        )
                    )
                ), 200);
            }

            // Create a new company with company_name
            $insert_company_array['display_language_id'] = $this -> rest -> language_id;
            $insert_company_array['company_name'] = $company_name;

            $company_id = $this -> company_api_model -> insert_company($insert_company_array);
        }

        // Create new record with company_id
        $insert_user_want_company_array['user_id'] = $this -> rest -> user_id;
        $insert_user_want_company_array['company_id'] = $company_id;

        $user_want_company_id = $this -> user_want_company_api_model -> insert_user_want_company($insert_user_want_company_array);

        // Get created record
        $user_want_company = $this -> user_want_company_api_model -> get_user_want_company($user_want_company_id);

        if (empty($user_want_company)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to add user want company',
                        'detail' => 'Failed to insert new record into db.'
                    )
                )
            ), 200);
        }

        $this -> response(array(
            'data' => $user_want_company
        ), 200);
    }

    public function dates_user_want_companies_delete($user_want_company_id) {

        if (empty($user_want_company_id)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to delete user want company',
                        'detail' => 'User want company id is not provided.'
                    )
                )
            ), 200);
        }

        $this -> user_want_company_api_model -> delete_user_want_company($user_want_company_id);

        $this -> response(array(
            'meta' => array(
                'status' => TRUE,
                'detail' => 'User want company is removed successfully.'
            )
        ), 200);
    }

    public function account_get() {

        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $user_membership_options = $this -> user_membership_option_api_model -> get_user_membership_options_by_user_id($this -> rest -> user_id);
        $account_statuses = $this -> account_status_api_model -> get_account_statuses($this -> rest -> language_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user,
                'relationships' => array(
                    'user_membership_options' => $user_membership_options
                )
            ),
            'included' => array(
                'account_statuses' => $account_statuses
            )
        ), 200);
    }

    public function account_post() {

        $account_status_id = $this -> post('account_status_id');
        $display_language_id = $this -> post('display_language_id');
        $want_pn_date_invite = $this -> post('want_pn_date_invite');
        $want_pn_new_applicant = $this -> post('want_pn_new_applicant');
        $want_pn_chat_message = $this -> post('want_pn_chat_message');
        $want_pn_date_cancellation = $this -> post('want_pn_date_cancellation');

        // Build update array
        if (!empty($account_status_id))
            $update_array['account_status_id'] = $account_status_id;

        if (!empty($display_language_id))
            $update_array['last_display_language_id'] = $display_language_id;

        if ($want_pn_date_invite === '0' || !empty($want_pn_date_invite))
            $update_array['want_pn_date_invite'] = $want_pn_date_invite;

        if ($want_pn_new_applicant === '0' || !empty($want_pn_new_applicant))
            $update_array['want_pn_new_applicant'] = $want_pn_new_applicant;

        if ($want_pn_chat_message === '0' || !empty($want_pn_chat_message))
            $update_array['want_pn_chat_message'] = $want_pn_chat_message;

        if ($want_pn_date_cancellation === '0' || !empty($want_pn_date_cancellation))
            $update_array['want_pn_date_cancellation'] = $want_pn_date_cancellation;

        // Update user data
        if (!empty($update_array)) {

            $this -> user_api_model -> update_user($this -> rest -> user_id, $update_array);
        }

        if (empty($update_array)) {

            $this -> response(array(
                'errors' => array(
                    array(
                        'id' => 'Currently not supported',
                        'code' => 'Currently not supported',
                        'title' => 'Failed to update \'account\' settings',
                        'detail' => 'No update information is provided.'
                    )
                )
            ), 200);
        }

        // Get updated user data
        $user = $this -> user_api_model -> get_user($this -> rest -> user_id);

        $this -> response(array(
            'data' => array(
                'attributes' => $user
            )
        ), 200);
    }

}