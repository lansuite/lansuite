<?php
/*
 * Created on 11.04.2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 	class updateStatement
	{
		var $tablename;
		var $params;
		var $values;
		
		/*
		 * Constructor for the Update Statement
		 */
		function UpdateStatement($tablename) {
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
		 * $condition e.g.
		 *  .execute('(colum1 = 3)')
		 *  .execute('(colum1 = 3) AND (colum2 = 44)')
		 */
		function execute($condition){
			global $db;
			$sql = 'UPDATE `' . $this->tablename . '` SET ';
			
			if (count($this->params) == 0)
				echo ('To less values - class_db_updateStatement<br>');
			else
			{
				foreach ($this->params as $id => $value) {
					$sql .= '`' . $value . '`= ';
				
					$sql .= $this->values[$id] . ', ';	
				}
				
				$sql = substr($sql, 0, strlen($sql)-2) . ' WHERE '. $condition . ';';
				
				return $db->query($sql);
			}
		}
		
		
	}
 
?>
