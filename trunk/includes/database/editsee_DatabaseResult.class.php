<?php
/* editsee DatabaseResult class */

class editsee_DatabaseResult {
	private $type;
	public $result;

	public function editsee_DatabaseResult($type,$result) {
		//we really should add some error checking here, but that's beta 2
		$this->type = $type;
		$this->result = $result;
	}

	public function getType() {
		return $this->type;
	}

	//_fetch_assoc, _fetch_row, _fetch_result, 
	public function _fetch_assoc() {
		echo mysql_error();
		switch ($this->type) {
			case 'mssql':
				return mssql_fetch_assoc($this->result);
			break;
			case 'sqlsrv':
				return sqlsrv_fetch_array($this->result,SQLSRV_FETCH_ASSOC);
			break;
			default:
			case 'mysql':
				return mysql_fetch_assoc($this->result);
			break;
		}
	}

	public function _fetch_row() {
		switch ($this->type) {
			case 'mssql':
				return mssql_fetch_row($this->result);
			break;
			case 'sqlsrv':
				return sqlsrv_fetch_array($this->result,SQLSRV_FETCH_NUMERIC);
			break;
			default:
			case 'mysql':
				return mysql_fetch_row($this->result);
			break;
		}
	}

	public function _result($row_number) {
		switch($this->type) {
			case 'mssql':
				return mssql_result($this->result,$rownumber);
			break;
			case 'sqlsrv':
				$result = sqlsrv_get_field($this->result,$rownumber);
				return $result[0];
			break;
			default:
			case 'mysql':
				return mysql_result($this->result,$row_number);
			break;
		}
	}

	public function _num_rows() {
		switch($this->type) {
			case 'mssql':
				return mssql_num_rows($this->result);
			break;
			case 'sqlsrv':
				return sqlsrv_num_rows($this->result);
			break;
			default:
			case 'mysql':
				return mysql_num_rows($this->result);
			break;
		}
	}

	public function _error() {
		switch($this->type) {
			case 'mssql':
				return mssql_get_last_message();
			break;
			case 'sqlsrv':
				return sqlsrv_errors();
			break;
			default:
			case 'mysql':
				return mysql_error();
			break;
		}
	}
	public function _affected_rows() {
		switch($this->type) {
			case 'mssql':
				return mssql_rows_affected();
			break;
			case 'sqlsrv':
				return sqlsrv_rows_affected();
			break;
			default:
			case 'mysql':
				return mysql_affected_rows();
			break;
		}
	}
	public function _insert_id() {
		switch ($this->type) {
			case 'mssql':
				//I think this is right!? un-tested
				return mssql_result(mssql_query("select @@identity"),0);
			break;
			case 'sqlsrv':
				//same here
				return sqlsrv_get_field(sqlsrv_query("select @@identity"),0);
			default:
			case 'mysql':
				return mysql_insert_id();
			break;
		}
	}
}
?>
