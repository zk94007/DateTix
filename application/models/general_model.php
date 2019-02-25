<?php

class General_model extends CI_Model
{
	private $_table;
	private $_fields;
	public $fields;

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}

	/**
	 * Set table name
	 * @access public
	 * @param  string - sets table_name
	 * @return null
	 * @author Rajnish Savaliya
	 */
	function set_table($table_name)
	{
		$this->_table = $table_name;
		$this->_fields = $this->db->list_fields($this->_table);
		foreach($this->_fields as $field) {
			$this->fields[$field] = "";
		}
	}

	/**
	 * Get record from tables
	 * @access public
	 * @return array()
	 * @author Rajnish Savaliya
	 */
	function get_fields_array()
	{
		return $this->_fields;
	}

	/**
	 * Get record from table
	 * @access public
	 * @param number - sets limit
	 * @param number - sets offset
	 * @param array  - sets order
	 * @author Rajnish Savaliya
	 * @return array()
	 */
	function get($select = array(),$conditions = array(),$order=array(),$limit=NULL,$offset=NULL)
	{
		$this->db->select($select)->from($this->_table)->where($conditions);
		if($order)
		{
			foreach ($order as $key => $value)
			{
				$this->db->order_by($key,$value);
			}
		}
		//$this->db->order_by(key($order),$order[key($order)]);
		if($limit)
		$this->db->limit($limit,$offset);

		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * GET [copy] Get record from table
	 * @access public
	 * @param number - sets limit
	 * @param number - sets offset
	 * @param array  - sets order
	 * @author Rajnish Savaliya
	 * @return stdClass()
	 */
	function get_stdClass($select = array(),$conditions = array(),$order=array(),$limit=NULL,$offset=NULL)
	{
		$this->db->select($select)->from($this->_table)->where($conditions);
		if($order)
		$this->db->order_by(key($order),$order[key($order)]);
		if($limit && $offset)
		$this->db->limit($limit,$offset);

		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * Advance Get Function
	 * @access public
	 * @param number - select values
	 * @param number - sets limit
	 * @param number - sets offset
	 * @param array  - sets order
	 * @param array  - sets groupby
	 * @author Rajnish Savaliya
	 * @return array()
	 */
	function advance_get($select = array(),$conditions = array(),$order=array(),$groupby='',$limit=NULL,$offset=NULL)
	{
		$this->db->select($select)->from($this->_table)->where($conditions);
		if($order)
		$this->db->order_by(key($order),$order[key($order)]);
			
		if($groupby != ''){
		 $this->db->group_by($groupby);
		}

		if($limit && $offset)
			$this->db->limit($limit,$offset);
		
		$query = $this->db->get();
		return $query->result_array();
	}


	/**
	 * Get record by id from table
	 * @access public
	 * @param number - sets limit
	 * @param number - sets offset
	 * @param array  - sets order
	 * @author Rajnish Savaliya
	 * @return array()
	 */

	function get_by_id($id,$order=array("id"=>"ASC"),$limit='1',$offset=NULL)
	{
		$this->db->where("id",$id);
		$this->db->from($this->_table)->order_by(key($order),$order[key($order)])->limit($limit,$offset);
		$query = $this->db->get();
		return $query->result_array();
	}

	/**
	 * Save record in table
	 * @access public
	 * @param array  -
	 * @return insert id
	 * @author Rajnish Savaliya
	 */
	function save($data,$password = NULL,$created = NULL)
	{
		if(!empty($data))
		{
			//if password field exist then
			if($password != NULL)
			{
				$data[$password] = md5($data[$password]);
			}
			if($created != NULL)
			{
				$data[$created] =  date('Y-m-d H:i:s');
			}

			$data = elements($this->_fields,$data);
			$this->db->insert($this->_table, $data);
			return $this->db->insert_id();
		}
		return false;
	}
	
	/**
	 * Save record in table along with Null Values
	 * @access public
	 * @param array  -
	 * @return insert id
	 * @author Rajnish Savaliya
	 */
	function simple_save($data)
	{
		if(!empty($data))
		{
			$this->db->insert($this->_table, $data);
			return $this->db->insert_id();
		}
		return false;
	}
	
	/**
	 * Save batch record in table
	 * @access public
	 * @param array  - all combine data
	 * @return insert id
	 * @author Rajnish Savaliya
	 */
	public function saveBatch($collection){
		$this->db->insert_batch($this->_table, $collection);
		return $this->db->insert_id();
	}

	/**
	 * Update record in table
	 * @access public
	 * @param array  - task data
	 * @param array  - field name & value
	 * @return boolean
	 *  @author Rajnish Savaliya
	 */
	function update($data,$fieldValue = array())
	{
		if(!empty($data) && !empty($fieldValue))
		{
			$this->db->where($fieldValue);
			$this->db->update($this->_table,$data);

			if($this->db->affected_rows() > 0)
			return true;
			else
			return false;
		}
		return false;
	}

	/**
	 * Delete record in table
	 * @access public
	 * @param array  - field name & value
	 * @return boolean
	 *  @author Rajnish Savaliya
	 */

	function delete($fieldValue = array())
	{
		if(!empty($fieldValue))
		{
			$this->db->delete($this->_table,$fieldValue);
			if($this->db->affected_rows() > 0)
			return true;
			else
			return false;
		}
		return false;
	}

	/**
	 * Delete record in table
	 * @access public
	 * @param array  - field name & value
	 * @return boolean
	 *  @author Rajnish Savaliya
	 */

	function delete_multiple($where_in = array(), $fieldName)
	{
		if(!empty($where_in))
		{
			$this->db->where_in($fieldName,$where_in);
			$this->db->delete($this->_table);
			if($this->db->affected_rows() > 0)
			return true;
			else
			return false;
		}
		return false;
	}

	/**
	 * Delete record in table
	 * @access public
	 * @param array  - field name & value
	 * @return boolean
	 *  @author Rajnish Savaliya
	 */
	function update_multiple($data,$where_in = array(), $fieldName)
	{
		if(!empty($where_in))
		{
			$this->db->where_in($fieldName,$where_in);
			$this->db->update($this->_table,$data);
			if($this->db->affected_rows() > 0)
			return true;
			else
			return false;
		}
		return false;
	}

	/**
	 * Get Field or Fields By Id
	 * @access public
	 * @param  string  - field name
	 * @param  number  - field id
	 * @return boolean
	 *  @author Rajnish Savaliya
	 */

	function get_fields($field_names = NULL , $id = NULL)
	{
		if($field_names != NULL && $id != NULL)
		{
			$this->db->select($field_names)->from($this->_table)->where('id',$id);
			$query = $this->db->get();
			$record = $query->result_array();
			if(!empty($record))
			{
				if(count(explode(",", $field_names)) > 1)
				return $record[0];
				else
				return $record[0][$field_names];
			}
			return "";
		}
		return "";
	}

	/**
	 * Join Two Table
	 * @access public
	 * @param array  - result
	 * @return stdClass
	 *  @author Rajnish Savaliya
	 */
	public function singleJoin($parentTable,$childTable,$select,$condition,$where=array()){
		$this->db->select($select);
		$this->db->from($parentTable);
		$this->db->where($where);
		$this->db->join($childTable,$condition);
		return $this->db->get()->result_array();
	}


	/**
	 * Join Two or More Table : mulitple joins with multiple where condition and multiple like condition
	 * @access public
	 * @param array  - result
	 * @return stdClass   - result
	 * @author Rajnish Savaliya
	 */

	public function multijoins($fields,$from,$joins,$where,$ordersby='',$action=NULL,$likes=NULL,$num=NULL,$offset=NULL,$wheretype='where',$groupby='',$wherein = array()){

		$this->db->select($fields);
		if($wheretype == 'where'){
			$this->db->where($where);
		}

		if($wheretype == 'where_in'){
			$this->db->where($where);
		}

		if($wherein)
		{
			$this->db->where_in(key($wherein),$wherein[key($wherein)]);
		}
		if($groupby != ''){
		 $this->db->group_by($groupby);
		}
		foreach($joins as $key => $value){
			$this->db->join($key, $value[0], $value[1]);
		}
		if($likes != NULL){
			foreach($likes as $field =>$like){
				$this->db->like($field, $like);
			}
		}
		if($ordersby != ''){
			$this->db->order_by(''.$ordersby.'');
		}
		if($action == 'count'){
			return	$this->db->get($from)->num_rows();
		}
		elseif($action == 'array'){
			return $this->db->get($from,$num,$offset)->result_array();
		}

		else{
			return $this->db->get($from,$num,$offset)->result();
		}
	}

	public function multijoins_groupby($fields,$from,$joins,$where,$ordersby='',$action=NULL,$groupby=''){

		$this->db->select($fields);
		$this->db->where($where);
		if($groupby != ''){
		 $this->db->group_by($groupby);
		}
		foreach($joins as $key => $value){
			$this->db->join($key, $value[0], $value[1]);
		}
		if($ordersby != ''){
			$this->db->order_by(''.$ordersby.'');
		}
		if($action == 'count'){
			return	$this->db->get($from)->num_rows();
		}
		elseif($action == 'array'){
			return $this->db->get($from)->result_array();
		}
		else{
			return $this->db->get($from)->result();
		}
	}

	/**
	 * Join Two or More Table : mulitple joins with multiple where condition and multiple like condition
	 * @access public
	 * @param array  - result
	 * @return ArrayObject  - result
	 * @author Rajnish Savaliya
	 * Comment by Hannan : this function does not support multiple orderby clauses.
	 */
	public function multijoins_arr($fields,$from,$joins,$where,$custom_where=NULL,$ordersby='',$num=NULL,$offset=NULL,$action='',$wheretype='where',$groupby=''){
		$this->db->select($fields);
		if($wheretype == 'where'){
			$this->db->where($where);
		}
		if($wheretype == 'where_in'){
			/*$field =  implode(",",(array_keys($where)));
		  $this->db->where_in(''.$field.'', $where['p.products_id']);*/
			$this->db->where($where);
		}
		if($groupby != ''){
		 $this->db->group_by($groupby);
		}
		foreach($joins as $key => $value){
			$this->db->join($key, $value[0], $value[1]);
		}
		if($custom_where != NULL){
			$this->db->where($custom_where);
		}
		if($ordersby != ''){
			$this->db->order_by(''.$ordersby.'');
		}
		if($action == 'count'){
			return	$this->db->get($from,$num,$offset)->num_rows();
		}else{
			return $this->db->get($from,$num,$offset)->result_array();
		}
	}

	/**
	 * Function give next/ successor id from calculating ids.
	 * @access public
	 * @param array  - result
	 * @return id
	 * @author Rajnish Savaliya
	 */
	public function getNextId($tableName,$id='id',$alias='')
	{
		if($alias == '')
		{
			$alias = $id;
		}

		$this->db->select_max($id,$alias);
		$query = $this->db->get($tableName);
		$result = $query->result_array();
		return $result['0'][$alias]+1;
	}

	/**
	 * Function check record is exist or not.
	 * @access public
	 * @param array  - result
	 * @return boolean true if have dublicate record and false doen't dublicate record
	 * @author Rajnish Savaliya
	 */
	public function checkDuplicate($condition,$table=''){
		if($table == '')
		$table = $this->_table;

		$query = $this->db->get_where($table,$condition);
		if($query->num_rows()>=1){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Count Number of record from table
	 * @access public
	 * @Optional = table name
	 * @author Rajnish Savaliya
	 * @return array()
	 */
	function count_record($condition,$table='')
	{
		if($table == '')
		$table = $this->_table;

		$query = $this->db->get_where($table,$condition);
		return $query->num_rows();
	}

	/**
	 * Count Number of record from table
	 * @access public
	 * @Optional = table name
	 * @author Rajnish Savaliya
	 * @return array()
	 */
	function custom_get($select,$condition = '')
	{
		$sql = "SELECT ".$select." FROM ".$this->_table;
		if($condition != '')
		$sql .= " Where ".$condition;

		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 * RUN SQL Query
	 * @access public
	 * @Optional = table name
	 * @author Rajnish Savaliya
	 * @return array()
	 */
	function sql_query($sql)
	{
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	/**
	 * Retrive all categories from database with tree structure
	 * @access public
	 * @author Pratik Patel
	 * @modified By Rajnish Savaliya
	 * @return array()
	 */
	public function get_all_categories()
	{
		$refs = array();
		$list = array();
		$sql = "SELECT iid, cat_code,parent_cat_code, category_name FROM category ";
		$result = mysql_query($sql);

		while($data = @mysql_fetch_assoc($result)) {

			$thisref = &$refs[ $data['cat_code'] ];
			$thisref['iid'] = $data['iid'];
			$thisref['cat_code'] = $data['cat_code'];
			//$thisref['parent_cat_code'] = $data['parent_cat_code'];
			$thisref['category_name'] = $data['category_name'];

			if ($data['parent_cat_code'] == 0) {
				$thisref['type'] = 'folder';

				$list[ $data['cat_code'] ] = &$thisref;

			} else {
				$thisref['type'] = 'item';

				$refs[ $data['parent_cat_code'] ]['additionalParameters']['children'][ $data['cat_code'] ] = &$thisref;
			}
		}

		return $list;
	}


	/**
	 * Get a single value from query
	 * @access public
	 * @author Hannan Munshi
	 * @return single value
	 */
	public function getSingleValue($tableName,$fieldName,$condition=array())
	{
		if(!empty($tableName) && !empty($fieldName))
		{
			$query = "SELECT $fieldName FROM $tableName WHERE ";
			if(!empty($condition))
			{
				foreach ($condition as $key => $value)
				{
					$query .= "$key = $value AND ";
				}

				$query = rtrim($query,'AND ');
			}
			$result = $this->db->query($query)->row();

			if(!empty($result))
			return $result->$fieldName;
		}
		return FALSE;

	}
        
        public function rawQuery($SQL,$params=array(),$expects='multiple'){
           $res = $this->db->query($SQL,$params);
           if($expects != 'multiple'){
               return $res->row_array();
           }
           return $res->result_array();
        }
}
