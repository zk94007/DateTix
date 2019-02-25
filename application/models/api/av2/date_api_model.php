<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Date_api_model extends  CI_Model {

    public function insert_date($insert_array) {

        $this -> db -> insert('date', $insert_array);
        return $this -> db -> insert_id();
    }

    public function get_date($date_id) {

        $this -> db -> where('date_id', $date_id);
        $dates = $this -> db -> get('date');

        if ($dates -> num_rows() > 0) {
            return $dates -> row();
        }

        return NULL;
    }

    public function get_upcoming_dates_by_merchant_id($merchant_id) {

        $this -> db -> where('merchant_id', $merchant_id);
        $this -> db -> where('date_time >', SQL_DATETIME);
        $result = $this -> db -> get('date');

        return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
    }

    public function get_past_dates_by_merchant_id($merchant_id) {

        $this -> db -> where('merchant_id', $merchant_id);
        $this -> db -> where('date_time <=', SQL_DATETIME);
        $result = $this -> db -> get('date');

        return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
    }

    public function update_date($date_id, $update_array) {

        $this -> db -> where('date_id', $date_id);
        $this -> db -> update('date', $update_array);
    }

    public function get_last_date_by_user_id($user_id) {

        $this -> db -> where('requested_user_id', $user_id);
        $this -> db -> order_by('post_time', 'DESC');
        $this -> db -> limit(1);
        $result = $this -> db -> get('date');

        return $result -> num_rows() > 0 ? $result -> row() : NULL;
    }

    public function get_12_hrs_upcoming_dates() {

        $this -> db -> where('date.date_time >', date("Y-m-d h:i:s", strtotime("+5 minutes")));
        $this -> db -> where('date.date_time <=', date("Y-m-d h:i:s", strtotime("+12 hours")));
        $this -> db -> where('date.completed_step >=', REQUIRED_DATE_COMPLETED_STEP);
        $this -> db -> where('date.status >=', 1);
        $result = $this -> db -> get('date');

        return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
    }

    public function find_date_ids($user_id, $sort_by_distance = 0, $limit = 0, $offset = 0) {

        // Load models
        $CI = & get_instance();
        $CI -> load -> model('api/av2/user_api_model');
        $CI -> load -> model('api/av2/user_preferred_date_type_api_model');
        $CI -> load -> model('api/av2/user_want_body_type_api_model');
        $CI -> load -> model('api/av2/user_want_child_plan_api_model');
        $CI -> load -> model('api/av2/user_want_child_status_api_model');
        $CI -> load -> model('api/av2/user_want_company_api_model');
        $CI -> load -> model('api/av2/user_want_descriptive_word_api_model');
        $CI -> load -> model('api/av2/user_want_drinking_status_api_model');
        $CI -> load -> model('api/av2/user_want_education_level_api_model');
        $CI -> load -> model('api/av2/user_want_ethnicity_api_model');
        $CI -> load -> model('api/av2/user_want_exercise_frequency_api_model');
        $CI -> load -> model('api/av2/user_want_gender_api_model');
        $CI -> load -> model('api/av2/user_want_relationship_status_api_model');
        $CI -> load -> model('api/av2/user_want_relationship_type_api_model');
        $CI -> load -> model('api/av2/user_want_religious_belief_api_model');
        $CI -> load -> model('api/av2/user_want_residence_type_api_model');
        $CI -> load -> model('api/av2/user_want_school_api_model');
        $CI -> load -> model('api/av2/user_want_smoking_status_api_model');
        $CI -> load -> model('api/av2/date_decision_api_model');

        $user = $CI -> user_api_model -> get_user($user_id);

        // Get all user settings at first
        $preferred_date_type_ids = $CI -> user_preferred_date_type_api_model -> get_date_type_ids_by_user_id($user_id);
        $want_relationship_type_ids = $CI -> user_want_relationship_type_api_model -> get_relationship_type_ids_by_user_id($user_id);
        $want_gender_ids = $CI -> user_want_gender_api_model -> get_gender_ids_by_user_id($user_id);
        $want_ethnicity_ids = $CI -> user_want_ethnicity_api_model -> get_ethnicity_ids_by_user_id($user_id);
        $want_body_type_ids = $CI -> user_want_body_type_api_model -> get_body_type_ids_by_user_id($user_id);
        $want_child_plan_ids = $CI -> user_want_child_plan_api_model -> get_child_plan_ids_by_user_id($user_id);
        $want_child_status_ids = $CI -> user_want_child_status_api_model -> get_child_status_ids_by_user_id($user_id);
        $want_company_ids = $CI -> user_want_company_api_model -> get_company_ids_by_user_id($user_id);
        $want_descriptive_word_ids = $CI -> user_want_descriptive_word_api_model -> get_descriptive_word_ids_by_user_id($user_id);
        $want_drinking_status_ids = $CI -> user_want_drinking_status_api_model -> get_drinking_status_ids_by_user_id($user_id);
        $want_education_level_ids = $CI -> user_want_education_level_api_model -> get_education_level_ids_by_user_id($user_id);
        $want_exercise_frequency_ids = $CI -> user_want_exercise_frequency_api_model -> get_exercise_frequency_ids_by_user_id($user_id);
        $want_relationship_status_ids = $CI -> user_want_relationship_status_api_model -> get_relationship_status_ids_by_user_id($user_id);
        $want_religious_belief_ids = $CI -> user_want_religious_belief_api_model -> get_religious_belief_ids_by_user_id($user_id);
        $want_residence_type_ids = $CI -> user_want_residence_type_api_model -> get_residence_type_ids_by_user_id($user_id);
        $want_school_ids = $CI -> user_want_school_api_model -> get_school_ids_by_user_id($user_id);
        $want_smoking_status_ids = $CI -> user_want_smoking_status_api_model -> get_smoking_status_ids_by_user_id($user_id);
        $user_date_decisions = $CI -> date_decision_api_model -> get_date_decisions_by_user_id($user_id);

        // Select date ids
        $this -> db -> distinct();
        $this -> db -> select('date.date_id, date.date_max_distance, merchant.gps_lat, merchant.gps_long');

        // Join tables
        $this -> db -> join('user as host_user', 'host_user.user_id = date.requested_user_id');
        $this -> db -> join('merchant', 'date.merchant_id = merchant.merchant_id');

        /* ========================== Start commented ========================
         * Filters below are disabled according to defined "Find Dates" logic.

        // Filter dates by user_want_body_types
        if (!empty($want_body_type_ids))
            $this -> db -> where_in('host_user.body_type_id', $want_body_type_ids);

        // Filter dates by user_want_child_plans
        if (!empty($want_child_plan_ids))
            $this->db->where_in('host_user.child_plan_id', $want_child_plan_ids);

        // Filter dates by user_want_child_statuses
        if (!empty($want_child_status_ids))
            $this -> db -> where_in('host_user.child_status_id', $want_child_status_ids);

        // Filter dates by user_want_companies
        if (!empty($want_company_ids)) {
            $this -> db -> join('user_job AS host_user_company', 'host_user_company.user_id = host_user.user_id');
            $this -> db -> where_in('host_user_company.company_id', $want_company_ids);
        }

        // Filter dates by user_want_descriptive_words
        if (!empty($want_descriptive_word_ids)) {
            $this -> db -> join('user_descriptive_word AS host_user_descriptive_word', 'host_user_descriptive_word.user_id = host_user.user_id');
            $this -> db -> where_in('host_user_descriptive_word.descriptive_word_id', $want_descriptive_word_ids);
        }

        // Filter dates by user_want_drinking_statuses
        if (!empty($want_drinking_status_ids))
            $this -> db -> where_in('host_user.drinking_status_id', $want_drinking_status_ids);

        // Filter dates by user_want_education_levels
        if (!empty($want_education_level_ids)) {
            $this -> db -> join('user_education_level AS host_user_education_level', 'host_user_education_level.user_id = host_user.user_id');
            $this -> db -> where_in('host_user_education_level.education_level_id', $want_education_level_ids);
        }

        // Filter dates by user_want_exercise_frequencies
        if (!empty($want_exercise_frequency_ids))
            $this -> db -> where_in('host_user.exercise_frequency_id', $want_exercise_frequency_ids);

        // Filter dates by user_want_relationship_statuses
        if (!empty($want_relationship_status_ids))
            $this -> db -> where_in('host_user.relationship_status_id', $want_relationship_status_ids);

        // Filter dates by user_want_religious_beliefs
        if (!empty($want_religious_belief_ids))
            $this -> db -> where_in('host_user.religious_belief_id', $want_religious_belief_ids);

        // Filter dates by user_want_residence_types
        if (!empty($want_residence_type_ids))
            $this -> db -> where_in('host_user.residence_type', $want_residence_type_ids);

        // Filter dates by user_want_schools
        if (!empty($want_school_ids)) {
            $this -> db -> join('user_school AS host_user_school', 'host_user_school.user_id = host_user.user_id');
            $this -> db -> where_in('host_user_school.school_id', $want_school_ids);
        }

        // Filter dates by user_want_smoking_statuses
        if (!empty($want_smoking_status_ids))
            $this -> db -> where_in('host_user.smoking_status_id', $want_smoking_status_ids);

        ========================== End commented ======================== */

        // Filter dates by user_want_genders
        if (!empty($want_gender_ids))
            $this -> db -> where_in('host_user.gender_id', $want_gender_ids);

        // Filter dates by date_gender_ids
        if (!empty($user -> gender_id))
            $this -> db -> where("date.date_gender_ids REGEXP '[[:<:]]". $user -> gender_id ."[[:>:]]'");

        /* COMMENT BY MICHAEL - Not Req Now
        // Filter dates by user_want_age_range
        if (!empty($user -> want_age_range_lower))
            $this -> db -> where('TIMESTAMPDIFF(YEAR, host_user.birth_date, CURDATE()) >=', $user -> want_age_range_lower);

        if (!empty($user -> want_age_range_upper))
            $this -> db -> where('TIMESTAMPDIFF(YEAR, host_user.birth_date, CURDATE()) <=', $user -> want_age_range_upper);
        */

        // Filter dates by date_age_range (Not used for now)
        /*if (!empty($user -> birth_date)) {
            $this->db->where('TIMESTAMPDIFF(YEAR, \'' . $user->birth_date . '\', CURDATE()) >=', 'date.age_range_lower', FALSE);
            $this->db->where('TIMESTAMPDIFF(YEAR, \'' . $user->birth_date . '\', CURDATE()) <=', 'date.age_range_upper', FALSE);
        }*/

        /* COMMENT BY MICHAEL - Not Req Now
        // Filter dates by user_want_ethnicities
        if (!empty($want_ethnicity_ids))
            $this -> db -> where_in('host_user.ethnicity_id', $want_ethnicity_ids);

        */

        // Filter dates by date_ethnicity_ids (Not used for now)
        /*if (!empty($user -> ethnicity_id))
            $this->db->where("date.date_ethnicity_ids REGEXP '[[:<:]]" . $user->ethnicity_id . "[[:>:]]'");*/

        // Filter dates by user_preferred_date_types
        if (!empty($preferred_date_type_ids))
            $this -> db -> where_in('date.date_type_id', $preferred_date_type_ids);

        // Filter dates by user_want_relationship_types
        if (!empty($want_relationship_type_ids))
            $this -> db -> where_in('date.date_relationship_type_id', $want_relationship_type_ids);

        // Filter dates by necessary conditions
        $this -> db -> where('host_user.user_id !=', $user_id);
        $this -> db -> where('date.date_time >', date("Y-m-d h:i:s", strtotime("+15 minutes")));
        $this -> db -> where('date.completed_step >=', REQUIRED_DATE_COMPLETED_STEP);
        $this -> db -> where('date.status >=', 1);

        // [INFO] Commented this in order to show all available upcoming dates in Find Date screen
        /*
        // Exclude dates that are already decided
        if (!empty($user_date_decisions)) {

            $decided_date_ids = array();
            foreach ($user_date_decisions as $date_decision) {

                $decided_date_ids[] = $date_decision['date_id'];
            }

            $this -> db -> where_not_in('date.date_id', $decided_date_ids);
        }
        */

        $this -> db -> order_by('date.date_time', 'ASC');

        if (!empty($limit))
            $this -> db -> limit($limit, $offset);

        $result = $this -> db -> get('date');

        // Return list of date_ids
        if ($result -> num_rows() > 0) {

            $result_array = $result -> result_array();

            $date_type_ids = array();
            foreach($result_array as $row) {

                if ($sort_by_distance == 1) {

                    if (!empty($user -> gps_lat) &&
                        !empty($user -> gps_lng) &&
                        !empty($row['gps_lat']) &&
                        !empty($row['gps_long']) &&
                        !empty($user -> want_max_date_distance) &&
                        !empty($row['date_max_distance'])) {

                        // Check if the date is in the want_date_distance
                        $distance = $CI -> user_api_model -> distance_between_two_points($user -> gps_lat, $user -> gps_lng, $row['gps_lat'], $row['gps_long'], 'K');

                        if ($distance <= $user -> want_max_date_distance &&
                            $distance <= $row['date_max_distance']) {

                            unset($date_type_id);
                            $date_type_id['date_id'] = $row['date_id'];
                            $date_type_id['distance'] = $distance;

                            $date_type_ids[] = $date_type_id;
                        }
                    }
                } else {

                    $date_type_ids[] = $row['date_id'];
                }
            }

            if ($sort_by_distance == 1) {
                // Sort by distance
                usort($date_type_ids, function ($a, $b) {
                    if ($a['distance'] > $b['distance']) {
                        return 1;
                    } else if ($a['distance'] < $b['distance']) {
                        return -1;
                    }
                    return 0;
                });

                return array_map(function ($date_type_id) {
                    return $date_type_id['date_id'];
                }, $date_type_ids);

            } else {
                return $date_type_ids;
            }
        }

        return NULL;
    }

    public function get_my_dates($user_id, $limit = 0, $offset = 0, $is_upcoming = TRUE, $my_hosts = TRUE) {

        $this -> db -> select('date.*');

        $this -> db -> where('date.completed_step >=', REQUIRED_DATE_COMPLETED_STEP);
        $this -> db -> where('date.status != -1');

        if ($is_upcoming == TRUE) {
            $this -> db -> where('date.date_time >=', SQL_DATETIME);
        } else {
            $this -> db -> where('date.date_time <', SQL_DATETIME);
        }

        if ($my_hosts == TRUE) {
            $this -> db -> where('date.requested_user_id', $user_id);
        } else {
            $this -> db -> join('date_applicant', 'date_applicant.date_id = date.date_id', 'left');
            $this -> db -> where("(date_applicant.applicant_user_id = {$user_id} AND date_applicant.status = 1)", NULL, FALSE);
        }

        $this -> db -> group_by('date.date_id');
        $this -> db -> order_by('date.date_time', 'DESC');

        if (!empty($limit))
            $this -> db -> limit($limit, $offset);

        $result = $this -> db -> get('date');

        $my_dates = array();

        if ($result -> num_rows() > 0) {

            $CI = & get_instance();

            $CI -> load -> model('api/av2/date_decision_api_model');
            $CI -> load -> model('api/av2/date_payer_api_model');
            $CI -> load -> model('api/av2/date_type_api_model');
            $CI -> load -> model('api/av2/date_applicant_api_model');
            $CI -> load -> model('api/av2/merchant_api_model');
            $CI -> load -> model('api/av2/relationship_type_api_model');
            $CI -> load -> model('api/av2/user_api_model');
            $CI -> load -> model('api/av2/user_follow_date_api_model');
            $CI -> load -> model('api/av2/user_photo_api_model');

            foreach ($result -> result_array() as $date) {

                $requested_user = $CI -> user_api_model -> get_user($date['requested_user_id']);
                $requested_user_photos = $CI -> user_photo_api_model -> get_user_photos_by_user_id($requested_user -> user_id);
                $date_applicants = $CI -> date_applicant_api_model -> get_date_applicants_by_date_id($date['date_id']);
                $date_type = $CI -> date_type_api_model -> get_date_type($date['date_type_id']);
                $relationship_type = $CI -> relationship_type_api_model -> get_relationship_type($date['date_relationship_type_id']);
                $date_payer = $CI -> date_payer_api_model -> get_date_payer($date['date_payer_id']);
                $merchant = $CI -> merchant_api_model -> get_merchant_with_photo($date['merchant_id']);
                $date_decisions = $CI -> date_decision_api_model -> get_date_decisions_by_date_id($date['date_id']);
                $follow_time = $CI -> user_follow_date_api_model -> get_follow_time_with_params($user_id, $date['date_id']);

                unset($my_date);
                $my_date['attributes'] = $date;
                $my_date['attributes']['views_count'] = count($date_decisions);

                $my_date['relationships']['date_type'] = $date_type;
                $my_date['relationships']['relationship_type'] = $relationship_type;
                $my_date['relationships']['date_payer'] = $date_payer;
                $my_date['relationships']['merchant'] = $merchant;

                $my_date['relationships']['requested_user']['attributes'] = $requested_user;
                if (!empty($requested_user_photos))
                    $my_date['relationships']['requested_user']['relationships']['user_photos'] = $requested_user_photos;

                if (!empty($date_applicants)) {
                    $date_applicant_objects = array();

                    foreach ($date_applicants as $date_applicant) {

                        $applicant_user = $CI -> user_api_model -> get_user($date_applicant['applicant_user_id']);

                        unset($date_applicant_object);
                        $date_applicant_object['attributes'] = $date_applicant;
                        $date_applicant_object['relationships']['applicant_user']['attributes'] = $applicant_user;

                        $date_applicant_objects[] = $date_applicant_object;
                    }
                    $my_date['relationships']['date_applicants'] = $date_applicant_objects;
                }

                $my_date['meta']['follow_time'] = $follow_time;

                $my_dates[] = $my_date;
            }
        }

        return $my_dates;
    }

    public function get_dates_in_date_time_interval($date_time_str, $user_id) {

        // Date time interval
        $date_time_interval_minutes = 120;

        $sql = "SELECT * FROM date
                WHERE requested_user_id = {$user_id}
                AND completed_step >= " . REQUIRED_DATE_COMPLETED_STEP .
                " AND status = 1" .
                " AND date_time BETWEEN '" . date('Y-m-d H:i:s',strtotime($date_time_str)) . "' - INTERVAL {$date_time_interval_minutes} MINUTE" .
                " AND '" . date('Y-m-d H:i:s',strtotime($date_time_str)) . "' + INTERVAL {$date_time_interval_minutes} MINUTE";

        $result = $this -> db -> query($sql);

        return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
    }

    public function get_first_date_between_users($user_id, $friend_id) {

        return $this -> get_one_date_between_users($user_id, $friend_id, FALSE);
    }

    public function get_last_date_between_users($user_id, $friend_id) {

        return $this -> get_one_date_between_users($user_id, $friend_id, TRUE);
    }

    private function get_one_date_between_users($user_id, $friend_id, $last = TRUE) {

        $this -> db -> select('date.*');

        $this -> db -> join('date_applicant', 'date_applicant.date_id = date.date_id');

        $this -> db -> where('date.completed_step >=', REQUIRED_DATE_COMPLETED_STEP);
        $this -> db -> where("(date_applicant.applicant_user_id = {$user_id} OR date.requested_user_id = {$user_id})");
        $this -> db -> where("(date_applicant.applicant_user_id = {$friend_id} OR date.requested_user_id = {$friend_id})");

        if ($last == TRUE)
            $this -> db -> order_by('date.date_time', 'DESC');
        else
            $this -> db -> order_by('date.date_time', 'ASC');

        $this -> db -> limit(1);

        $result = $this -> db -> get('date');

        return $result -> num_rows() > 0 ? $result -> row() : NULL;
    }

    public function get_applied_dates_with_params($requested_user_id, $target_user_id) {

        $this -> db -> select('date.*, date_applicant.is_chosen');

        $this -> db -> join('date_applicant', 'date.date_id = date_applicant.date_id');

        $this -> db -> where('date.requested_user_id', $requested_user_id);
        $this -> db -> where('date_applicant.applicant_user_id', $target_user_id);

        $result = $this -> db -> get('date');

        return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
    }
}