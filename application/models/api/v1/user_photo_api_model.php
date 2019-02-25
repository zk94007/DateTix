<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Photo_api_model extends CI_Model {

	public function get_user_photos_by_user_id($user_id){

		$image_data = array();

		$this->db->where('user_id', $user_id);
		$this->db->order_by('set_primary','desc');
		$result = $this->db->get('user_photo');
		$result = $result->result_array();
		if($result)
		{
			foreach ($result as $key=>$value) {
				$image_data[$key]  = $value;
				$image_data[$key]['url'] = base_url() . "user_photos/user_$user_id/" . $value['photo'];
			}
		}

		return $image_data;
	}

	public function insert_user_photo($insert_array) {

		$this -> db -> insert('user_photo', $insert_array);
		return $this -> db -> insert_id();
	}

	public function insert_user_photo_with_upload_data($user_id, $data){

		$existing_photos_count  = $this->check_user_photo_exist_by_user_id($user_id);

		if($existing_photos_count == 0) {

			$data = array('user_id'=>$user_id,'set_primary'=>'1','photo'=>$data['file_name'],'uploaded_time'=>SQL_DATETIME);
		} else {
			$data = array('user_id'=>$user_id,'photo'=>$data['file_name'],'uploaded_time'=>SQL_DATETIME);
		}

		$this->db->insert('user_photo',$data);

		return $this->db->insert_id();
	}

	public function get_user_photo($user_photo_id) {

		$this -> db -> where('user_photo_id', $user_photo_id);
		$result = $this -> db -> get('user_photo');
		return $result -> num_rows() > 0 ? $result -> row() : NULL;
	}

	public function update_user_photo($user_photo_id, $update_array) {

		$this -> db -> where('user_photo_id', $user_photo_id);
		$this -> db -> update('user_photo', $update_array);
	}

	public function delete_user_photo($user_photo_id, $user_id)
	{
		$this->db->where('user_id', $user_id);
		$this->db->from('user_photo');
		$user_photos = $this->db->count_all_results();
		if($user_photos > 1)
		{
			$this->db->where('user_photo_id', $user_photo_id);
			$q = $this->db->get('user_photo', 1);

			if ($q->num_rows() > 0) {
				$user_photo = $q->row_array();

				// remove image from stroage
				$photo_path = './user_photos/user_' . $user_id . '/' . $user_photo['photo'];
				if (file_exists($photo_path)) {
					unlink($photo_path);
				}

				// remove database entry
				$this->db->limit(1);
				$this->db->where('user_photo_id', $user_photo_id);
				$this->db->delete('user_photo');

				return '1';
			}
		}
		return "0";
	}

	public function create_user_photos_folder($user_id) {
		$folder_name         = "user_".$user_id;
		$path_to_upload       = "user_photos/$folder_name/";

		if ( ! file_exists($path_to_upload) )
		{
			$create = mkdir($path_to_upload, 0777, $recursive = TRUE);
			if ( ! $create)
				return;
		}
		return $path_to_upload;
	}

	public function check_user_photo_exist_by_user_id($user_id){
		$this->db->where('user_id',$user_id);
		$result = $this->db->count_all_results('user_photo');
		return $result;
	}

	public function has_user_photo_id($user_id, $user_photo_id) {

		$this -> db -> where('user_id', $user_id);
		$this -> db -> where('user_photo_id', $user_photo_id);
		$result = $this -> db -> get('user_photo');

		return $result -> num_rows() > 0 ? TRUE : FALSE;
	}

	public function set_primary_photo_with_params($user_id, $user_photo_id) {

		// Clear 'set_primary' flag for user photos
		$this -> db -> where('user_id', $user_id);
		$this -> db -> update('user_photo', array('set_primary' => 0));

		// Set primary photo
		$update_array['set_primary'] = 1;
		$this -> update_user_photo($user_photo_id, $update_array);
	}
}
