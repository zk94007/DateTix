<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Delayed_Job_api_model extends CI_Model {

    public function insert_delayed_job($insert_array) {

        $this -> db -> insert('delayed_job', $insert_array);
        return $this -> db -> insert_id();
    }

    public function update_delayed_job($delayed_job_id, $update_array) {

        $this -> db -> where('delayed_job_id', $delayed_job_id);
        $this -> db -> update('delayed_job', $update_array);
    }

    public function delete_delayed_job($delayed_job_id) {

        $this -> db -> where('delayed_job_id', $delayed_job_id);
        $this -> db -> delete('delayed_job');
    }

    public function get_delayed_job($delayed_job_id) {

        $this -> db -> where('delayed_job_id', $delayed_job_id);
        $result = $this -> db -> get('delayed_job');

        return $result -> num_rows() > 0 ? $result -> row() : NULL;
    }

    public function get_first_delayed_job_in_queue() {

        $this -> db -> where('locked', 0);
        $this -> db -> order_by('queue_number');

        $result = $this -> db -> get('delayed_job');

        return $result -> num_rows() > 0 ? $result -> row() : NULL;
    }

    public function reset_last_error_by_id($delayed_job_id) {

        $update_array['last_error'] = NULL;

        $this -> update_delayed_job($delayed_job_id, $update_array);
    }

    public function append_last_error_with_params($delayed_job_id, $error) {

        $delayed_job = $this -> get_delayed_job($delayed_job_id);

        if ($delayed_job) {

            $update_array['last_error'] = $delayed_job -> last_error . $error . "\n\n";

            $this -> update_delayed_job($delayed_job_id, $update_array);
        }
    }

    public function lock_delayed_job_by_id($delayed_job_id) {

        $update_array['locked'] = 1;

        $this -> update_delayed_job($delayed_job_id, $update_array);
    }

    public function unlock_delayed_job_by_id($delayed_job_id) {

        $update_array['locked'] = 0;

        $this -> update_delayed_job($delayed_job_id, $update_array);
    }

    public function increase_attempts_count_by_id($delayed_job_id) {

        $delayed_job = $this -> get_delayed_job($delayed_job_id);

        if ($delayed_job) {

            $update_array['attempts'] = $delayed_job -> attempts + 1;

            $this -> update_delayed_job($delayed_job_id, $update_array);
        }
    }

    public function set_run_at_now_by_id($delayed_job_id) {

        $update_array['run_at'] = SQL_DATETIME;

        $this -> update_delayed_job($delayed_job_id, $update_array);
    }

    public function set_failed_at_now_by_id($delayed_job_id) {

        $update_array['failed_at'] = SQL_DATETIME;

        $this -> update_delayed_job($delayed_job_id, $update_array);
    }

    public function set_updated_at_now_by_id($delayed_job_id) {

        $update_array['updated_at'] = SQL_DATETIME;

        $this -> update_delayed_job($delayed_job_id, $update_array);
    }

    public function get_last_queue_number() {

        $this -> db -> order_by('queue_number', 'DESC');
        $result = $this -> db -> get('delayed_job');

        if ($result -> num_rows() > 0) {
            return $result -> row() -> queue_number;
        } else {
            return 0;
        }
    }

    public function set_queue_number_to_the_last_by_id($delayed_job_id) {

        $update_array['queue_number'] = $this -> get_last_queue_number() + 1;

        $this -> update_delayed_job($delayed_job_id, $update_array);
    }
}
