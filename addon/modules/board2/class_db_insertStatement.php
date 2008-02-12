<?php
/*
 * Created on 07.04.2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 	class insertStatement
	{
		var $tablename;
		var $params;
		var $values;
		
		/*
		 * Constructor for the Insert Statement
		 */
		function insertStatement($tablename) {
			$this->tablename = $tablename;
			$params = array();
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
		function addParameter($datatyp, $param, $value){
			if ($value != '') {
				if ('s' == $datatyp) {
					$this->params[] .= $param;
					$this->values[] .= '\'' . $value . '\'';
				} else if ('i' == $datatyp) {
					$this->params[] .= $param;
					$this->values[] .= $value;
				} else {
					$this->params[] .= $param;
					$this->values[] .= $value;
				}
			}
		}
		
		/*
		 * Executes the Inserstatement in the DB and returns the $query_id
		 */
		function execute(){
			global $db;
			$sql = 'INSERT INTO `' . $this->tablename . '` (';
			
			foreach ($this->params as $value)
				$sql .= '`' . $value . '`, ';
			
			$sql = substr($sql, 0, strlen($sql)-2) . ') VALUES (';
		
			foreach ($this->values as $value)
				$sql .= $value . ', ';	
				
			$sql = substr($sql, 0, strlen($sql)-2) . ');';

			return $db->query($sql);
		}
		
		
	}
 
?>
