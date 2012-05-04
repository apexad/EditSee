<?php
/* editsee database abstration class */
require_once("includes/database/editsee_DatabaseResult.class.php");

class editsee_Database {
	private $type;

	private $host;
	private $user;
	private $password;
	private $database;
	private $table_prefix;

	private $db; //real db connection

	public function editsee_Database($type, $host, $user, $password, $database,$table_prefix) {
		$this->type = $type;
		$this->table_prefix = $table_prefix;
		switch ($this->type) {
			case 'mssql':
				$this->db = @mssql_connect($host, $user, $password) or die('unable to connect to database');
				@mssql_select_db($database,$this->db);
			break;
			case 'sqlsrv':
				$connectionInfo = array("UID" => $user, "PWD" => $password, "Database"=> $database);
				$this->db = sqlsrv_connect($host, $connectionInfo) or die('unable to connect to database');
			break;
			default:
			case 'mysql':
				$this->db = @mysql_connect($host, $user, $password) or die('unable to connect to database');
				@mysql_select_db($database,$this->db);
			break;
		}
	}
	public function connected() {
		return $this->db;
	}
	public function get_table_prefix() {
		return $this->table_prefix;
	}
	public function _query($sql) {
		switch($this->type) {
			case 'mssql':
				return new editsee_DatabaseResult($this->type,mssql_query($sql,$this->db));
			break;
			case 'sqlsrv':
				return new editsee_DatabaseResult($this->type,sqlsrv_query($this->db,$sql));
			break;
			default:
			case 'mysql':
				echo mysql_error();
				return new editsee_DatabaseResult($this->type,mysql_query($sql,$this->db));
			break;
		}
	}
	public function _escape_string($string_to_escape) {
		switch ($this->type) {
			case 'mssql':
			case 'sqlsrv':
				if(is_numeric($string_to_escape)) { return $string_to_escape; }
				else {
						$unpacked = unpack('H*hex', $string_to_escape);
						return '0x' . $unpacked['hex'];
				}
			break;
			default:
			case 'mysql':
				return mysql_real_escape_string($string_to_escape,$this->db);
			break;
		}
	}
	public function _alter($table,$column,$newtype) {
			switch ($this->type) {
			case 'mssql':
			case 'sqlsrv':
				return $this->_query("ALTER TABLE `".$this->table_prefix.$table."` ALTER COLUMN `".$column."` ".$newtype);
			break;
			default:
			case 'mysql':
				return $this->_query("ALTER TABLE  `".$this->table_prefix.$table."` 
									  CHANGE  `".$column."`  `".$column."` ".$newtype);
			break;
		}
	}
	public function _insert_user($user,$role,$password,$email) {
		return $this->_query("insert into ".$this->table_prefix."user(username,role,password,email) values(
									'".$this->_escape_string($user)."'
									,'".$role."'
									,md5('".$this->_escape_string($password)."')
									,'".$this->_escape_string($email)."')");
	}
	public function _un_delete($id,$type) {
		switch ($type) {
			case 'link':
				$id_name = 'link_id';
			break;
			case 'post':
			case 'page':
				$id_name ='id';
			break;
		}
		return $this->_query("update `".$this->table_prefix.$type."` set deleted=0 where ".$id_name."='".$id."'");
	}
	public function _insert_post($id,$title,$content,$category,$urltag,$type,$date,$in_nav,$page_order_position,$page_order_after,$draft='0') {
		$id = $this->_escape_string($id);
		$title = $this->_escape_string($title);
		$content = $this->_escape_string($content);
		$category = $this->_escape_string($category);
		$urltag = $this->_escape_string(str_replace(array(' ',"'",'"','/','.',',','&'),'-',$urltag));
		$date = $this->_escape_string($date);
		
		$category_query = $this->_query("select tag_id from ".$this->table_prefix."tags where tag='".$category."' and type='cat'");
		if ($category_query->_num_rows() == 1) {
			$category_id = $category_query->_result(0);
		}
		else {
			$category_id=0; //general category
		}
		
		switch($page_order_position) {
			case 'begin':
				$this->_query("update ".$this->table_prefix."post set page_order=(page_order+1) where type='page'");
				$query = $this->_query("select min(page_order) from ".$this->table_prefix."post where type='page'");
				$page_order = $query->_result(0);
			break;
			case 'end':
					$query = $this->_query("select max(page_order) from ".$this->table_prefix."post where type='page'");
					$page_order = ($query->_result(0))+1;

			break;
			case 'after':
				$this->_query("update ".$this->table_prefix."post set page_order=(page_order+1) where page_order > '".$page_order_after."' and type='page'");
				$page_order = $page_order_after+1;
			break;
		}
		/*echo "insert into ".$this->table_prefix."post(id,user_id,title,content,urltag,type,date_entered,in_nav,page_order,draft) 
					values('".$id."','".$_SESSION['user_id']."','".$title."','".$content."','".$urltag."','".$type."','".$date."','".$in_nav."','".$page_order."','".$draft."')";
		*/
		$main_insert = $this->_query("insert into ".$this->table_prefix."post(id,user_id,title,content,urltag,type,date_entered,in_nav,page_order,draft) 
					values('".$id."','".$_SESSION['user_id']."','".$title."','".$content."','".$urltag."','".$type."','".$date."','".$in_nav."','".$page_order."','".$draft."')
					on duplicate key update 
					title='".$title."',content='".$content."', urltag='".$urltag."',date_entered='".$date."', in_nav='".$in_nav."',page_order='".$page_order."',draft='".$draft."'");
		if ($id == 'new') {
			$id = $main_insert->_insert_id();
		}
		$this->_query("delete from ".$this->table_prefix."post_tags where post_id='".$id."' and type='cat'");
		$this->_query("insert into ".$this->table_prefix."post_tags(post_id,tag_id,type) 
		values('".$id."','".$category_id."','cat')");
		return $id;
	}
	public function _insert_link($link_url,$link_title,$link_nofollow,$link_target) {
		$link_url = $this->_escape_string($link_url);
		$link_title = $this->_escape_string($link_title);
		$link_nofollow = str_word_count($link_nofollow);
		$link_target = $this->_escape_string($link_target);
		$query = $this->_query("select max(link_order) from ".$this->table_prefix."links");
		$max_link_order = $query->_result(0);
		return $this->_query("insert into ".$this->table_prefix."links(link_order,url,title,nofollow,target)
					values('".($max_link_order+1)."','".$link_url."','".$link_title."','".$link_nofollow."','".$link_target."')");
	}
	public function insert_comment($post_id,$name,$email,$text) {
		$name = $this->_escape_string($name);
		$email = $this->_escape_string($email);
		$text = str_replace(array('<','>','&'),array('&lt;','&gt;','&amp;'),$this->_escape_string($text));
		
		return $this->_query("insert into ".$this->table_prefix."comments(name,linked_post_id,email,comment) 
								values('".$name."','".$post_id."','".$email."','".$text."')");
	}
	public function _insert_category($category) {
		$category = $this->_escape_string($category);
		return $this->_query("insert into ".$this->table_prefix."tags(tag,type) values('".$category."','cat')");
	}
	public function _insert_custom_section($section,$label,$data) {
		$section = $this->_escape_string($section);
		$label = $this->_escape_string($label);
		$data = $this->_escape_string($data);
		return $this->_query("insert into ".$this->table_prefix."custom(section,label,data) values('".$section."','".$label."','".$data."')
								on duplicate key update data='".$data."'");
	}
	public static function db_date_add($param,$length,$time_unit) {
		/* this function generates sql for a 'date addition'
		   $param = database field name or a proper date like 2011-05-04
		   $length = how much to add
		  $time_unit = second, minute, hour, day, week, month, etc..
		*/
		switch($this->type) {
			case 'mssql':
			case 'sqlsrv':
				return 'dateadd('.$time_unit.','.$length.','.$param.')';
			break;
			default:
			case 'mysql':
				return 'date_add('.$param.',interval '.$length.' '.$time_unit.')';
			break;
		}
	}
	public function _limit_query($table,$table_id,$field_list,$start_limit,$limit,$where,$orderby) {
		switch ($this->type){
			case 'mssql':
			case 'sqlsrv':
				return $this->_query("select top ".$start_limit+$limit."
										from ".$table." 
										where ".$where." 
										and (".$table_id." not in (select top ".$start_limit." ".$table_id."from ".$table."))
										order by ".$orderby);
			break;
			default:
			case 'mysql':
				return $this->_query("select ".$field_list." 
										from ".$table." where ".$where." order by ".$orderby."
										limit ".$start_limit.",".$limit);
		}
	}
	public function now() {
		switch($this->type) {
			case 'mssql':
			case 'sqlsrv':
				return 'GETDATE()';
			break;
			case 'mysql':
			default:
				return 'NOW()';
		}
	}
	public function _delete_post($post_id) {
		return $this->_query("update ".$this->table_prefix."post set deleted='1',date_deleted=".$this->now()." where id='".$post_id."'");
	}
	public function _delete_link($link_id) {
		return $this->_query("update ".$this->table_prefix."links set deleted='1',date_deleted=".$this->now()." where link_id='".$link_id."'");
	}
	public function _update_user($username,$email) {
		return $this->_query("update ".$this->table_prefix."user set username='".$this->_escape_string($username)."',email='".$this->_escape_string($email)."' where user_id='".$_SESSION['user_id']."'");
	}
}
