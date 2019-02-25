<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Chat_api_model extends CI_Model {

    public function insert_user_chat($insert_array) {

        $this -> db -> insert('user_chat', $insert_array);
        return $this -> db -> insert_id();
    }

    public function get_user_chat($user_chat_id) {

        $this -> db -> where('user_chat_id', $user_chat_id);
        $result = $this -> db -> get('user_chat');

        return $result -> num_rows() > 0 ? $result -> row() : NULL;
    }

    public function get_user_chats_by_user_id_friend_id($user_id, $friend_id) {

        $this -> db -> where("(from_user_id = {$user_id} AND to_user_id = {$friend_id})", null, FALSE);
        $this -> db -> or_where("(from_user_id = {$friend_id} AND to_user_id = {$user_id})", null, FALSE);

        $this -> db -> order_by('chat_message_time', 'ASC');

        $result = $this -> db -> get('user_chat');

        return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
    }

	public function get_last_chat_messages_by_user_id($user_id) {

		$query = "
                SELECT * FROM (
                    SELECT * FROM (
                        SELECT to_user_id AS friend_id, chat_message, chat_message_time
                        FROM user_chat WHERE from_user_id = {$user_id}
                        UNION
                        SELECT from_user_id AS friend_id, chat_message, chat_message_time
                        FROM user_chat WHERE to_user_id = {$user_id}
                    ) AS chat
                    ORDER BY chat.friend_id, chat.chat_message_time DESC
                ) AS ordered_chat
                GROUP BY ordered_chat.friend_id
                ORDER BY ordered_chat.chat_message_time DESC";

		$result = $this -> db -> query($query);

		return $result -> num_rows() > 0 ? $result -> result_array() : NULL;
	}

    public function get_unread_messages_count($user_id, $friend_id = NULL) {

        $this -> db -> select('count(*) as unread_count');

        $this -> db -> where('to_user_id', $user_id);

        if (!empty($friend_id)) {
            $this->db->where('from_user_id', $friend_id);
        }

        $this -> db -> where('is_read', '0');

        $result = $this -> db -> get('user_chat');

        return $result -> num_rows() > 0 ? $result -> row() -> unread_count : 0;
    }

    public function mark_user_chats_read_by_user_id_friend_id($user_id, $friend_id) {

        $this -> db -> where("(from_user_id = {$user_id} AND to_user_id = {$friend_id})", null, FALSE);
        $this -> db -> or_where("(from_user_id = {$friend_id} AND to_user_id = {$user_id})", null, FALSE);

        $update_array['is_read'] = 1;

        $this -> db -> update('user_chat', $update_array);
    }
}
