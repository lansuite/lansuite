<?php
/*
 * Created on 07.04.2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class InsertStatement
{
	private $tablename;
	private $params;
	private $valueslist;
	private $values;
		
	/*
	 * Constructor for the Insert Statement
	 */
	public function insertStatement($tablename) {
		$this->tablename = $tablename;
		$params = array();
		$valueslist = array();
		$values = array();
	}
		
	/*
	 * Adds a Parameter to the insert statement
	 * $datatype e.g.
	 *  's'     String
	 *  'i'     Integer
	 * $param The Columnname in the table
	 * $value The value for the column
	 */
	public function addParameter($datatyp, $param, $value){
		if ($value != '') {
			if ($datatype == '') $datatype = 'plain';
				
			$this->params[] .= $param;
			$this->values[] .= '%' . $datatyp . '%';
			$this->valuelist[] .= $value;
		}
	}

	/*
	 * Executes the Inserstatement in the DB and returns the $query_id
	 */
	public function execute(){
		global $db;
			
		$sql = 'INSERT INTO `' . $this->tablename . '` (';
		foreach ($this->params as $value) $sql .= '`' . $value . '`, ';
			
		$sql = substr($sql, 0, strlen($sql)-2) . ') VALUES (';
		foreach ($this->values as $value) $sql .= $value . ', ';

		$sql = substr($sql, 0, strlen($sql)-2) . ');';
		return $db->qry($sql , $this->valuelist);
	}
}
//
// Please make a history at the end of file of your changes !!
//

/* HISTORY
 * 06. 2. 2009 : Created the file.
 */
?>
