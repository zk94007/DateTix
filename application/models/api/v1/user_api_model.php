<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_api_model extends CI_Model {

    public function get_user($user_id) {

        $this -> db -> where('user_id', $user_id);
        return $this->db->get('user')->row();
    }

    public function insert_user($insert_array) {

        $this -> db -> insert('user', $insert_array);
        return $this -> db -> insert_id();
    }

    public function update_user($user_id, $update_array) {

        $this -> db -> where('user_id', $user_id);
        $this -> db -> update('user', $update_array);
    }

    public function is_email_address_exists($email) {

        return $this -> db -> where('email_address', $email) -> count_all_results('user_email') > 0;
    }

    public function is_mobile_number_exists($mobile_international_code, $mobile_phone_number) {

        $this -> db -> where('mobile_international_code', $mobile_international_code);
        $this -> db -> where('mobile_phone_number', $mobile_phone_number);

        return $this -> db -> count_all_results('user') > 0;
    }

    public function is_facebook_id_exists($facebook_id) {

        return $this -> db -> where('facebook_id', $facebook_id) -> count_all_results('user') > 0;
    }

    public function get_user_by_facebook_id($facebook_id) {

        $this -> db -> where('facebook_id', $facebook_id);
        $result = $this -> db -> get('user');

        return $result -> num_rows() > 0 ? $result -> row() : NULL;
    }

    public function get_user_by_mobile_number($mobile_international_code, $mobile_phone_number){

        $this -> db -> where('mobile_international_code', $mobile_international_code);
        $this -> db -> where('mobile_phone_number', $mobile_phone_number);
        $q  = $this -> db -> get('user');

        return ($q->num_rows() > 0) ? $q->row() : NULL ;
    }

    public function find_profile_matches($user_id, $limit = 0) {

        $CI = & get_instance();

        $CI -> load -> model('api/v1/user_decision_api_model');

        // Excluded user ids
        $excluded_user_ids = array();
        $user_decisions = $CI -> user_decision_api_model -> get_user_decisions_by_user_id($user_id);
        foreach ($user_decisions as $user_decision) {
            $excluded_user_ids[] = $user_decision['target_user_id'];
        }
        $excluded_user_ids[] = $user_id;
        $excluded_user_ids_string = implode(',', $excluded_user_ids);

        // Get user
        $user = $this -> get_user($user_id);

        $this -> db -> select('user.*');

        $this -> db -> join('user_email', 'user_email.user_id = user.user_id AND user_email.is_contact = 1 AND user_email.is_verified = 1');
        $this -> db -> join('user_want_gender', "user_want_gender.user_id = user.user_id AND user_want_gender.gender_id = {$user -> gender_id}");
        $this -> db -> join('user_want_ethnicity', "user_want_ethnicity.user_id = user.user_id AND user_want_ethnicity.ethnicity_id = {$user -> ethnicity_id}");

        if (!empty($user -> birth_date))
            $this -> db -> where("user.want_age_range_lower <= TIMESTAMPDIFF(YEAR, '{$user -> birth_date}',CURDATE()) AND TIMESTAMPDIFF(YEAR, '{$user -> birth_date}',CURDATE()) <= user.want_age_range_upper");

        $this -> db -> where("user.user_id NOT IN ({$excluded_user_ids_string})");

        $this -> db -> order_by('RAND()');

        if (!empty($limit))
            $this -> db -> limit($limit);

        $result = $this -> db -> get('user');

        return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
    }

    public function find_friends_near_user($user_id) {

        $user = $this -> get_user($user_id);

        // Get all users with valid gps_lat and gps_lng
        $this -> db -> where('gps_lat !=', 0);
        $this -> db -> where('gps_lng !=', 0);
        $this -> db -> where('user_id !=', $user_id);

        $result = $this -> db -> get('user');

        $friends_near_user = array();

        if ($result -> num_rows() > 0) {

            $friends = $result -> result_array();

            foreach ($friends as $friend) {

                if (!empty($user -> gps_lat) &&
                        !empty($user -> gps_lng) &&
                        !empty($friend['gps_lat']) &&
                        !empty($friend['gps_lng'])) {

                    $distance = $this -> distance_between_two_points($user -> gps_lat, $user -> gps_lng, $friend['gps_lat'], $friend['gps_lng'], 'K');

                    if ($distance <= 5) {
                        $friends_near_user[] = $friend;
                    }
                }
            }
        }

        return $friends_near_user;
    }

    public function find_inactive_users() {

        $this -> db -> where("DATEDIFF(NOW(), last_active_time) >= 3");
        $this -> db -> where("(last_inactive_status_remind_time IS NULL OR DATEDIFF(NOW(), last_inactive_status_remind_time) >= 3)");

        $result = $this -> db -> get('user');

        return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
    }

    public function find_people_ids($user_id, $sort_by_distance = 0, $limit = 0, $offset = 0) {

        // Get user
        $user = $this -> get_user($user_id);

        // Load models
        $CI = & get_instance();
        $CI -> load -> model('api/v1/user_want_ethnicity_api_model');
        $CI -> load -> model('api/v1/user_want_gender_api_model');
        $CI -> load -> model('api/v1/user_decision_api_model');

        // Get all user settings at first
        $want_ethnicity_ids = $CI -> user_want_ethnicity_api_model -> get_ethnicity_ids_by_user_id($user_id);
        $want_gender_ids = $CI -> user_want_gender_api_model -> get_gender_ids_by_user_id($user_id);
        $user_decisions = $CI -> user_decision_api_model -> get_user_decisions_by_user_id($user_id);

        // Select user ids
        $this -> db -> distinct();
        $this -> db -> select('user.user_id, user.gps_lat, user.gps_lng');

        // Filter people by checking verified email address
        $this -> db -> join('user_email','user_email.user_id = user.user_id AND user_email.is_contact = 1 AND user_email.is_verified = 1');

        // Filter people by user_want_ethnicities
        if (!empty($want_ethnicity_ids))
            $this -> db -> where_in('user.ethnicity_id', $want_ethnicity_ids);

        // Filter people by their_want_ethnicities
        if (!empty($user -> ethnicity_id)) {
            $this -> db -> join('user_want_ethnicity', 'user_want_ethnicity.user_id = user.user_id');
            $this -> db -> where('user_want_ethnicity.ethnicity_id', $user -> ethnicity_id);
        }

        // Filter people by user_want_genders
        if (!empty($want_gender_ids))
            $this -> db -> where_in('user.gender_id', $want_gender_ids);

        // Filter people by their_want_genders
        if (!empty($user -> gender_id)) {
            $this -> db -> join('user_want_gender', 'user_want_gender.user_id = user.user_id');
            $this -> db -> where('user_want_gender.gender_id', $user -> gender_id);
        }

        // Filter people by user_want_age_range
        if (!empty($user -> want_age_range_lower))
            $this -> db -> where('TIMESTAMPDIFF(YEAR, user.birth_date, CURDATE()) >=', $user -> want_age_range_lower);

        if (!empty($user -> want_age_range_upper))
            $this -> db -> where('TIMESTAMPDIFF(YEAR, user.birth_date, CURDATE()) <=', $user -> want_age_range_upper);

        // Filter people by their_want_age_range
        if (!empty($user -> birth_date)) {
            $this->db->where('TIMESTAMPDIFF(YEAR, \'' . $user->birth_date . '\', CURDATE()) >=', 'user.want_age_range_lower', FALSE);
            $this->db->where('TIMESTAMPDIFF(YEAR, \'' . $user->birth_date . '\', CURDATE()) <=', 'user.want_age_range_upper', FALSE);
        }

        // Exclude users that are already decided through dates
        if (!empty($user_decisions)) {

            $decided_user_ids = array();
            foreach ($user_decisions as $user_decision) {

                $decided_user_ids[] = $user_decision['target_user_id'];
            }

            $this -> db -> where_not_in('user.user_id', $decided_user_ids);
        }

        // Filter people by necessary conditions
        $this -> db -> where('user.user_id !=', $user_id);

        $this -> db -> order_by('user.last_active_time', 'DESC');

        if (!empty($limit))
            $this -> db -> limit($limit, $offset);

        $result = $this -> db -> get('user');

        // Return list of user_ids
        if ($result -> num_rows() > 0) {

            $result_array = $result -> result_array();

            $people_ids = array();
            foreach($result_array as $row) {

                if ($sort_by_distance == 1) {

                    if (!empty($user -> gps_lat) &&
                        !empty($user -> gps_lng) &&
                        !empty($row['gps_lat']) &&
                        !empty($row['gps_lng']) &&
                        !empty($user -> want_max_people_distance)) {

                        // Check if the person is in the want_people_distance
                        $distance = $this -> distance_between_two_points($user -> gps_lat, $user -> gps_lng, $row['gps_lat'], $row['gps_lng'], 'K');

                        if ($distance <= $user -> want_max_people_distance) {

                            unset($people_id);
                            $people_id['user_id'] = $row['user_id'];
                            $people_id['distance'] = $distance;

                            $people_ids[] = $people_id;
                        }
                    }
                } else {

                    $people_ids[] = $row['user_id'];
                }
            }

            if ($sort_by_distance == 1) {
                // Sort by distance
                usort($people_ids, function ($a, $b) {
                    if ($a['distance'] > $b['distance']) {
                        return 1;
                    } else if ($a['distance'] < $b['distance']) {
                        return -1;
                    }
                    return 0;
                });

                return array_map(function ($people_id) {
                    return $people_id['user_id'];
                }, $people_ids);

            } else {
                return $people_ids;
            }
        }

        return NULL;
    }

    // [TODO] This function should be moved to 'libraries'
    /*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
    /*::                                                                         :*/
    /*::  This routine calculates the distance between two points (given the     :*/
    /*::  latitude/longitude of those points). It is being used to calculate     :*/
    /*::  the distance between two locations using GeoDataSource(TM) Products    :*/
    /*::                                                                         :*/
    /*::  Definitions:                                                           :*/
    /*::    South latitudes are negative, east longitudes are positive           :*/
    /*::                                                                         :*/
    /*::  Passed to function:                                                    :*/
    /*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
    /*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
    /*::    unit = the unit you desire for results                               :*/
    /*::           where: 'M' is statute miles (default)                         :*/
    /*::                  'K' is kilometers                                      :*/
    /*::                  'N' is nautical miles                                  :*/
    /*::  Worldwide cities and other features databases with latitude longitude  :*/
    /*::  are available at http://www.geodatasource.com                          :*/
    /*::                                                                         :*/
    /*::  For enquiries, please contact sales@geodatasource.com                  :*/
    /*::                                                                         :*/
    /*::  Official Web site: http://www.geodatasource.com                        :*/
    /*::                                                                         :*/
    /*::         GeoDataSource.com (C) All Rights Reserved 2015		   		     :*/
    /*::                                                                         :*/
    /*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
    function distance_between_two_points($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    public function send_push_notification_to_user($user_id, $message, $meta = '', $debug = FALSE) {

        $user = $this -> get_user($user_id);

        if (empty($user))
            return array(
                'status' => FALSE,
                'detail' => 'User id is invalid.'
            );

        if (empty($user -> device_token))
            return array(
                'status' => FALSE,
                'detail' => 'Device Token does not exist.'
            );

        return $this -> send_push_notification_to_device($user -> device_token, $message, $meta, $debug);
    }

    public function send_push_notification_to_device($device_token, $message, $meta = '', $debug = FALSE) {

        $apns_cert = 'apns-product.pem';
        $apns_host = 'gateway.push.apple.com';

        if ($debug == TRUE) {
            $apns_cert = 'apns-dev.pem';
            $apns_host = 'gateway.sandbox.push.apple.com';
        }

        $pass_phrase = '1111';
        $apns_port = 2195;

        $body['aps'] = array(
            'alert' => $message,
            'badge' => 1,
            'sound' => 'default',
            'meta' => $meta
        );

        $payload = json_encode($body);

        $stream_context = stream_context_create();

        stream_context_set_option($stream_context, 'ssl', 'local_cert', $apns_cert);
        stream_context_set_option($stream_context, 'ssl', 'passphrase', $pass_phrase);

        $apns = stream_socket_client('ssl://' . $apns_host . ':' . $apns_port, $error, $error_string, 2, STREAM_CLIENT_CONNECT, $stream_context);

        if (!$apns)
            return array(
                'status' => FALSE,
                'detail' => "Failed to connect {$error} {$error_string}"
            );

        $apns_message = chr(0) . chr(0) . chr(32) . pack('H*', str_replace(' ', '', $device_token)) . chr(0) . chr(strlen($payload)) . $payload;

        fwrite($apns, $apns_message);
        fclose($apns);

        return array(
            'status' => TRUE,
            'detail' => 'Push Notification is sent successfully.'
        );
    }

}