<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_translation extends CI_Model {

    /**
     * Language table name
     * @var string
     */
    private $_table = 'display_language';

    /**
     * Language table primary key field name
     * @var string
     */
    private $_table_id = 'display_language_id';

    /**
     * Language table language code field name
     * @var string
     */
    private $_table_code = 'language_code';

    public function __construct()
    {
        parent::__construct();
		
		if(isset($_GET['lang_id']) && $_GET['lang_id'] != "")
		{
			$this -> session -> set_userdata('sess_language_id',$_GET['lang_id']);
		}
		
        if (!$this->session->userdata('sess_language_id')) {
            $this->setLang();
        }
    }

    /**
     * Set language
	 * basically if lang_id is NULL (first time user visit the website), then go through this checking of the shortcut_url
	 * if shortcut_url doesn't match any of the ones in the hardcoded list then set lang_id to 1
	 * if shortcut_url is "", then go through the old language_domain_mapping array to check domain
	 * so here is the order of checking:
		1. if shortcut_url != "", then see if it's in hard-coded list, and set lang_id accordingly
		2. if it's not in hard-coded list, then set lang_id=1
		3. if it's "", then go through language_domain_mapping array
		4. if it's still not found, then set lang_id to 1
	 * 
     * @param int $id primary key for language table
     */
    public function setLang($id = "")
    {
    	if($id == "")
		{
			$city_mapping['939'] = 'atlanta';
			$city_mapping['971'] = 'baltimore';
			$city_mapping['105'] = 'beijing';
			$city_mapping['972'] = 'boston';
			$city_mapping['133'] = 'changsha';
			$city_mapping['162'] = 'chengdu';
			$city_mapping['946'] = 'chicago';
			$city_mapping['106'] = 'chongqing';
			$city_mapping['149'] = 'dalian';
			$city_mapping['1057'] = 'dallas';
			$city_mapping['906'] = 'denver';
			$city_mapping['978'] = 'detroit';
			$city_mapping['107'] = 'fuzhou';
			$city_mapping['112'] = 'guangzhou';
			$city_mapping['260'] = 'hongkong';
			$city_mapping['926'] = 'miami';
			$city_mapping['92'] = 'montreal';
			$city_mapping['140'] = 'nanjing';
			$city_mapping['1007'] = 'nyc';
			$city_mapping['927'] = 'orlando';
			$city_mapping['89'] = 'ottawa';
			$city_mapping['1118'] = 'paloalto';
			$city_mapping['1037'] = 'philadelphia';
			$city_mapping['1038'] = 'pittsburgh';
			$city_mapping['1008'] = 'rochester';
			$city_mapping['884'] = 'sandiego';
			$city_mapping['885'] = 'sanfrancisco';
			$city_mapping['886'] = 'sanjose';
			$city_mapping['159'] = 'shanghai';
			$city_mapping['151'] = 'shenyang';
			$city_mapping['114'] = 'shenzhen';
			$city_mapping['613'] = 'singapore';
			$city_mapping['894'] = 'sunnyvale';
			$city_mapping['141'] = 'suzhou';
			$city_mapping['167'] = 'tianjin';
			$city_mapping['90'] = 'toronto';
			$city_mapping['1135'] = 'waterloo';
			$city_mapping['129'] = 'wuhan';
			$city_mapping['154'] = 'xian';
			$city_mapping['109'] = 'xiamen';
			
			$shortcut_url = $this->uri->segment(1);
			if($city_id = array_search($shortcut_url, $city_mapping))
			{
				$this -> session -> set_userdata('sess_city_id',$city_id);
	 		}
	 		
				//1. if shortcut_url != "", then see if it's in hard-coded list, and set lang_id accordingly
				if($shortcut_url != "")
				{
					if($shortcut_url == 'hongkong' || $shortcut_url == 'taipei')
					{
						//$this -> session -> set_userdata('sess_language_id', 2);
						$this -> session -> set_userdata('sess_language_id', 1);
					}
					elseif($shortcut_url == 'beijing' || $shortcut_url == 'shanghai')
					{
						$this -> session -> set_userdata('sess_language_id', 3);
					}
					else {
						//2. if it's not in hard-coded list, then set lang_id=1
						$this -> session -> set_userdata('sess_language_id', 1);
					}
				}
				else{
					
					//3. if it's "", then go through language_domain_mapping array
					
					//set language_id => domain name
					$language_domain_mapping['1'] = 'datetix.com';
					$language_domain_mapping['1'] = 'datetix.hk';
					
					if($lang_id = array_search($_SERVER['HTTP_HOST'], $language_domain_mapping))
					{
						$this -> session -> set_userdata('sess_language_id',$lang_id);
			 		}
					else {
						//4. if it's still not found, then set lang_id to 1
						$this -> session -> set_userdata('sess_language_id', 1);
					}
				}	
			
				
		}	
		else {
			//if id != "" then get data from database.
			$lang_id = $id;
	        $this->db->where($this->_table_id, $lang_id);
	        $lang = $this->db->get($this->_table)->row();
	        $this->session->set_userdata('sess_language_id', $lang->{$this->_table_id});
	        $this->session->set_userdata('sess_language_code', $lang->{$this->_table_code});	
		}
    }

    public function get_all_phrases() {
        $query = "SELECT * FROM phrase ";
        $query = $this->db->query($query);
        foreach ($query->result() as $row) {
            $data[] = $row;
        }
        if (!empty($data)) {
            return $data;
        }
    }

    public function google_translate_new($language_id, $language_code) {
        $phrases = $this->get_all_phrases();
        if (!empty($phrases)) {
            foreach ($phrases as $phrase_row) {
                $phrase_id = $phrase_row->phrase_id;
                if (!$this->isset_translation($phrase_id, $language_id)) {
                    $translation = $this->google_translate($phrase_row->phrase, strtolower($language_code));
                    $insert_array = array("phrase_id" => $phrase_id, "language_id" => $language_id, "translation" => $translation);
                    $this->db->insert('phrase_translation', $insert_array);

                    // clear cache if new phrase is added
                    // $this->db->cache_delete_all();
                }
            }
        }
    }

    public function google_translate_all($language_id, $language_code) {
        $phrases = $this->get_all_phrases();
        if (!empty($phrases)) {
            foreach ($phrases as $phrase_row) {
                $phrase_id = $phrase_row->phrase_id;
                if (!$this->is_approved_translation($phrase_id, $language_id)) {
                    $translation = $this->google_translate($phrase_row->phrase, strtolower($language_code));
                    $this->update_translation($phrase_id, $language_id, $translation);
                }
            }
        }
    }

    public function isset_translation($phrase_id, $language_id) {
        $this->db->where(array("phrase_id" => $phrase_id, "language_id" => $language_id));
        $query = $this->db->get('phrase_translation');
        if ($query->num_rows() < 1)
            return false;
        else
            return true;
    }

    public function is_approved_translation($phrase_id, $language_id) {
        $this->db->where(array("phrase_id" => $phrase_id, "language_id" => $language_id, "approved" => '1'));
        $query = $this->db->get('phrase_translation');
        if ($query->num_rows() < 1)
            return false;
        else
            return true;
    }

    public function google_translate($text, $to_lang = 'de') {
        /* search for translation in google translate */
        // load google translate api
        $from_lang = 'en';
        $this->load->library('Google_Translate_API', array(), 'gTranslate');
        $target_text = $this->gTranslate->translate($text, $from_lang, $to_lang);
        return $target_text;
    }

    public function update_translation($phrase_id, $language_id, $translation) {
        //if (!$this->is_approved_translation($phrase_id, $language_id)) {
            if ($this->isset_translation($phrase_id, $language_id)) {
                $update_array = array("translation" => $translation);
                $update_cond  = array("phrase_id" => $phrase_id, "language_id" => $language_id);
                $this->db->update("phrase_translation", $update_array, $update_cond);
            } else {
                $insert_array = array("phrase_id" => $phrase_id, "language_id" => $language_id, "translation" => $translation);
                $this->db->insert('phrase_translation', $insert_array);
            }

            // clear cache if new phrase is added
            // $this->db->cache_delete_all();
       // }
    }

    public function get_translation($language_id, $phrase) {
        $this->db->where(array("phrase" => $phrase));
        $query = $this->db->get('phrase');

        $translation_exists = 1;
        if ($query->num_rows() < 1) {
            $insert_phrase = array("phrase" => $phrase);
            $this->db->insert('phrase', $insert_phrase);
            $phrase_id = $this->db->insert_id();
            $translation_exists = 0;
        } else {
            foreach ($query->result() as $row) {
                $phrase_id = $row->phrase_id;
            }

            // enable query caching for faster static translation
            // $this->db->cache_on();

            $this->db->where(array("phrase_id" => $phrase_id, "language_id" => $language_id));
            $query = $this->db->get('phrase_translation');
            if ($query->num_rows() < 1)
                $translation_exists = 0;
            else {
                foreach ($query->result() as $row) {
                    $translation = $row->translation;
                }
            }

            // disable query caching
            // $this->db->cache_off();
        }
        if (!$translation_exists) {
            $translation = $this->google_translate($phrase, $this->session->userdata('sess_language_code'));
            $insert_array = array("phrase_id" => $phrase_id, "language_id" => $language_id, "translation" => $translation);
            $this->db->insert('phrase_translation', $insert_array);

            // clear cache if new phrase is added
            // $this->db->cache_delete_all();
        }
        return $translation;
    }

    public function get_phrases_with_limit($language_id, $search, $count = '', $limit = '', $start = '', $not_approved = '') {
        $condition = "";
        $limit_cond = "";
        if ($not_approved)
            $condition = " AND  phrase_translation.approved='0' ";
        if ($search) {
            $condition = " AND (phrase LIKE '%$search%' OR translation LIKE '%$search%' ) ";
        }
        if ($limit > 0) {
            $limit_cond = " LIMIT $start,$limit ";
        }
        $query = "SELECT phrase.*,phrase_translation.language_id,phrase_translation.translation,phrase_translation.approved FROM phrase 
                        LEFT JOIN phrase_translation ON phrase_translation.phrase_id = phrase.phrase_id 
                        WHERE 1 
                        AND ( phrase_translation.language_id = '$language_id' OR phrase_translation.language_id is null) 
                        $condition $limit_cond";
        $query = $this->db->query($query);
        if ($count == 1)
            return $query->num_rows();

        foreach ($query->result() as $row) {
            $data[] = $row;
        }
        if (!empty($data)) {
            return $data;
        }
    }

    public function approve_translation($phrase_id, $language_id) {
        $update_array = array("approved" => 1);
        $update_cond = array("phrase_id" => $phrase_id, "language_id" => $language_id);
        $this->db->update("phrase_translation", $update_array, $update_cond);
    }

    public function remove_all_phrases($language_id) {
        $this->db->delete('phrase_translation', array("language_id" => $language_id));

        // clear cache if new phrase is added
        // $this->db->cache_delete_all();
    }

    public function save_all_phrases() {
        $language_id = $this->input->post("language_id");
        $translation_array = $this->input->post("translation");
        if (!empty($translation_array)) {
            foreach ($translation_array as $phrase_id => $translation) {
                $this->update_translation($phrase_id, $language_id, $translation);
            }
        }
    }
    public function get_oldest_phrases($month,$offset,$limit)
    {
        $limit_cond ;
        $list = array();
        $limit_cond = " LIMIT $offset,$limit ";
        $time_1month_before = mktime(0,0,0,date("m")-$month,date("d"),date("Y"));
        $query = "SELECT * FROM phrase WHERE last_fetch_time<$time_1month_before ";
        $result = $this->db->query($query);
        $count = $result->num_rows();
        $query = $query.$limit_cond;
        $result = $this->db->query($query);
        $list = $result->result_array();
        return array("count"=>$count,"data"=>$list);
    }

    public function delete_oldest_phrase(){
        $phrase_array = $this->input->post("phrase_id");
        foreach ($phrase_array as $phrase_id)
        {
            $this->db->delete("phrase",array("phrase_id"=>$phrase_id));
            $this->db->delete("phrase_translation",array("phrase_id"=>$phrase_id));
        }
    }
}
